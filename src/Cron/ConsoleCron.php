<?php

namespace Simflex\Core\Cron;

use Simflex\Core\System\Container;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\Help;
use Simflex\Core\Console\ConsoleBase;
use Simflex\Core\Logger\Log;

/**
 * Команды cron: ./sf cron/<команда>.
 *
 * Системный планировщик запускает ./sf cron/run раз в минуту; строку для crontab печатает cron/crontab,
 * добавляет в crontab текущего пользователя cron/install (запуск на хосте, где работает сайт). В Docker
 * cron/run раз в минуту запускает сервис cron из docker-compose.yml шаблона сайта
 */
class ConsoleCron extends ConsoleBase
{
    /** Метка строки сайта в crontab: по ней install и uninstall находят свою строку */
    protected const string MARK = '# simflex-cron ';

    #[Command('Run the cron jobs scheduled for the current minute, or one job now')]
    public function run(#[Help('Job key in provider/cron.php: run only this job now')] string $job = ''): void
    {
        try {
            (new Cron($job !== '' ? $job : null))->execute();
        } catch (\Throwable $e) {
            Log::critical('Cron error: {error}', ['error' => $e->getMessage()]);
        }
    }

    #[Command('Print the crontab line of this site')]
    public function crontab(): void
    {
        echo static::getLine(), "\n";
    }

    #[Command('Add the line of this site to the crontab of the current user')]
    public function install(): void
    {
        $lines = static::getOtherLines();
        $lines[] = static::getLine();
        static::save($lines);
        Log::notice('Crontab: {line}', ['line' => static::getLine()]);
    }

    #[Command('Remove the line of this site from the crontab of the current user')]
    public function uninstall(): void
    {
        static::save(static::getOtherLines());
        Log::notice('Crontab line of the site is removed');
    }

    /**
     * Строка crontab: раз в минуту ./sf cron/run в корне сайта
     */
    public static function getLine(): string
    {
        $root = escapeshellarg(Container::getPaths()->getRoot());
        return "* * * * * cd $root && " . PHP_BINARY . ' sf cron/run > /dev/null 2>&1 ' . static::MARK . Container::getPaths()->getRoot();
    }

    /**
     * @return list<string> Строки crontab текущего пользователя, кроме строки этого сайта
     */
    protected static function getOtherLines(): array
    {
        exec('crontab -l 2>/dev/null', $lines);
        return array_values(array_filter(
            $lines,
            fn(string $line) => !str_ends_with($line, static::MARK . Container::getPaths()->getRoot())
        ));
    }

    /**
     * @param list<string> $lines
     */
    protected static function save(array $lines): void
    {
        $file = tempnam(sys_get_temp_dir(), 'crontab');
        file_put_contents($file, $lines ? implode("\n", $lines) . "\n" : '');
        exec('crontab ' . escapeshellarg($file) . ' 2>&1', $output, $code);
        unlink($file);
        if ($code !== 0) {
            throw new \RuntimeException('crontab: ' . implode("\n", $output));
        }
    }
}
