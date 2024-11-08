<?php

namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\ModelBase;

/**
 * UserRolePriv model
 *
 * @property int $id
 * @property int $role_id
 * @property int $priv_id
 */
class UserRolePriv extends ModelBase
{
    protected static $primaryKeyName = 'id';
    protected static $table = 'user_role_priv';

    /**
     * @throws Exception
     */
    public function offsetGetRole(): ?UserRole
    {
        return UserRole::findOne(['role_id' => $this->role_id]);
    }

    /**
     * @throws Exception
     */
    public function offsetGetPriv(): ?UserPriv
    {
        return UserPriv::findOne(['priv_id' => $this->priv_id]);
    }
}