<?php

namespace Simflex\Core\Cron;

use Exception;
use ReflectionException;
use ReflectionMethod;
use Simflex\Core\DI\DIException;
use Simflex\Core\DI\Injector;
use Simflex\Core\Log;
use Simflex\Core\Models\Cron as CronModel;
use Simflex\Core\Models\CronLog;
use Simflex\Core\Time;
use Throwable;

class Cron
{
    /**
     * @param string|null $jobKey Запустить сейчас только это задание (ключ в provider/cron.php), независимо от
     *                            расписания и активности. При null - все активные задания по расписанию
     */
    public function __construct(protected ?string $jobKey = null)
    {
    }

    /**
     * Выполняет задания cron
     * @throws Exception
     */
    public function execute(): void
    {
        CronRegistry::sync();

        if ($this->jobKey !== null) {
            $job = CronRegistry::job($this->jobKey);
            if (!$job) {
                throw new Exception("Cron job {$this->jobKey} is not declared in provider/cron.php");
            }
            $this->runAndLog($this->jobKey, $job);
            return;
        }

        foreach (CronModel::find(['active' => 1]) as $row) {
            $job = CronRegistry::job($row->job);
            if ($job && $this->testTiming($row->timing)) {
                $this->runAndLog($row->job, $job);
            }
        }
    }

    protected function runAndLog(string $key, CronJob $job): void
    {
        try {
            $this->runJob($job);
            $result = 'ok';
        } catch (Throwable $ex) {
            Log::error('Cron job {job} failed: {error}', ['job' => $key, 'error' => $ex->getMessage()]);
            $result = 'failed (' . $ex->getMessage() . ')';
        }

        CronLog::insertStatic([
            'job' => $key,
            'datetime' => Time::mysql(time()),
            'result' => mb_substr($result, 0, 2047),
        ]);
    }

    /**
     * Запускает задание cron
     * @throws DIException
     * @throws ReflectionException
     */
    protected function runJob(CronJob $job): void
    {
        [$class, $action] = $job->handler;
        $method = new ReflectionMethod($class, $action);

        if ($method->isStatic()) {
            $method->invoke(null, ...$job->params);
            return;
        }

        $instance = new $class(...Injector::resolveClass($class));
        $method->invoke($instance, ...$job->params);
    }

    /**
     * Проверяет, наступило ли время по расписанию
     * @param string $timing Строка расписания
     * @return bool true, если время наступило
     */
    protected function testTiming(string $timing): bool
    {
        // разбираем расписание
        $time = explode(' ', $timing);
        if (count($time) != 5) {
            return false;
        }

        // получаем текущее время
        $now = getdate();
        $now = [
            $now['minutes'],
            $now['hours'],
            $now['mday'],
            $now['mon'],
            $now['wday'],
        ];

        // проверяем расписание
        foreach ($time as $i => $t) {
            // заменяем * на текущее время
            if ($t == '*') {
                $t = $now[$i];
            }

            $delim = 0;
            $matches = false;

            // проверяем, есть ли в расписании разделитель
            if (str_contains($t, '/')) {
                $range = explode('/', $t);
                $t = $range[0];
                $delim = (int)$range[1];
            }

            $vals = explode(',', $t);
            foreach ($vals as $val) {
                // проверяем, есть ли диапазон
                if (str_contains($val, '-')) {
                    $range = explode('-', $val);
                    if ($range[0] <= $now[$i] && $now[$i] <= $range[1]) {
                        $val = $now[$i];
                    }
                }

                // проверяем совпадение расписания
                if ($matches = (int)$val == (int)$now[$i] && (!$delim || !((int)$val % $delim))) {
                    break;
                }
            }

            $time[$i] = $matches ? $now[$i] : '-';
        }

        // проверяем, совпадает ли преобразованное расписание с текущим временем
        return implode(' ', $time) == implode(' ', $now);
    }
}
