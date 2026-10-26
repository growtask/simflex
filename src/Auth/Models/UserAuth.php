<?php

namespace Simflex\Auth\Models;

use Simflex\Core\DB\Expr;
use Simflex\Core\ModelBase;

/**
 * Класс UserAuth
 * @package Simflex\Auth\Models
 * @property string $token
 */
class UserAuth extends ModelBase
{
    protected static string $table = 'user_auth';
    protected static string $primaryKeyName = 'auth_id';

    public static function create(int|string|null $userId, string $validInterval = '1 WEEK'): ?static
    {
        $token = md5(microtime());
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $model = new static();
        $success = $model->insert([
            'user_id' => $userId,
            'token' => $token,
            'time_expires' => new Expr("NOW() + INTERVAL $validInterval"),
            'useragent' => $useragent,
            'ip' => static::getRemoteAddr(),
        ]);
        return $success ? $model : null;
    }

    protected static function getRemoteAddr(): string
    {
        return @$_SERVER['HTTP_X_FORWARDED_FOR'] ?: (@$_SERVER['REMOTE_ADDR'] ?: '');
    }

    public static function findByToken(string $token): ?static
    {
        static::bulkDelete(['time_last_login < NOW() - INTERVAL 1 YEAR']);
        return static::findOne(['time_expires > NOW()', 'token' => $token]);
    }

    public function prolong(string $validInterval = '1 WEEK'): bool
    {
        return $this->update([
            'time_expires' => new Expr("NOW() + INTERVAL $validInterval"),
            'time_last_login' => new Expr('NOW()'),
            'ip' => static::getRemoteAddr(),
        ]);
    }
}
