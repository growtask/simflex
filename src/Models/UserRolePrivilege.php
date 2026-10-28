<?php

namespace Simflex\Models;

use Exception;
use Simflex\Core\ModelBase;

/**
 * Модель UserRolePrivilege
 *
 * @property int $id
 * @property int $role_id
 * @property string $privilege Имя привилегии, см. \Simflex\Core\Privilege
 */
class UserRolePrivilege extends ModelBase
{
    protected static string $primaryKeyName = 'id';
    protected static string $table = 'user_role_privilege';

    /**
     * @throws Exception
     */
    public function offsetGetRole(): ?UserRole
    {
        return UserRole::findOne(['role_id' => $this->role_id]);
    }
}
