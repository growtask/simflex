<?php

namespace Simflex\Core\Cron;

/**
 * Задание cron, объявленное в provider/cron.php приложения.
 * $timing и $active - значения по умолчанию: таблица cron хранит значения, отредактированные в админке.
 */
readonly class CronJob
{
    /**
     * @param string $name Название в админке
     * @param array{0: class-string, 1: string} $handler [класс, метод]; нестатический метод вызывается у экземпляра,
     *        зависимости конструктора которого подставляет \Simflex\Core\DI\Injector
     * @param string $timing Расписание по умолчанию, как в crontab *nix, например '30 3 * * *'
     * @param bool $active Включено по умолчанию
     * @param array $params Аргументы метода (позиционные или именованные)
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
