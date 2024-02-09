<?php
namespace Simflex\Admin\Migration\Exception;

class FieldAlreadyExistsException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = 'Field ' . $this->message . ' already exists in the table';
    }
}