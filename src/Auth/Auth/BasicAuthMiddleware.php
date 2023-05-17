<?php


namespace Simflex\Auth\Auth;

class BasicAuthMiddleware extends BaseMiddleware
{

    /**
     * @inheritDoc
     */
    public function handle($payload, \Closure $next)
    {
        if ($payload) {
            return $next($payload);
        }
        $login = $_SERVER['PHP_AUTH_USER'] ?? null;
        $pass = $_SERVER['PHP_AUTH_PW'] ?? null;
        if ($login && $pass) {
            $user = $this->userModelClass::findOne(['login' => $login, 'password' => md5($pass)]);
            if ($user) {
                return $next($user);
            }
        }
        return $next($payload);
    }

}