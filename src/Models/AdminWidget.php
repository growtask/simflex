<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * Виджет на странице группы админки
 *
 * @property int $widget_id
 * @property string $class Класс виджета (наследник Admin\WidgetBase)
 * @property string $name
 * @property string $privilege Привилегия, без которой виджет не показывается; пусто - всем
 * @property string $param
 * @property int $npp
 * @property bool $active
 */
class AdminWidget extends ModelBase
{
    protected static string $table = 'admin_widget';
    protected static string $primaryKeyName = 'widget_id';
}
