<?php

namespace Simflex\Core;

/**
 * CURL helper class
 */
class Curl
{
    /**
     * @var string Request method
     */
    public string $method = 'GET';

    /**
     * @var string Request body
     */
    public string $body = '';

    /**
     * @var bool Whether to return response
     */
    public bool $returnTransfer = true;

    /**
     * @var array HTTP headers
     */
    public array $headers = [];

    /**
     * @param string $url Target URL
     */
    public function __construct(public string $url)
    {
    }

    /**
     * Sets body to comply with application/x-www-form-urlencoded
     * @param array $post Post fields
     * @return void
     */
    public function setPostBody(array $post): void
    {
        $this->body = http_build_query($post);
    }

    /**
     * Sets body to comply with application/json
     * @param array $json JSON data
     * @return void
     */
    public function setJsonBody(array $json): void
    {
        $this->headers[] = 'Content-Type: application/json';
        $this->body = json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Sends the request
     * @return string|null Returns the response if returnTransfer is set to true and response wasn't false, otherwise null
     */
    public function send(): ?string
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, $this->returnTransfer);
        curl_setopt($c, CURLOPT_HTTPHEADER, $this->headers);

        if ($this->method != 'GET') {
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, $this->method);
            curl_setopt($c, CURLOPT_POSTFIELDS, $this->body);
        }

        $ret = curl_exec($c);
        curl_close($c);

        return $this->returnTransfer && $ret !== false ? $ret : null;
    }
}