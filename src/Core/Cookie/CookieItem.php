<?php

namespace Simflex\Core\Cookie;

class CookieItem
{
    protected string $key;
    protected ?string $value;
    protected ?\DateTime $expires;

    public function __construct(string $key, ?string $value = null, ?\DateTime $expires = null)
    {
        $this->key = $key;
        $this->value = $value ?? $_COOKIE[$key] ?? null;
        $this->expires = $expires;
    }

    public function save(): void
    {
        if (headers_sent()) {
            throw new \Exception('Cannot modify header information - headers already sent');
        }
        $_COOKIE[$this->key] = $this->value;
        setcookie($this->key, $this->value, $this->expires ? $this->expires->getTimestamp() : 0, '/');
    }

    public function setExpires(\DateTime $expires): static
    {
        $this->expires = $expires;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }
}
