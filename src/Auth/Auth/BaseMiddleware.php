<?php


namespace Simflex\Auth\Auth;


use Simflex\Core\Middleware\Handler;
use Simflex\Core\Models\User;

abstract class BaseMiddleware implements Handler
{

    /**
     * @var User
     */
    protected $userModelClass = User::class;

    /**
     * @param string $userModelClass
     */
    public function setUserModelClass($userModelClass): void
    {
        $this->userModelClass = $userModelClass;
    }

}