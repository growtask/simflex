<?php

namespace Simflex\Core;

abstract class ExtensionBase implements HasAdminParams
{
    /**
     * Путь до расширения в файловой системе
     * @var string
     */
    protected string $rootDir;
    protected string $root = '';

    /**
     * Title in the admin panel lists
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

    public function __construct()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $this->rootDir = dirname($reflector->getFileName());
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
     * Renders the template into a string, $this in the template is the extension
     *
     * @param string $file Absolute path to the template
     * @param array<string, mixed> $vars Variables available in the template as $name
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
