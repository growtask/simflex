<?php

namespace Simflex\Admin;

/**
 * Модуль, размещённый на страницах админки, см. AdminModules::init()
 */
readonly class AdminModule
{
    /**
     * @param class-string<\Simflex\Core\ModuleBase> $class
     * @param string $position Позиция в макете админки: Page::position($position)
     * @param string $name Заголовок модуля, показывается, если задан $params['is_title']
     * @param array $params Параметры модуля, как у модулей сайта в module_item.params
     * @param string[] $sections Ссылки пунктов меню (как они объявлены в Menu), на которых показывается модуль.
     *        Пусто — на всех страницах: меню, хлебные крошки
     */
    public function __construct(
        public string $class,
        public string $position,
        public string $name = '',
        public array $params = [],
        public array $sections = [],
    ) {
    }
}
