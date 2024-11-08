<?php

namespace Simflex\Core\Console;

use Simflex\Core\Request;

class CliRequest extends Request
{
    protected array $argv = [];

    public function __construct()
    {
        // set parameters for CLI request
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['DOCUMENT_ROOT'] = SF_ROOT_PATH;
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // parse commandline options
        global $argv;
        $_REQUEST = [];

        $args = array_slice($argv, 1);
        foreach ($args as $i => $arg) {
            if (!str_starts_with($arg, '--')) {
                $_REQUEST[$i] = $arg;
            } else {
                $data = explode('=', $arg);
                $_REQUEST[trim($data[0], '-')] = $data[1];
            }
        }

        // legacy: set GET, POST, REQUEST for compatibility
        $_GET = $_POST = $_REQUEST;
        $this->argv = $_REQUEST;

        parent::__construct();
    }

    /**
     * Get CLI argument
     * @param int|string|null $key Argument key
     * @param mixed $default Default value
     * @return array|string|null Argument value
     */
    public function arg(int|string|null $key = null, mixed $default = null): array|string|null
    {
        return is_null($key) ? $this->argv : $this->argv[$key] ?? $default;
    }
}