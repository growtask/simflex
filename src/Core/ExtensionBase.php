<?php

namespace Simflex\Core;

abstract class ExtensionBase implements HasAdminParams
{
    protected string $root = '';

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

    protected function root(): string
    {
        if (!$this->root) {
            $this->root = '/';

            $ext = substr(get_class($this), 3);
            if (preg_match('@^([A-Z]+[a-z]+)[A-Z]+@', $ext, $a)) {
                $ext = $a[1];
            }
            $ext = strtolower($ext);

            $menu = Core::menu('by_ext');
            if (isset($menu[$ext]) && is_array($menu[$ext])) {
                $this->root = $menu[$ext][0]['link'];
                $n = count($menu[$ext]);
                for ($i = 1; $i < $n; $i++) {
                    if (mb_strlen($menu[$ext][$i]['link']) < mb_strlen($this->root)) {
                        $this->root = $menu[$ext][$i]['link'];
                    }
                }
            }
        }
        return $this->root;
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

    protected function link($link): string
    {
        return str_replace('//', '/', $this->root() . $link);
    }
}
