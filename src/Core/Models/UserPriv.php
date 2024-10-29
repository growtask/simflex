<?php

namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\DB\Where;
use Simflex\Core\ModelBase;

/**
 * Class UserPriv
 *
 * @property int priv_id
 * @property bool active
 * @property int npp
 * @property string name
 * @property string comment
 */
class UserPriv extends ModelBase
{
    protected static $table = 'user_priv';
    protected static $primaryKeyName = 'priv_id';

    /**
     * Find user priv by name
     *
     * @param string $name Name of user priv
     * @param bool $onlyActive Select only active user priv
     * @return UserPriv|null User priv model
     * @throws Exception
     */
    public static function byName(string $name, bool $onlyActive = true): ?UserPriv
    {
        $where = new Where(['name' => $name]);
        if ($onlyActive) {
            $where['active'] = true;
        }

        return static::findOne($where);
    }
}