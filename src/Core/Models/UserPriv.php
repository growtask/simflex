<?php

namespace Simflex\Core\Models;

use Simflex\Core\DB\Where;
use Simflex\Core\ModelBase;

/**
 * Class UserPriv
 * @package Simflex\Core\Models
 * @property string $name;
 */
class UserPriv extends ModelBase
{
    protected static $table = 'user_priv';
    protected static $primaryKeyName = 'priv_id';

    /**
     * @param string $name
     * @param bool $onlyActive
     * @return UserPriv|null
     * @throws \Exception
     */
    public static function byName(string $name, bool $onlyActive = true)
    {
        $where = new Where(['name' => $name]);
        if ($onlyActive) {
            $where['active'] = true;
        }
        return static::findOne($where);
    }

}