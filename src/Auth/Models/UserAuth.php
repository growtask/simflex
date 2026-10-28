<?php

namespace Simflex\Core\Auth\Models;

use Simflex\Core\System\Container;
use Simflex\Core\DB\Expr;
use Simflex\Core\System\ModelBase;

/**
 * Класс UserAuth
 * @package Simflex\Core\Auth\Models
 * @property string $token
 */
class UserAuth extends ModelBase
{
    protected static string $table = 'user_auth';
    protected static string $primaryKeyName = 'auth_id';

    public static function create(int|string|null $userId, string $validInterval = '1 WEEK'): ?static
    {
        $token = md5(microtime());
        $useragent = Container::getRequest()?->getUserAgent() ?? '';
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
        return Container::getRequest()?->getIp() ?? '';
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
