<?php

namespace Simflex\Auth\Auth;

use JetBrains\PhpStorm\Deprecated;
use Simflex\Core\Middleware\Handler;
use Simflex\Core\Models\User;

abstract class BaseMiddleware implements Handler
{
    /**
     * @var class-string<User>
     */
    protected string $userModelClass = User::class;

    #[Deprecated('Use Factory override instead')]
    public function setUserModelClass(string $userModelClass): void
    {
        $this->userModelClass = $userModelClass;
    }
}
