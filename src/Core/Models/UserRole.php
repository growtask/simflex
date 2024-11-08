<?php

namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\ModelBase;

/**
 * Class UserRole
 *
 * @property int $role_id
 * @property int $priv_id
 * @property bool $active
 * @property int $npp
 * @property string $alias
 * @property string $name
 */
class UserRole extends ModelBase
{
    protected static $table = 'user_role';
    protected static $primaryKeyName = 'role_id';

    /**
     * Get role by alias
     *
     * @param string $alias Role alias
     * @return static|null
     * @throws Exception
     */
    public static function byAlias(string $alias): ?static
    {
        return static::findOne(['alias' => $alias]);
    }

    /**
     * @throws Exception
     */
    public function addPriv(UserPriv $priv): void
    {
        UserRolePriv::insertStatic([
            'role_id' => $this->role_id,
            'priv_id' => $priv->priv_id,
        ]);
    }
}