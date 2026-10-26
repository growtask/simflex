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
        if (!is_dir($path . '/provider/extensions')) {
            echo "[!] Not a Simflex package, skipping.\n";
            return;
        }

        $root = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        // копируем файлы
        foreach (static::copyMigrations($path . '/database/migrations', $root . '/database/migrations') as $from => $to) {
            echo "[*] Installing migration $from as $to...\n";
        }
        static::copyContents($path . '/provider/extensions', $root . '/provider/extensions', 'extension');

        // очищаем кэш
        if (is_file($root . '/cache/extensions.php')) {
            unlink($root . '/cache/extensions.php');
        }

        if (is_file($root . '/cache/files.php')) {
            unlink($root . '/cache/files.php');
        }

        echo "[+] Package has been installed.\n";
    }

    /**
     * Копирует миграции пакета в сайт следующими свободными номерами сайта: `01_catalog_category.php` пакета
     * становится, например, `29_catalog_category.php`. Миграция, которая уже есть в сайте под любым номером
     * (то же имя без номера), не копируется — повторная установка и обновление пакета её не дублируют.
     *
     * @return array<string, string> имя в пакете => имя в сайте, только скопированные
     */
    public static function copyMigrations(string $from, string $to): array
    {
        if (!is_dir($from) || !is_dir($to)) {
            return [];
        }

        $installed = [];
        $last = 0;
        $width = 2;
        foreach (scandir($to) as $file) {
            if (preg_match('/^(\d+)_(.+\.php)$/', $file, $m)) {
                $installed[$m[2]] = true;
                $last = max($last, (int)$m[1]);
                $width = max($width, strlen($m[1]));
            }
        }

        $copied = [];
        $files = array_filter(scandir($from), fn(string $file) => is_file("$from/$file") && str_ends_with($file, '.php'));
        sort($files);
        foreach ($files as $file) {
            $name = preg_replace('/^\d+_/', '', $file);
            if (isset($installed[$name])) {
                continue;
            }
            $target = sprintf("%0{$width}d_%s", ++$last, $name);
            copy("$from/$file", "$to/$target");
            $installed[$name] = true;
            $copied[$file] = $target;
        }

        return $copied;
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
