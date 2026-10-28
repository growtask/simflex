<?php

namespace Simflex\Admin\Plugins\Log;

use Simflex\Models\Log as LogModel;

class Log
{
    public static function a(string $action, string $data): void
    {
        LogModel::insertStatic([
            'action' => $action,
            'ip' => (string)($_SERVER['REMOTE_ADDR'] ?? ''),
            'browser' => (string)($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'data' => $data,
        ]);
    }
}
