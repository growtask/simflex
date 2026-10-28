<?php

namespace Simflex\Admin;

use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\ParamGroup;

/**
 * Модуль с настройками, которые редактируются в админке.
 * Значения хранятся сериализованными в module_item.params.
 */
interface HasAdminParams
{
    /**
     * @return array<FieldDefinition|ParamGroup> FieldDefinition попадает в общий блок "Параметры",
     *         ParamGroup образует свой блок
     */
    public static function adminParams(): array;
}
