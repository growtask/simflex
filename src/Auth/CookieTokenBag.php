<?php

namespace Simflex\Auth;

use Simflex\Core\Cookie\CookieBag;
use Simflex\Core\Cookie\CookieItem;

class CookieTokenBag extends CookieBag
{
    public const COOKIE_NAME = 'sft';

    public static function defaultPrefix(): string
    {
        return SF_LOCATION_SITE == SF_LOCATION ? 's' : 'a';
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
