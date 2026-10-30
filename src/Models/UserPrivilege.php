<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * Привилегия, выключенная или включённая в админке (раздел «Привилегии»). Привилегии объявлены в коде,
 * в таблице - только их переключатель
 *
 * @property int $privilege_id
 * @property string $name
 * @property bool $active
 */
class UserPrivilege extends ModelBase
{
    protected static string $table = 'user_privilege';

    public const ?string COLUMN_TIME_CREATE = null;
    public const ?string COLUMN_TIME_UPDATE = null;
    protected static string $primaryKeyName = 'privilege_id';
}
