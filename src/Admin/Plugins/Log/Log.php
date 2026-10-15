<?php

namespace Simflex\Admin\Plugins\Log;

use Simflex\Core\DB;

class Log
{

    public static function a(string $action, string $data): void
    {
        DB::query(
            "INSERT INTO log (action, ip, browser, data) VALUES (?, ?, ?, ?)",
            [$action, (string)($_SERVER['REMOTE_ADDR'] ?? ''), (string)($_SERVER['HTTP_USER_AGENT'] ?? ''), $data]
        );
    }

}
