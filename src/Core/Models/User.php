<?php

namespace Simflex\Core\Models;

use Exception;
use Simflex\Core\Buffer;
use Simflex\Core\DB;
use Simflex\Core\ModelBase;
use Simflex\Core\Privilege;

/**
 * User model
 *
 * @property int user_id
 * @property int role_id
 * @property bool active
 * @property string login
 * @property string password
 * @property string hash
 * @property string hash_admin
 * @property string email
 * @property string name
 * @property string code
 */
class User extends ModelBase
{
    protected static string $table = 'user';
    protected static string $primaryKeyName = 'user_id';

    /**
     * @throws Exception
     */
    protected function offsetGetRole(): ?UserRole
    {
        return UserRole::findOne(['role_id' => $this->role_id]);
    }

    /**
     * @param string $privilege Privilege name, see \Simflex\Core\Privilege
     */
    public function ican(string $privilege): bool
    {
        return isset($this->getPrivileges()[$privilege]);
    }

    /**
     * Privileges of the role and the personal ones, declared in code and enabled
     *
     * @return array<string, string> name => name
     */
    public function getPrivileges(): array
    {
        return Buffer::getOrSet('user-privilege-' . $this->id, function () {
            $q = '
                SELECT privilege FROM user_role_privilege WHERE role_id' . ($this->role_id ? '=' . (int)$this->role_id : ' IS NULL') . '
                UNION SELECT privilege FROM user_privilege_personal WHERE user_id=' . (int)$this->id;
            $names = Privilege::granted(array_column(DB::assoc($q) ?: [], 'privilege'));
            return array_combine($names, $names);
        });
    }

    /**
     * Users having any of the privileges by their role or personally, and the developers
     *
     * @example User::findAdv()->modify('hasPrivileges', ['privilege_name'])->all()
     */
    public static function aqModifyHasPrivileges(\Simflex\Core\DB\AQ $AQ, array $privileges): void
    {
        if (empty($privileges)) {
            return;
        }
        // developers have all the privileges
        $privileges = [...Privilege::filterActive($privileges), Privilege::DEV];
        $in = "'" . implode("','", DB::escape($privileges)) . "'";
        $table = static::$table;
        $AQ->where("(
            $table.user_id IN(SELECT user_id FROM user_privilege_personal WHERE privilege IN($in))
            OR $table.role_id IN(
                SELECT role_id FROM user_role_privilege JOIN user_role USING(role_id) WHERE user_role.active = 1 AND privilege IN($in)
            )
        )");
    }
}
