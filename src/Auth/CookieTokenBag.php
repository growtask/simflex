<?php

namespace Simflex\Core\Auth;

use Simflex\Core\System\Container;
use Simflex\Core\System\Location;
use Simflex\Core\Http\Cookie\CookieBag;
use Simflex\Core\Http\Cookie\CookieItem;

class CookieTokenBag extends CookieBag
{
    public const COOKIE_NAME = 'sft';

    public static function defaultPrefix(): string
    {
        return Container::getLocation() === Location::Site ? 's' : 'a';
    }

    public function __construct(string $prefix)
    {
        parent::__construct($prefix);
        $this->cookies['token'] = new CookieItem($this->prefix . self::COOKIE_NAME);
    }

    public function get(): ?string
    {
        return $this->cookies['token']->getValue();
    }

    public function set(string $token, \DateTime $expires): void
    {
        $this->cookies['token']->setValue($token)->setExpires($expires)->save();
    }

    public function delete(): void
    {
        $this->set('', new \DateTime());
    }
}
