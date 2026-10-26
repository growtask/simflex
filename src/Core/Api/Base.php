<?php

namespace Simflex\Core\Api;

use Simflex\Core\Container;
use Simflex\Core\Errors\Error;
use Simflex\Core\Errors\ErrorCodes;
use Simflex\Core\Models\User;

abstract class Base
{
    /**
     * @var bool Требовать ли авторизацию во всех методах
     */
    protected bool $requireAuth = false;

    /**
     * @var User|null Авторизованный пользователь (задан ТОЛЬКО при requireAuth = true)
     */
    protected ?User $user = null;

    abstract public function execute(): string;

    /**
     * Возвращает имя метода для выполнения
     *
     * @throws \Simflex\Core\Errors\Error
     */
    protected function getMethodName(): string
    {
        $parts = Container::get('request')->getUrlParts();
        $name = 'action' . ucfirst(end($parts) ?: 'index');
        if (!method_exists($this, $name)) {
            throw Error::byCode(ErrorCodes::APP_METHOD_NOT_FOUND);
        }

        return $name;
    }

    /**
     * Проверяет, что пользователь авторизован
     *
     * @throws Error
     */
    protected function assertAuthenticated(): User
    {
        $user = Container::get('user');
        if (!$user) {
            throw Error::byCode(ErrorCodes::APP_UNAUTHORIZED);
        }

        return $user;
    }
}
