<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * Привилегия, выданная пользователю лично
 *
 * @property int $id
 * @property int $user_id
 * @property string $privilege Имя привилегии, см. \Simflex\Core\System\Privilege
 */
class UserPrivilegePersonal extends ModelBase
{
    protected static string $primaryKeyName = 'id';
    protected static string $table = 'user_privilege_personal';
}
