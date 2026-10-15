<?php

namespace Simflex\Core;

use JetBrains\PhpStorm\NoReturn;

abstract class Response implements \Simflex\Core\DI\Service
{
    protected int $statusCode = 200;
    protected array $cookies = [];
    protected array $headers = [];

    public function __construct()
    {
    }

    public static function getServiceName(): string
    {
        return 'response';
    }

    /**
     * Sets redirect url
     * @param string $url URL to redirect to
     */
    public function redirect(string $url): static
    {
        $this->setHeader('Location', $url);
        return $this;
    }

    /**
     * Redirect and quit
     * @param string $url URL to redirect to
     */
    #[NoReturn]
    public function redirectNow(string $url): never
    {
        $this->redirect($url);
        exit;
    }

    /**
     * Sets the cookie
     *
     * @param string $name Name
     * @param string $value Value
     * @param array $options Options
     */
    public function setCookie(string $name, string $value = '', array $options = []): static
    {
        $this->cookies[$name] = [
            'value' => $value,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * Sets the header
     *
     * @param string $name Name
     * @param string $value Value
     */
    public function setHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Sends a header with status code
     * @param int $code Status code string
     */
    public function setStatusCode(int $code): static
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Sets the content type
     * @param string $contentType Content type
     */
    public function setContentType(string $contentType): static
    {
        $this->setHeader('Content-Type', $contentType);
        return $this;
    }

    /**
     * Sets the content disposition header
     * @param string $fileName File name
     */
    public function setContentDisposition(string $fileName): static
    {
        $this->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        return $this;
    }

    /**
     * Utility function to send a file
     *
     * @param string $fileName File name
     * @param string $fileContent Content of the file
     */
    public function outputFile(string $fileName, string $fileContent): void
    {
        $this->outputHeaders();

        header('Content-Length: ' . strlen($fileContent));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: ' . $fileName);

        echo $fileContent;
    }

    public function output(): void
    {
        $this->outputHeaders();
        echo $this->makeBody();
    }

    protected function outputHeaders(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->cookies as $cookieName => $cookie) {
            setcookie($cookieName, $cookie['value'], $cookie['options']);
        }

        foreach ($this->headers as $header => $value) {
            header($header . ': ' . $value);
        }
    }

    abstract protected function makeBody(): string;
}
