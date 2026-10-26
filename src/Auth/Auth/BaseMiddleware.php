<?php

namespace Simflex\Auth\Auth;

use Simflex\Core\Middleware\Handler;
use Simflex\Core\Models\User;

/**
 * Класс модели пользователя — User, переопределяется через
 * Container::getFactory()->override(User::class, AppUser::class)
 */
abstract class BaseMiddleware implements Handler
{
    /**
     * @var class-string<User>
     */
    protected string $userModelClass = User::class;
}
