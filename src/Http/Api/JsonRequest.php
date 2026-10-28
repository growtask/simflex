<?php

namespace Simflex\Http\Api;

use Simflex\Http\Request;

class JsonRequest extends Request
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Возвращает JSON в виде массива
     */
    public function json(): array
    {
        return json_decode($this->requestBody, true) ?: [];
    }
}
