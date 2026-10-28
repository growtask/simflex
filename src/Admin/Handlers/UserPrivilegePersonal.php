<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Admin\Fields\FieldPrivilegeSelect;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Admin\Table\Presets\ForeignKeyField;
use Simflex\Core\Admin\Table\Presets\IdField;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\System\Privilege;
use Simflex\Core\Models\UserPrivilegePersonal as UserPrivilegePersonalModel;

/**
 * Привилегии, выданные пользователю лично, помимо привилегий его роли.
 * Только для разработчиков: здесь можно выдать любую привилегию, в том числе dev
 */
class UserPrivilegePersonal extends Table
{
    protected static string $model = UserPrivilegePersonalModel::class;
    protected static ?string $privilegeAdd = Privilege::DEV;
    protected static ?string $privilegeEdit = Privilege::DEV;
    protected static ?string $privilegeDelete = Privilege::DEV;

    public static function getFields(): array
    {
        return [
            new IdField(),
            new ForeignKeyField(
                name: 'user_id',
                label: 'Пользователь',
                table: 'user',
                display: 'login',
                help: 'Пользователь, которому привилегия выдаётся лично, сверх привилегий его роли',
                width: '250',
                required: true,
            ),
            new FieldDefinition(
                name: 'privilege',
                class: FieldPrivilegeSelect::class,
                label: 'Привилегия',
                help: 'Привилегия, которую получает пользователь',
                width: '1',
                required: true,
                filter: true,
            ),
        ];
    }
}
