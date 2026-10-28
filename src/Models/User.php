<?php

namespace Simflex\Models;

use Exception;
use Simflex\Core\Buffer;
use Simflex\DB\DB;
use Simflex\Core\ModelBase;
use Simflex\Core\Privilege;

/**
 * Модель пользователя
 *
 * @property int user_id
 * @property int role_id
 * @property bool active
 * @property string login
 * @property string password
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
     * @param string $privilege Имя привилегии, см. \Simflex\Core\Privilege
     */
    public function ican(string $privilege): bool
    {
        return isset($this->getPrivileges()[$privilege]);
    }

    /**
     * Привилегии роли и личные, объявленные в коде и включённые
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
     * Пользователи, у которых есть любая из привилегий по роли или лично, и разработчики
     *
     * @example User::findAdv()->modify('hasPrivileges', ['privilege_name'])->all()
     */
    public static function aqModifyHasPrivileges(\Simflex\DB\AQ $AQ, array $privileges): void
    {
        if (empty($privileges)) {
            return;
        }
        // у разработчиков есть все привилегии
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
