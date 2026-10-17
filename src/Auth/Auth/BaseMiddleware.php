<?php

namespace Simflex\Auth\Auth;

use Simflex\Core\Middleware\Handler;
use Simflex\Core\Models\User;

/**
 * The user model class is User; override it with Container::getFactory()->override(User::class, AppUser::class)
 */
abstract class BaseMiddleware implements Handler
{
    /**
     * @var class-string<User>
     */
    protected string $userModelClass = User::class;
}
