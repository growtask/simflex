<?php

namespace Simflex\Core\Cron;

use Exception;
use ReflectionClass;
use ReflectionException;
use Simflex\Core\DI\DIException;
use Simflex\Core\DI\Injector;
use Simflex\Core\Log;
use Simflex\Core\Models\Cron;
use Simflex\Core\Models\CronLog;
use Simflex\Core\Time;

class Cron
{
    /**
     * @var array|Cron[]
     */
    protected array $jobs = [];

    /**
     * CronBootstrap constructor.
     * @param int|null $jobId
     * @throws Exception
     */
    public function __construct(protected ?int $jobId)
    {
        $where = $jobId ? ['id' => $jobId] : [];
        $this->jobs = Cron::find(array_merge($where, ['active' => 1]));
    }

    /**
     * Execute cron jobs
     * @return void
     */
    public function execute(): void
    {
        foreach ($this->jobs as $job) {
            try {
                $this->runJob($job);
                CronLog::insertStatic([
                    'cron_id' => $job->id,
                    'datetime' => Time::mysql(time()),
                    'result' => 'ok',
                ]);
            } catch (Exception $ex) {
                Log::error('Cron job {jobId} failed: {error}', ['jobId' => $job->id, 'error' => $ex->getMessage()]);
                CronLog::insertStatic([
                    'cron_id' => $job->id,
                    'datetime' => Time::mysql(time()),
                    'result' => 'failed (' . $ex->getMessage() . ')',
                ]);
            }
        }

        // delete old logs
        if ($this->testTiming('30 0 * * *')) {
            CronLog::bulkDelete('adddate(datetime, interval 2 month) < now()');
        }
    }

    /**
     * Run a cron job
     * @param Cron $job Cron job
     * @return void
     * @throws DIException
     * @throws ReflectionException
     */
    protected function runJob(Cron $job): void
    {
        if (!$this->testTiming($job->timing)) {
            return;
        }

        $class = new ReflectionClass($job->class_fqn);
        $method = $class->getMethod($job->action);

        if ($method->isStatic()) {
            $method->invoke(null, ...Injector::resolve($method, $job->cparams));
            return;
        }

        $args = [];
        if ($ctor = $class->getConstructor()) {
            $args = Injector::resolve($ctor);
        }

        $instance = $class->newInstance(...$args);
        $method->invoke($instance, $job->cparams);
    }

    /**
     * Test if timing has passed
     * @param string $timing Timing string
     * @return bool True if timing has passed
     */
    protected function testTiming(string $timing): bool
    {
        // parse timing
        $time = explode(' ', $timing);
        if (count($time) != 5) {
            return false;
        }

        // get current time
        $now = getdate();
        $now = [
            $now['minutes'],
            $now['hours'],
            $now['mday'],
            $now['mon'],
            $now['wday'],
        ];

        // check timing
        foreach ($time as $i => $t) {
            // replace * with current time
            if ($t == '*') {
                $t = $now[$i];
            }

            $delim = 0;
            $matches = false;

            // check if timing has a delimiter
            if (str_contains($t, '/')) {
                $range = explode('/', $t);
                $t = $range[0];
                $delim = (int)$range[1];
            }

            $vals = explode(',', $t);
            foreach ($vals as $val) {
                // check if there is a range
                if (str_contains($val, '-')) {
                    $range = explode('-', $val);
                    if ($range[0] <= $now[$i] && $now[$i] <= $range[1]) {
                        $val = $now[$i];
                    }
                }

                // check if timing matches
                if ($matches = (int)$val == (int)$now[$i] && (!$delim || !((int)$val % $delim))) {
                    break;
                }
            }

            $time[$i] = $matches ? $now[$i] : '-';
        }

        // check if converted timing matches current time
        return implode(' ', $time) == implode(' ', $now);
    }
}