<?php

namespace Simflex\Admin;

use Simflex\Admin\Plugins\Log\Log;

;

use Simflex\Auth\Bootstrap;
use Simflex\Auth\CookieTokenBag;
use Simflex\Auth\Models\UserAuth;
use Simflex\Auth\SessionStorage;
use Simflex\DB\DB;
use Simflex\Models\User;
use Simflex\Core\Privilege;

class Auth
{
    public const REMEMBER_ME_INTERVAL = '1 WEEK';

    public static function login($login, $password, bool $isRemember, string $redirect): void
    {
        if (empty($login) || empty($password)) {
            return;
        }

        $successLogin = false;
        if (strpos($redirect, '//') !== false) {
            $redirect = '/';
        }
        if (preg_match('@^[0-9a-z\@\-\.]+$@i', $login)) {
            $q = 'SELECT u.*
                    FROM user u
                    JOIN user_role r ON r.role_id=u.role_id
                    WHERE login=:login
                      AND u.active=1
                      AND r.active=1';
            $row = DB::result($q, '', ['login' => strtolower($login)]);
            if ($row && md5($password) === $row['password']) {
                $user = (new User())->fill($row);
                if ($user->ican(Privilege::ADMIN)) {
                    SessionStorage::set($row['user_id']);
                    Bootstrap::authByUser($user);
                    if ($isRemember) {
                        $auth = UserAuth::create($row['user_id'], static::REMEMBER_ME_INTERVAL);
                        $cookies = new CookieTokenBag(CookieTokenBag::defaultPrefix());
                        $cookies->set($auth->token, new \DateTime(static::REMEMBER_ME_INTERVAL));
                    }
                    $successLogin = true;
                    $logLogin = $login;
                    Log::a('login_success', "Логин: $logLogin");
                }
            }
        }
        if (!$successLogin) {
            Log::a('login_attempt', "Логин: {$login}");
        }

        header("Location: $redirect");
        exit;
    }

    public static function logout(): never
    {
        $redirect = $_REQUEST['r'] ?? Core::url();
        if (strpos($redirect, '//') !== false) {
            $redirect = '/';
        }
        Bootstrap::signOut();
        header("Location: $redirect");
        exit;
    }
}
