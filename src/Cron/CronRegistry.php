<?php

namespace Simflex\Core\Cron;

use Simflex\Core\System\Container;
use Simflex\Core\DB\DB;
use Simflex\Core\System\ExtensionConfig;
use Simflex\Core\Models\Cron as CronModel;

/**
 * Задания cron, объявленные в коде: provider/cron.php приложения возвращает ключ задания => CronJob.
 * Таблица cron хранит только настройки, редактируемые в админке (active, timing), одна запись на ключ задания.
 */
class CronRegistry
{
    /** @var array<string, CronJob>|null */
    protected static ?array $jobs = null;

    /**
     * @return array<string, CronJob> ключ задания => задание
     */
    public static function jobs(): array
    {
        if (static::$jobs === null) {
            $file = Container::getPaths()->getRoot('provider/cron.php');
            $data = is_file($file) ? require $file : [];

            // задачи расширений, затем сайта: задача сайта с тем же ключом заменяет задачу расширения
            $all = [];
            foreach (ExtensionConfig::getAll() as $config) {
                $all = array_merge($all, $config->getCronJobs());
            }
            $all = array_merge($all, is_array($data) ? $data : []);

            static::$jobs = [];
            foreach ($all as $key => $job) {
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
     * Приводит таблицу cron в соответствие с кодом: добавляет записи новых заданий со значениями по умолчанию,
     * удаляет записи заданий, убранных из кода. Настройки существующих записей сохраняются.
     */
    public static function sync(): void
    {
        DB::transaction(static::doSync(...));
    }

    protected static function doSync(): void
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
                CronModel::insert([
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
