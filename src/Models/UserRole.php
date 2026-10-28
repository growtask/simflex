<?php

namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\System\ModelBase;

/**
 * Класс UserRole
 *
 * @property int $role_id
 * @property string $privilege Привилегия, нужная, чтобы видеть и назначать роль
 * @property bool $active
 * @property string $alias
 * @property string $name
 */
class UserRole extends ModelBase
{
    protected static string $table = 'user_role';
    protected static string $primaryKeyName = 'role_id';

    /**
     * Роль по алиасу
     *
     * @param string $alias Алиас роли
     * @throws Exception
     */
    public static function byAlias(string $alias): ?static
    {
        return static::findOne(['alias' => $alias]);
    }

    /**
     * @param string $privilege Имя привилегии, см. \Simflex\Core\System\Privilege
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
