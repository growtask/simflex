<?php

namespace Simflex\Extensions\Install;

use Composer\Installer\PackageEvent;

class PackageInstaller
{
    public static function onPackageEvent(PackageEvent $event): void
    {
        // get package
        $operation = $event->getOperation();
        if (method_exists($operation, 'getPackage')) {
            $package = $operation->getPackage();
        } elseif (method_exists($operation, 'getTargetPackage')) {
            $package = $operation->getTargetPackage();
        }

        if (!isset($package) || !$package) {
            echo "[!] Unable to get package\n";
            return;
        }

        // check if it's a Simflex package
        $path = $event->getComposer()->getInstallationManager()->getInstallPath($package);
        if (!is_dir($path . '/provider/extension')) {
            echo "[!] Not a Simflex package\n";
            return;
        }

        $root = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        // copy files
        static::copyContents($path . '/database/migrations', $root . '/database/migrations');
        static::copyContents($path . '/database/seeders', $root . '/database/seeders');
        static::copyContents($path . '/provider/extension', $root . '/provider/extension');

        // clear cache
        if (is_file($root . '/cache/extensions.php')) {
            unlink($root . '/cache/extensions.php');
        }

        if (is_file($root . '/cache/files.php')) {
            unlink($root . '/cache/files.php');
        }

        echo "[+] Processed package\n";
    }

    protected static function copyContents(string $from, string $to): void
    {
        if (!is_dir($from) || !is_dir($to)) {
            echo '[!] Not found: ' . $from . ' or ' . $to . "\n";
            return;
        }

        echo '[*] Copying ' . $from . ' to ' . $to . "\n";
        foreach (scandir($from) as $file) {
            copy($from . '/' . $file, $to . '/' . $file);
        }
    }
}