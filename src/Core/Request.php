<?php

namespace Simflex\Core;

use Simflex\Core\Routing\Route;

class Request implements \Simflex\Core\DI\Service
{
    protected bool $isHttps;
    protected string $host;
    protected string $requestMethod;
    protected array $headers = [];
    protected array $getParams;
    protected array $postParams;
    protected array $reqParams;
    protected array $cookies;
    protected array $files;
    protected string $requestBody;
    protected string $urlPath;
    protected array $urlParts;
    protected array $serverInfo = [];
    public ?Route $route = null;

    public static function getServiceName(): string
    {
        return 'request';
    }

    /**
     * Конструктор Request.
     */
    public function __construct()
    {
        // кешируем значения
        $this->host = (string)($_SERVER['HTTP_HOST'] ?? '');
        $this->isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        $this->requestMethod = (string)($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->reqParams = $_REQUEST;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->requestBody = (string)file_get_contents('php://input');

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                // превращаем HTTP_X_X в x-x
                $this->headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
            }
        }

        // разбираем и собираем URL
        $this->setPath(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
    }

    public function setPath(string $path): void
    {
        $this->urlPath = $path;
        $this->urlParts = array_slice(explode('/', $this->urlPath), 1);
        $this->serverInfo = $_SERVER;
    }

    public function getFullUrl(): string
    {
        return ($this->isHttps ? 'https://' : 'http://') . $this->host . $this->urlPath;
    }

    /**
     * Возвращает полный путь URL
     */
    public function getPath(): string
    {
        return $this->urlPath;
    }

    /**
     * Возвращает URL, разобранный на части
     */
    public function getUrlParts(): array
    {
        return $this->urlParts;
    }

    /**
     * Возвращает последнюю часть пути URL
     * @return string  Последняя часть пути URL
     */
    public function getUrlLastPart(): string
    {
        return $this->urlParts[count($this->urlParts) - 2] ?? '/';
    }

    /**
     * Проверяет, что запрос - POST
     */
    public function isPost(): bool
    {
        return $this->requestMethod == 'POST';
    }

    /**
     * Проверяет, что тело запроса - JSON
     */
    public function isJson(): bool
    {
        return $this->header('Content-Type') == 'application/json';
    }

    /**
     * Проверяет, что запрос - AJAX
     */
    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') == 'XMLHttpRequest';
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * Параметры GET
     */
    public function get(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->getParams[$k] ?? $default) : $this->getParams;
    }

    /**
     * Параметры POST
     */
    public function post(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->postParams[$k] ?? $default) : $this->postParams;
    }

    /**
     * Параметры REQUEST
     */
    public function request(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->reqParams[$k] ?? $default) : $this->reqParams;
    }

    /**
     * Входящие cookies
     */
    public function cookie(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->cookies[$k] ?? $default) : $this->cookies;
    }

    /**
     * Входящие файлы
     */
    public function file(int|string|null $k = null): mixed
    {
        return $k ? ($this->files[$k] ?? null) : $this->files;
    }

    /**
     * Входящие заголовки
     */
    public function header(?string $k = null): mixed
    {
        return $k ? ($this->headers[strtolower($k)] ?? null) : $this->headers;
    }

    public function isHttps(): bool
    {
        return $this->isHttps;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getServerInfo(): array
    {
        return $this->serverInfo;
    }

    public function getBody(): string
    {
        return $this->requestBody;
    }

    /**
     * Собирает строку запроса из текущих GET-параметров.
     * @param array $extra Дополнительные параметры
     * @param array $ignore Ключи, которые нужно пропустить
     * @return string Строка запроса
     */
    public function buildQuery(array $extra = [], array $ignore = []): string
    {
        $get = [];
        foreach ($this->getParams as $key => $value) {
            if (!in_array($key, $ignore)) {
                $get[$key] = $value;
            }
        }

        foreach ($extra as $key => $value) {
            $get[$key] = $value;
        }

        return http_build_query($get);
    }
}
