<?php

namespace Simflex\Core\System;

use Simflex\Core\Http\View;
use Simflex\Core\Admin\HasAdminParams;

abstract class ExtensionBase implements HasAdminParams
{
    /**
     * Название в списках админки
     */
    public static function extensionTitle(): string
    {
        $parts = explode('\\', static::class);
        return end($parts);
    }

    public static function adminParams(): array
    {
        return [];
    }

    /**
     * Каталог класса расширения в файловой системе. Не зависит от конструктора: наследники объявляют свой
     * конструктор без вызова родительского
     */
    protected function getRootDir(): string
    {
        return dirname((new \ReflectionClass($this))->getFileName());
    }

    /**
     * Рендерит шаблон в строку, $this в шаблоне - расширение
     *
     * @param string $file Абсолютный путь к шаблону
     * @param array<string, mixed> $vars Переменные, доступные в шаблоне как $name
     */
    protected function render(string $file, array $vars = []): string
    {
        return View::render($file, $vars, $this);
    }
}
