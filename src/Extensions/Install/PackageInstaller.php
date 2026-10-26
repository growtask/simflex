<?php

namespace Simflex\Extensions\Install;

use Composer\Installer\PackageEvent;

class PackageInstaller
{
    public static function onPackageEvent(PackageEvent $event): void
    {
        // получаем пакет
        $operation = $event->getOperation();
        if (method_exists($operation, 'getPackage')) {
            $package = $operation->getPackage();
        } elseif (method_exists($operation, 'getTargetPackage')) {
            $package = $operation->getTargetPackage();
        }

        if (!isset($package) || !$package) {
            echo "[!] Unable to get package information. Check your composer installation!\n";
            return;
        }

        // проверяем, что это пакет Simflex
        $path = $event->getComposer()->getInstallationManager()->getInstallPath($package) . '/src';
        if (!is_dir($path . '/provider/extension')) {
            echo "[!] Not a Simflex package, skipping.\n";
            return;
        }

        $root = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        // копируем файлы
        static::copyContents($path . '/database/migrations', $root . '/database/migrations', 'migration');
        static::copyContents($path . '/provider/extension', $root . '/provider/extension', 'extension');

        // очищаем кэш
        if (is_file($root . '/cache/extensions.php')) {
            unlink($root . '/cache/extensions.php');
        }

        if (is_file($root . '/cache/files.php')) {
            unlink($root . '/cache/files.php');
        }

        echo "[+] Package has been installed.\n";
    }

    protected static function copyContents(string $from, string $to, string $type): void
    {
        if (!is_dir($from) || !is_dir($to)) {
            return;
        }

        foreach (scandir($from) as $file) {
            if (!is_file($from . '/' . $file)) {
                continue;
            }

            echo "[*] Installing $type $file...\n";
            copy($from . '/' . $file, $to . '/' . $file);
        }
    }
}
