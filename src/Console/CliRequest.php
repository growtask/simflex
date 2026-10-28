<?php

namespace Simflex\Core\Console;

use Simflex\Core\System\Container;
use Simflex\Core\Http\Request;

class CliRequest extends Request
{
    protected array $argv = [];

    public function __construct()
    {
        // задаем параметры CLI-запроса
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['DOCUMENT_ROOT'] = Container::getPaths()->getPublic();
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        parent::__construct();

        // параметры командной строки: позиционные - по номеру, --key=value - по имени; доступны и как GET, POST,
        // REQUEST запроса
        global $argv;
        $args = [];
        foreach (array_slice($argv ?? [], 1) as $i => $arg) {
            if (!str_starts_with($arg, '--')) {
                $args[$i] = $arg;
            } else {
                $data = explode('=', $arg, 2);
                $args[trim($data[0], '-')] = $data[1] ?? '';
            }
        }
        $this->argv = $this->getParams = $this->postParams = $this->reqParams = $args;
    }

    /**
     * Аргумент CLI
     * @param int|string|null $key Ключ аргумента
     * @param mixed $default Значение по умолчанию
     * @return array|string|null Значение аргумента
     */
    public function arg(int|string|null $key = null, mixed $default = null): array|string|null
    {
        return is_null($key) ? $this->argv : $this->argv[$key] ?? $default;
    }
}
