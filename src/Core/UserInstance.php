<?php


namespace Simflex\Core;

use Simflex\Core\DB;

/**
 * Class UserInstance
 * @package Simflex\Core
 * @deprecated use Container::getUser() and \Simflex\Core\Models\User and Simplex Auth package
 * @link https://github.com/glushkovds/simplex-auth
 */
class UserInstance
{

    public int $id = 0;
    public string $login = '';
    public int $roleId = 0;
    public string $roleName = '';
    private array $privIds = [];
    private array $privNames = [];
    private array|false $info;
    private string $idName;
    private string $hashName;
    private string $dbHashName;
    private string $remIdName;
    private string $remHashName;

    public function __construct(
        string $idName,
        string $hashName,
        string $dbHashName,
        string $remIdName,
        string $remHashName
    ) {
        $this->idName = $idName;
        $this->hashName = $hashName;
        $this->dbHashName = $dbHashName;
        $this->remIdName = $remIdName;
        $this->remHashName = $remHashName;
        $this->login();
    }

    public function logout(): void
    {
        $_SESSION[$this->idName] = 0;
        $_SESSION[$this->hashName] = '';
        setcookie($this->remIdName);
        setcookie($this->remHashName);
        unset($_COOKIE[$this->remIdName]);
        unset($_COOKIE[$this->remHashName]);
    }
    
    public function initByModel(\Simflex\Core\Models\User $model): void
    {
        $this->id = $model->getId();
        $this->login = $model->login;
        $this->roleId = $model->roleId;
        $this->roleName = $model->role->name;
        $this->login();
    }

    private function login(): void
    {
        if (!isset($_SESSION[$this->idName]) && isset($_COOKIE[$this->remIdName]) && isset($_COOKIE[$this->remHashName])) {
            $userId_md5 = DB::escape($_COOKIE[$this->remIdName]);
            $hash = DB::escape($_COOKIE[$this->remHashName]);
            $q = "select user_id `id` from `user` where md5(`user_id`) = '$userId_md5' and $this->dbHashName = '$hash'";
            if ($userId = DB::result($q, "id")) {
                $_SESSION[$this->idName] = $userId;
                $_SESSION[$this->hashName] = $hash;
                setcookie($this->remIdName, $_COOKIE[$this->remIdName], time() + 60 * 60 * 24 * 3, "/");
                setcookie($this->remHashName, $_COOKIE[$this->remHashName], time() + 60 * 60 * 24 * 3, "/");
            }
        }

        // Authorization for API, for authorize method
        if (!empty($GLOBALS[self::class]['login']) && !empty($GLOBALS[self::class]['password'])) {
            $q = "
                    SELECT user_id, role_id, login, password, $this->dbHashName, r.name role_name
                    FROM user u
                    JOIN user_role r USING(role_id)
                    WHERE login='" . DB::escape($GLOBALS[self::class]['login']) . "' AND u.active=1 AND r.active=1
                ";
            $r = DB::query($q);
            if ($row = DB::fetch($r)) {
                if (md5($GLOBALS[self::class]['password']) === $row['password']) {
                    $this->id = (int)$row[$this->idName];
                    $this->login = $row['login'];
                    $this->roleId = (int)$row['role_id'];
                    $this->roleName = $row['role_name'];
                }
            }
        }

        if (!empty($_SESSION[$this->idName]) && !empty($_SESSION[$this->hashName])) {
            $q = "
                    SELECT user_id, role_id, login, $this->dbHashName, r.name role_name
                    FROM user u
                    JOIN user_role r USING(role_id)
                    WHERE user_id=" . (int)$_SESSION[$this->idName] . " AND u.active=1 AND r.active=1
                ";
            $r = DB::query($q);
            if ($row = DB::fetch($r)) {
                if ($_SESSION[$this->hashName] === $row[$this->dbHashName]) {
                    $this->id = (int)$_SESSION[$this->idName];
                    $this->login = $row['login'];
                    $this->roleId = (int)$row['role_id'];
                    $this->roleName = $row['role_name'];
                }
            }
        }

        if ($this->id) {
            $q = "
                SELECT priv_id, name
                FROM user_priv
                WHERE active=1
                AND (
                    priv_id IN(SELECT priv_id FROM user_role_priv WHERE role_id" . ($this->roleId ? '=' . (int)$this->roleId : " IS NULL") . ")
                    OR priv_id IN(SELECT priv_id FROM user_priv_personal WHERE user_id=" . $this->id . ")
                )
            ";
            $r = DB::query($q);
            while ($row = DB::fetch($r)) {
                $this->privIds[(int)$row['priv_id']] = (int)$row['priv_id'];
                $this->privNames[$row['name']] = $row['name'];
            }
        }
    }

    /**
     * Возвращает информацию о пользователе
     * @param string|false $field (optional) - если указано, возвращает конкретное поле
     * @return false|array|string
     */
    public function info(string|false $field = false, bool $buffered = true): mixed
    {
        if (!$this->id) {
            return false;
        }
        if (!isset($this->info) || !$buffered) {
            $q = "select * from user where user_id = " . $this->id;
            $this->info = DB::result($q);
            $this->info['role_name'] = $this->roleName;
        }
        $ret = false;
        if ($field === false) {
            $ret = $this->info;
        } elseif (isset($this->info[$field])) {
            $ret = $this->info[$field];
        }
        return $ret;
    }

    public function privIds(): array
    {
        return count($this->privIds) ? $this->privIds : [0];
    }

    public function privNames(): array
    {
        return count($this->privNames) ? $this->privNames : [''];
    }

    public function ican(string|int|bool $priv): array|bool
    {
        if (false === $priv) {
            return $this->privNames;
        }
        if (is_int($priv)) {
            return isset($this->privIds[$priv]);
        }
        return isset($this->privNames[$priv]);
    }

}
