<?php

namespace Simflex\Core;

use Simflex\Core\Routing\Route;

class Request implements \Simflex\Core\DI\Service
{
    protected bool $isHttps;
    protected string $host;
    protected string $requestMethod;
    protected array $headers;
    protected array $getParams;
    protected array $postParams;
    protected array $reqParams;
    protected array $cookies;
    protected array $files;
    protected string $requestBody;
    protected string $urlPath;
    protected array $urlParts;
    protected array $serverInfo = [];
    protected ?Route $route = null;

    public static function getServiceName(): string
    {
        return 'request';
    }

    /**
     * Request constructor.
     */
    public function __construct()
    {
        // cache values
        $this->host = $_SERVER['HTTP_HOST'];
        $this->isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->reqParams = $_REQUEST;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->requestBody = file_get_contents('php://input');

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                // convert HTTP_X_X to x-x
                $this->headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
            }
        }

        // parse and compose URL
        $this->setPath(parse_url($_SERVER['REQUEST_URI'])['path'] ?? '/');
    }

    public function setRoute(Route $route): void
    {
        $this->route = $route;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
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
     * Returns full URL path
     * @return string
     */
    public function getPath(): string
    {
        return $this->urlPath;
    }

    /**
     * Returns broken down URL parts
     * @return array
     */
    public function getUrlParts(): array
    {
        return $this->urlParts;
    }

    /**
     * Returns last part of URL path
     * @return string  Last part of URL path
     */
    public function getUrlLastPart(): string
    {
        return $this->urlParts[count($this->urlParts) - 2] ?? '/';
    }

    /**
     * Checks if the request was POST
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->requestMethod == 'POST';
    }

    /**
     * Checks if the request body is JSON data
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->header('Content-Type') == 'application/json';
    }

    /**
     * Checks if the request was AJAX
     * @return bool
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
     * Gets GET parameters
     *
     * @param mixed|null $k
     * @return array|mixed|null
     */
    public function get(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->getParams[$k] ?? $default) : $this->getParams;
    }

    /**
     * Gets POST parameters
     *
     * @param mixed|null $k
     * @return array|mixed|null
     */
    public function post(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->postParams[$k] ?? $default) : $this->postParams;
    }

    /**
     * Gets REQUEST parameters
     *
     * @param mixed|null $k
     * @return array|mixed|null
     */
    public function request(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->reqParams[$k] ?? $default) : $this->reqParams;
    }

    /**
     * Gets input cookies
     *
     * @param mixed|null $k
     * @return array|mixed|null
     */
    public function cookie(int|string|null $k = null, mixed $default = null): mixed
    {
        return $k ? ($this->cookies[$k] ?? $default) : $this->cookies;
    }

    /**
     * Gets input files
     *
     * @param mixed|null $k
     * @return array|mixed|null
     */
    public function file(int|string|null $k = null): mixed
    {
        return $k ? ($this->files[$k] ?? null) : $this->files;
    }

    /**
     * Returns input headers
     *
     * @param string|null $k
     * @return mixed|null
     */
    public function header(?string $k = null): mixed
    {
        return $k ? ($this->headers[strtolower($k)] ?? null) : $this->headers;
    }

    /**
     * @return bool
     */
    public function isHttps(): bool
    {
        return $this->isHttps;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return array
     */
    public function getServerInfo(): array
    {
        return $this->serverInfo;
    }

    public function getBody(): string
    {
        return $this->requestBody;
    }

    /**
     * Builds query from existing GET parameters.
     * @param array $extra Extra parameters
     * @param array $ignore Ignore keys
     * @return string Query
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