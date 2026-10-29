<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * @property int $menu_id
 * @property int $menu_pid
 * @property string $component Класс компонента, см. \Simflex\Core\System\ExtensionRegistry
 * @property bool $active
 * @property bool $hidden
 * @property int $npp
 * @property string $place Где выводится пункт: PLACE_MAIN (основное меню) или PLACE_FOOTER (колонка подвала)
 * @property string $name
 * @property string $link
 */
class Menu extends ModelBase
{
    protected static string $primaryKeyName = 'menu_id';
    protected static string $table = 'menu';

    /** Основное меню (шапка): корневые пункты - пункты меню */
    public const PLACE_MAIN = 'main';
    /** Подвал: корневые пункты - заголовки колонок, вложенные - ссылки колонки */
    public const PLACE_FOOTER = 'footer';

    public static function byLink(string $link): ?self
    {
        return self::findOne(['link' => $link]);
    }
}
