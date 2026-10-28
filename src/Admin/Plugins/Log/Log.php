<?php

namespace Simflex\Core\Admin\Plugins\Log;

use Simflex\Core\System\Container;
use Simflex\Core\Models\Log as LogModel;

class Log
{
    public static function a(string $action, string $data): void
    {
        LogModel::insertStatic([
            'action' => $action,
            'ip' => (string)Container::getRequest()?->server('REMOTE_ADDR', ''),
            'browser' => (string)Container::getRequest()?->getUserAgent(),
            'data' => $data,
        ]);
    }
}
