<?php

namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\ModelBase;

/**
 * Class UserRole
 *
 * @property int $role_id
 * @property string $privilege Privilege required to see and assign the role
 * @property bool $active
 * @property string $alias
 * @property string $name
 */
class UserRole extends ModelBase
{
    protected static string $table = 'user_role';
    protected static string $primaryKeyName = 'role_id';

    /**
     * Get role by alias
     *
     * @param string $alias Role alias
     * @throws Exception
     */
    public static function byAlias(string $alias): ?static
    {
        return static::findOne(['alias' => $alias]);
    }

    /**
     * @param string $privilege Privilege name, see \Simflex\Core\Privilege
     * @throws Exception
     */
    public function addPrivilege(string $privilege): void
    {
        UserRolePrivilege::insertStatic([
            'role_id' => $this->role_id,
            'privilege' => $privilege,
        ]);
    }
}
