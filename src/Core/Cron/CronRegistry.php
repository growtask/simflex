<?php

namespace Simflex\Core\Cron;

use Simflex\Core\Models\Cron as CronModel;

/**
 * Cron jobs declared in code: the app's provider/cron.php returns job key => CronJob.
 * The cron table keeps only the settings editable in the admin panel (active, timing), one row per job key.
 */
class CronRegistry
{
    /** @var array<string, CronJob>|null */
    protected static ?array $jobs = null;

    /**
     * @return array<string, CronJob> job key => job
     */
    public static function jobs(): array
    {
        if (static::$jobs === null) {
            $file = SF_ROOT_PATH . '/provider/cron.php';
            $data = is_file($file) ? require $file : [];

            static::$jobs = [];
            foreach (is_array($data) ? $data : [] as $key => $job) {
                if (is_string($key) && $job instanceof CronJob) {
                    static::$jobs[$key] = $job;
                }
            }
        }

        return static::$jobs;
    }

    public static function job(string $key): ?CronJob
    {
        return static::jobs()[$key] ?? null;
    }

    /**
     * Brings the cron table in line with the code: adds the rows of new jobs with their defaults,
     * deletes the rows of the jobs removed from the code. The settings of the existing rows are kept.
     */
    public static function sync(): void
    {
        $jobs = static::jobs();
        $existing = [];
        foreach (CronModel::all() as $row) {
            if (isset($jobs[$row->job])) {
                $existing[$row->job] = true;
            } else {
                $row->delete();
            }
        }

        foreach ($jobs as $key => $job) {
            if (!isset($existing[$key])) {
                CronModel::insertStatic([
                    'job' => $key,
                    'active' => (int)$job->active,
                    'timing' => $job->timing,
                ]);
            }
        }
    }

    public static function reset(): void
    {
        static::$jobs = null;
    }
}
