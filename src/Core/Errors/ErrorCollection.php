<?php

namespace Simflex\Core\Errors;

/**
 * Class ErrorCollection реализует интерфейс массива. Представляет собой список ошибок.
 * Можно например так
 * $errors = new ErrorCollection([Error::byCode(101)]);
 * $errors[101]->getMessage();
 * throw $errors[101];
 */
class ErrorCollection implements \ArrayAccess
{
    /**
     * @var Error[]
     */
    protected array $errors = [];

    /**
     *
     * @param Error[] $errors
     */
    public function __construct(array $errors = [])
    {
        foreach ($errors as $e) {
            $this->add($e);
        }
    }

    public function add(Error $e): void
    {
        $this->errors[$e->getCode()] = $e;
    }

    public function getFirst(): ?Error
    {
        return $this->errors ? reset($this->errors) : null;
    }

    public function __get($key): mixed
    {
        return $this->errors[$key];
    }

    public function __set($key, $value): void
    {
        $this->errors[$key] = $value;
    }

    public function __isset($key): bool
    {
        return isset($this->errors[$key]);
    }

    public function __unset($key): void
    {
        unset($this->errors[$key]);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->errors[] = $value;
        } else {
            $this->errors[$offset] = $value;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->errors[$offset]);
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->errors[$offset]);
        }
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ? $this->errors[$offset] : null;
    }
}
