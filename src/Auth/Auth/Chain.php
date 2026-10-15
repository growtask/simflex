<?php


namespace Simflex\Auth\Auth;


use JetBrains\PhpStorm\Deprecated;
use Simflex\Core\Models\User;

class Chain extends \Simflex\Core\Middleware\Chain
{
    /**
     * @var class-string<User>
     */
    protected string $userModelClass = User::class;

    /**
     * @param string $class subclass of Simflex\Core\Models\User
     */
    #[Deprecated('Use Factory override instead')]
    public function setUserModelClass(string $class): static
    {
        if ($class && !is_subclass_of($class, User::class)) {
            throw new \InvalidArgumentException("userModelClass must be sublass of Simflex\Core\Models\User");
        }
        $this->userModelClass = $class;
        return $this;
    }

    public function process(mixed $payload = null): mixed
    {
        foreach ($this->middlewares as $mw) {
            if ($mw instanceof BaseMiddleware) {
                $mw->setUserModelClass($this->userModelClass);
            } else {
                throw new \InvalidArgumentException("All middlewares must be sublass of Simflex\Auth\Auth\BaseMiddleware");
            }
        }
        return parent::process($payload);
    }

    /**
     * @return string|User string of user model class
     */
    public function getUserModelClass(): string
    {
        return $this->userModelClass;
    }

}
