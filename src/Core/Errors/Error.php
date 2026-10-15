<?php

namespace Simflex\Core\Errors;

use RuntimeException;
use Simflex\Core\Log;
use Simflex\Core\Errors\ErrorCodes;
use Throwable;

/**
 * Class Error
 *
 */
class Error extends \Error
{

    /**
     * @var array Дополнительные необязательные параметры, например, для кастомизации текста ошибки.
     */
    public array $params = [];

    /** @inheritDoc */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $params = [])
    {
        parent::__construct($message, $code, $previous);
        $this->params = $params;
    }

    /**
     * @param string $message Например {code, -10} Пользователь не найден
     */
    public static function createFromMessage(string $message): ?static
    {
        $matches = [];
        if (preg_match('#{error,\s*-?([0-9]+)}\s*(.*)#', $message, $matches)) {
            return new static($matches[2], (int)$matches[1]);
        }
        return null;
    }

    public function toString(bool $withCode = false): string
    {
        $ret = $this->message;
        if ($withCode && $this->code !== null) {
            $ret = "{error, $this->code} $this->message";
        }
        return $ret;
    }

    /**
     * Бросить исключение определенного класса
     */
    public function throwCustom(string $exceptionClass = RuntimeException::class): never
    {
        throw new $exceptionClass($this->message, $this->code);
    }

    /**
     * @param int $code from ErrorCodes
     */
    public static function byCode(int $code, ?Throwable $previous = null, array $params = []): Error
    {
        $class = Error::class;
        $error = new $class(ErrorCodes::getText($code, false, $params), $code, $previous);
        $error->params = $params;
        return $error;
    }

    public function toArray(): array
    {
        return static::toArrayThrowableInner($this) + [
                'params' => $this->params,
            ];
    }

    protected static function toArrayThrowableInner(Throwable $e): array
    {
        return [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'file' => $e->getFile() . ':' . $e->getLine(),
            'previous' => $e->getPrevious() ? [
                'code' => $e->getPrevious()->getCode(),
                'message' => $e->getPrevious()->getMessage(),
                'class' => get_class($e->getPrevious()),
                'file' => $e->getPrevious()->getFile() . ':' . $e->getPrevious()->getLine()
            ] : null,
        ];
    }

    public static function toArrayThrowable(Throwable $e): array
    {
        if ($e instanceof self) {
            return $e->toArray();
        }
        return self::toArrayThrowableInner($e);
    }

    public static function serializeThrowable(Throwable $e): string
    {
        return (string)json_encode(self::toArrayThrowable($e), JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public function serialize(): string
    {
        return (string)json_encode($this->toArray(), JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * Send this error to logs
     */
    public function report(): void
    {
        $level = 'error';
        Log::$level($this->getMessage());
    }

    public static function byThrowable(Throwable $throwable): static
    {
        if ($throwable instanceof static) {
            return clone $throwable;
        }
        return new static(
            $throwable->getMessage(),
            $throwable->getCode() ?: ErrorCodes::APP_INTERNAL_ERROR,
            $throwable->getPrevious(),
            ($throwable instanceof self) ? $throwable->params : []
        );
    }
}
