<?php

namespace Simflex\Core;

use Simflex\Core\DB;
use Simflex\Core\Helpers\Phone;
use Simflex\Core\Mail;
use Simflex\Core\Sms;
use Simflex\Core\UserInstance;

/**
 * Class User
 * @package Simflex\Core
 * @deprecated use Container::getUser() and \Simflex\Core\Models\User and Simplex Auth package
 * @link https://github.com/glushkovds/simplex-auth
 */
class User
{
    public static int $id = 0;
    public static string $login = '';
    public static int $roleId = 0;
    public static string $roleName = '';

    protected static UserInstance $instance;

    public static function login(string|UserInstance $type = 'site'): void
    {
        if ('site' == $type) {
            self::$instance = new UserInstance('user_id', 'user_hash', 'hash', 'ch', 'cs');
        }
        if ('admin' == $type) {
            self::$instance = new UserInstance('admin_user_id', 'admin_user_hash', 'hash_admin', 'cha', 'csa');
        }
        if ($type instanceof UserInstance) {
            self::$instance = $type;
        }

        self::$id = self::$instance->id;
        self::$login = self::$instance->login;
        self::$roleId = self::$instance->roleId;
        self::$roleName = self::$instance->roleName;
    }

    public static function logout(): void
    {
        self::$instance->logout();
        self::$id = 0;
        self::$login = '';
        self::$roleId = 0;
        self::$roleName = '';
    }

    public static function logout2(): void
    {
        self::$instance = new UserInstance('', '', '', '', '');
        self::$id = 0;
        self::$login = '';
        self::$roleId = 0;
        self::$roleName = '';
    }

    public static function privIds(): array
    {
        return self::$instance->privIds();
    }

    public static function privNames(): array
    {
        return self::$instance->privNames();
    }

    /**
     * @param string|int|bool $priv - можно указать название привилегии, можно ее ID. Если false, то вернет все привилегии
     */
    public static function ican(string|int|bool $priv = false): array|bool
    {
        return self::$instance->ican($priv);
    }

    /**
     * Возвращает информацию о пользователе
     * @param string (optional) $field - если указано, возвращает конкретное поле
     * @return false|array|string
     */
    public static function info(string|false $field = false): mixed
    {
        return self::$instance->info($field);
    }

    public static function create(array $data): array
    {
        if (self::$id) {
            return ['success' => false, 'error_code' => 1, 'error' => 'Пользователь уже авторизован'];
        }

        $login = Phone::extract((string)@$data['login']);
        $pass = (string)@$data['pass'] ?: rand(100000, 999999);
        $email = (string)@$data['email'];
        $name = (string)@$data['name'];

        $errors = [];
        $login ? null : $errors[] = 'логин';
        $email ? null : $errors[] = 'email';
        $name ? null : $errors[] = 'имя';
        if (count($errors)) {
            return ['success' => false, 'error_code' => 2, 'error' => 'Не указано: ' . implode(', ', $errors)];
        }

        $user = DB::result('SELECT user_id FROM user WHERE login = ?', 0, [$login]);
        if ($user) {
            return ['success' => false, 'error_code' => 3, 'error' => 'Пользователь с таким логином уже зарегистрирован'];
        }

        $user = DB::result('SELECT user_id FROM user WHERE email = ?', 0, [$email]);
        if ($user) {
            return ['success' => false, 'error_code' => 4, 'error' => 'Пользователь с таким почтовым адресом уже зарегистрирован'];
        }

        $passMD5 = md5($pass);
        $hash = md5(microtime());
        DB::query(
            'INSERT INTO user SET login = ?, email = ?, name = ?, password = ?, hash = ?',
            [$login, $email, $name, $passMD5, $hash]
        );
        $userId = DB::insertId();

        if (!$userId) {
            return ['success' => false, 'error_code' => 5, 'error' => 'Ошибка регистрации'];
        }

        $HTTPHost = (string)($_SERVER['HTTP_HOST'] ?? '');
        if (function_exists('idn_to_utf8') && str_contains($HTTPHost, 'xn--')) {
            $HTTPHost = idn_to_utf8($HTTPHost) ?: $HTTPHost;
        }

        // Отправляем письмо об успешной регистрации
        $mailTpl = SF_ROOT_PATH . '/theme/default/mail/user_create.tpl';
        if ($email && is_file($mailTpl)) {
            ob_start();
            include $mailTpl;
            $html = ob_get_contents();
            ob_end_clean();
            Mail::staticSend($email, "Регистрация на сайте $HTTPHost", $html);
        }

        // Отправляем СМС с рег. данными
        $smsText = "Для Вас зарегистрирован аккаунт на сайте $HTTPHost: логин $login, пароль $pass";
        Sms::send($login, $smsText);

        if ($userId) {
            setcookie('user_id', $userId, time() + 3600 * 24 * 365, '/');
            setcookie('user_hash', $hash, time() + 3600 * 24 * 365, '/');
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_hash'] = $hash;
            self::login();
        }

        return ['success' => true, 'user_id' => $userId];
    }

    public static function authorizeOnce($login, $password): void
    {
        $GLOBALS[UserInstance::class]['login'] = $login;
        $GLOBALS[UserInstance::class]['password'] = $password;
        static::login();
    }
}
