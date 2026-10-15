<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $menu_id
 * @property int $menu_pid
 * @property int $priv_id
 * @property int $npp
 * @property string $name
 * @property string $link
 * @property string $model
 * @property string $icon
 * @property bool $hidden
 */
class AdminMenu extends ModelBase
{
    protected static string $primaryKeyName = 'menu_id';
    protected static string $table = 'admin_menu';

    public static function byLink(string $link): ?static
    {
        return static::findOne(['link' => $link]);
    }
}
