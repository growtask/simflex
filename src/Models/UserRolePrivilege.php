<?php

namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\System\ModelBase;

/**
 * Модель UserRolePrivilege
 *
 * @property int $id
 * @property int $role_id
 * @property string $privilege Имя привилегии, см. \Simflex\Core\System\Privilege
 */
class UserRolePrivilege extends ModelBase
{
    protected static string $primaryKeyName = 'id';
    protected static string $table = 'user_role_privilege';

    public const ?string COLUMN_TIME_CREATE = null;
    public const ?string COLUMN_TIME_UPDATE = null;

    /**
     * @throws Exception
     */
    public function offsetGetRole(): ?UserRole
    {
        return UserRole::findOne(['role_id' => $this->role_id]);
    }
}
