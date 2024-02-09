<?php

namespace Simflex\Admin\Migration\Exception;

class FieldTypeNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = 'Field type ' . $this->message . ' was not found';
    }
}