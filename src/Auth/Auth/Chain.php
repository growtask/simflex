<?php


namespace Simflex\Auth\Auth;


use Simflex\Core\Models\User;

class Chain extends \Simflex\Core\Middleware\Chain
{
    /**
     * @var User
     */
    protected $userModelClass = User::class;

    /**
     * @param string $class subclass of Simflex\Core\Models\User
     * @return $this
     */
    public function setUserModelClass($class)
    {
        if ($class && !is_subclass_of($class, User::class)) {
            throw new \InvalidArgumentException("userModelClass must be sublass of Simflex\Core\Models\User");
        }
        $this->userModelClass = $class;
        return $this;
    }

    public function process($payload = null)
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
    public function getUserModelClass()
    {
        return $this->userModelClass;
    }

}