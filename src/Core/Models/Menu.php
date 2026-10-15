<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $menu_id
 * @property int $menu_pid
 * @property int $component_id
 * @property bool $active
 * @property bool $hidden
 * @property int $npp
 * @property string $name
 * @property string $link
 */
class Menu extends ModelBase
{
    protected static string $primaryKeyName = 'menu_id';
    protected static string $table = 'menu';

    public static function byLink(string $link): ?self
    {
        return self::findOne(['link' => $link]);
    }
}
