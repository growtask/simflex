<?php

namespace Simflex\Http\Api;

use Simflex\Core\Errors\Error;
use Simflex\Core\Errors\ErrorCodes;
use Simflex\Http\Response;

class JsonResponse extends Response
{
    protected int $errorCode = 0;
    protected string $errorMessage = '';
    protected array $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->setContentType('application/json');
    }

    /**
     * @param mixed $mixed Скаляр, ассоциативный массив или Throwable
     * @throws \Simflex\Core\Errors\Error
     */
    public function setData(mixed $mixed): static
    {
        if (is_scalar($mixed)) {
            $this->set('result', $mixed);
            return $this;
        }

        if (is_array($mixed)) {
            $this->data = $mixed;
            return $this;
        }

        if (is_object($mixed)) {
            if ($mixed instanceof \Throwable) {
                $this->setError($mixed);
                return $this;
            }

            if (method_exists($mixed, 'toArray')) {
                $this->data = $mixed->toArray();
                return $this;
            }

            throw Error::byCode(ErrorCodes::APP_UNSUPPORTED_RESPONSE_TYPE, null, ['class' => get_class($mixed)]);
        }

        throw Error::byCode(
            ErrorCodes::APP_UNSUPPORTED_RESPONSE,
            null,
            ['content' => substr(var_export($mixed, true), 0, 200)]
        );
    }

    protected function makeBody(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Устанавливает значение в data по ключу
     *
     * @param int|string $key Ключ
     * @param mixed $value Значение
     */
    public function set(int|string $key, mixed $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Устанавливает ошибку из Throwable
     *
     * @param \Throwable $err Исключение, из которого берется ошибка
     */
    public function setError(\Throwable $err, bool $overwriteStatusCode = true): static
    {
        // переопределяем код статуса, если другой еще не задан
        if ($this->statusCode === 200 && $overwriteStatusCode) {
            $this->statusCode = ErrorCodes::getHttpStatusCode(intval($err->getCode())) ?? 500;
        }

        $this->errorCode = intval($err->getCode());
        $this->errorMessage = $err->getMessage();
        return $this;
    }

    /**
     * Возвращает массив с текущей ошибкой и данными
     */
    public function toArray(): array
    {
        return [
                'error' => [
                    'code' => $this->errorCode,
                    'message' => $this->errorMessage,
                ],
            ] + $this->data;
    }
}
