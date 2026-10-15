<?php

namespace Simflex\Admin;

use Simflex\Admin\PlugFS;
use Simflex\Admin\Plugins\Alert\Alert;;
use Simflex\Core\DB;

abstract class InstallBase {

    const TYPE_EXT = 0;
    const TYPE_PLUG = 1;

    public int $type = self::TYPE_EXT;
    public bool $hasSetup = false;
    public string $destDir;
    protected string $installDir;

    public function __construct($istallDir) {
        $this->installDir = $istallDir;
    }

    public function install(): bool {
        $success = $this->copyFiles() && $this->initDB();
        return $success;
    }

    protected function copyFiles(): mixed {
        $type = $this->type == self::TYPE_EXT ? 'ext' : 'plug';
        $dest = "{$_SERVER['DOCUMENT_ROOT']}/$type/$this->destDir";
        if (is_dir($dest)) {
            Alert::error("Папка $dest уже существует");
        }
        $success = PlugFS::moveDir($this->installDir . '/data', $dest);
        return $success;
    }

    protected function initDB(): bool {
        return true;
    }

    protected static function queryFromFile(string $file, array $replaces = [], bool $debug = false): int|bool {
        $success = true;
        $qr = file_get_contents($file);
        if (count($replaces)) {
            $qr = str_replace(array_keys($replaces), array_values($replaces), $qr);
        }
        $qs = array_filter(array_map('trim', explode('---', $qr)));

        foreach ($qs as $q) {
            $success &= DB::query($q);
            if (!$success && $debug) {
                echo $q;
                die;
            }
        }
        return $success;
    }

}
