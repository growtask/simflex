<?php

namespace Simflex\Core\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Simflex\Core\Container;

class FileLogger extends AbstractLogger
{
    protected function getFilePath(): ?string
    {
        if (!is_dir($rootDir = Container::getConfig()->logPath)) {
            if (!@mkdir($rootDir, 0700, true)) {
                return null;
            }
        }

        return $rootDir . '/' . date('Y-m-d') . '.log';
    }

    protected function toString($object): string
    {
        if (is_array($object) || is_object($object)) {
            $object = var_export($object, true);
        }

        return str_replace("\n", ' ', (string)$object);
    }

    protected function interpolate($message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $this->toString($val);
        }

        return strtr($message, $replace);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if ($level == LogLevel::DEBUG && !Container::getConfig()->devMode) {
            return;
        }

        $filePath = $this->getFilePath();
        if (!$filePath) {
            return;
        }

        $str = date('Y-m-d H:i:s') . " [$level] " . $this->interpolate($message, $context) . "\n";
        @file_put_contents($filePath, $str, FILE_APPEND);
    }
}