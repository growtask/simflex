<?php

namespace Simflex\Core\Http\Cookie;

class CookieBag
{
    protected string $prefix = '';
    /** @var CookieItem[] */
    protected array $cookies = [];
    protected string $prolongPeriod = '1 WEEK';

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    protected function save(): void
    {
        foreach ($this->cookies as $cookie) {
            $cookie->save();
        }
    }

    public function prolong(): void
    {
        $expires = new \DateTime('+' . $this->prolongPeriod);
        foreach ($this->cookies as $cookie) {
            $cookie->setExpires($expires)->save();
        }
    }
}
