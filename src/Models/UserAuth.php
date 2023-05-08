<?php

namespace Simflex\Auth\Models;

use Simflex\Core\DB\Expr;
use Simflex\Core\ModelBase;

/**
 * Class UserAuth
 * @package Simflex\Auth\Models
 * @property string $token
 */
class UserAuth extends ModelBase
{

    protected static $table = 'user_auth';
    protected static $primaryKeyName = 'auth_id';

    /**
     * @param int|null $userId
     * @param string $validInterval [optional = 1 WEEK]
     * @return static|null
     */
    public static function create($userId, $validInterval = '1 WEEK')
    {
        $token = md5(microtime());
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $model = new static;
        $success = $model->insert([
            'user_id' => $userId,
            'token' => $token,
            'time_expires' => new Expr("NOW() + INTERVAL $validInterval"),
            'useragent' => $useragent,
            'ip' => static::getRemoteAddr(),
        ]);
        return $success ? $model : null;
    }

    protected static function getRemoteAddr()
    {
        return @$_SERVER['HTTP_X_FORWARDED_FOR'] ?: (@$_SERVER['REMOTE_ADDR'] ?: '');
    }

    public static function findByToken($token)
    {
        static::bulkDelete(['time_last_login < NOW() - INTERVAL 1 YEAR']);
        return static::findOne(['time_expires > NOW()', 'token' => $token]);
    }

    public function prolong($validInterval = '1 WEEK')
    {
        return $this->update([
            'time_expires' => new Expr("NOW() + INTERVAL $validInterval"),
            'time_last_login' => new Expr("NOW()"),
            'ip' => static::getRemoteAddr(),
        ]);
    }

}