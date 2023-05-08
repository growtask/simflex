<?php
namespace Simflex\Core\Api;

use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\Errors\Error;
use Simflex\Core\Errors\ErrorCodes;
use Simflex\Core\Models\User;

abstract class Base
{
    /**
     * @var bool Should require authentication on all methods?
     */
    protected $requireAuth = false;

    /**
     * @var User|null Authenticated user (set ONLY if requireAuth = true)
     */
    protected $user = null;

    /**
     * @return string
     */
    public abstract function execute(): string;

    /**
     * Gets name of the method to execute
     *
     * @return string
     * @throws \Simflex\Core\Errors\Error
     */
    protected function getMethodName(): string
    {
        $name = 'action' . ucfirst(Container::get('request')->getUrlParts(0) ?: 'index');
        if (!method_exists($this, $name)) {
            throw Error::byCode(ErrorCodes::APP_METHOD_NOT_FOUND);
        }

        return $name;
    }

    /**
     * Assert that user is currently authenticated
     * 
     * @throws Error
     * @return User
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
