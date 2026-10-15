<?php

namespace Simflex\Admin\Plugins\Log;

use Simflex\Core\DB;

class Log
{

    public static function a(string $action, string $data): void
    {
        $action = DB::escape($action);
        $data = DB::escape($data);
        $browser = DB::escape($_SERVER['HTTP_USER_AGENT']);
        $set = ["action = '$action', ip = '{$_SERVER['REMOTE_ADDR']}', browser = '$browser'"];
        $set[] = "data = '$data'";
        $q = "INSERT INTO log SET " . implode(', ', $set);
        DB::query($q);
    }

}
