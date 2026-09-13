<?php

namespace Simflex\Core\Cron;

/**
 * Cron job declared in the app's provider/cron.php.
 * $timing and $active are the defaults: the cron table keeps the values edited in the admin panel.
 */
readonly class CronJob
{
    /**
     * @param string $name Title in the admin panel
     * @param array{0: class-string, 1: string} $handler [class, method]; a non-static method is called on an
     *        instance whose constructor dependencies are resolved by \Simflex\Core\DI\Injector
     * @param string $timing Default schedule, as in *nix crontab, e.g. '30 3 * * *'
     * @param bool $active Enabled by default
     * @param array $params Arguments of the method (positional or named)
     */
    public function __construct(
        public string $name,
        public array $handler,
        public string $timing,
        public bool $active = true,
        public array $params = [],
    ) {
    }
}
