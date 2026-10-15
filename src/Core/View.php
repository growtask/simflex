<?php

namespace Simflex\Core;

/**
 * Template rendering. The only place where template output is buffered:
 * components, modules, widgets, fields and admin pages return strings, and output
 * goes to the browser only from the system layer (Response, layout).
 */
final class View
{
    /**
     * Renders the template file into a string
     *
     * @param string $file Absolute path to the template
     * @param array<string, mixed> $vars Variables available in the template as $name
     * @param object|null $context Object available in the template as $this
     */
    public static function render(string $file, array $vars = [], ?object $context = null): string
    {
        $render = function (string $__file, array $__vars): string {
            extract($__vars, EXTR_SKIP);
            $__level = ob_get_level();
            ob_start();
            try {
                include $__file;
            } catch (\Throwable $e) {
                while (ob_get_level() > $__level) {
                    ob_end_clean();
                }
                throw $e;
            }
            return (string)ob_get_clean();
        };

        if ($context) {
            $render = \Closure::bind($render, $context, $context::class);
        }

        return $render($file, $vars);
    }
}
