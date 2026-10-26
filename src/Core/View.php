<?php

namespace Simflex\Core;

/**
 * Рендеринг шаблонов. Единственное место, где буферизуется вывод шаблона:
 * компоненты, модули, виджеты, поля и страницы админки возвращают строки, а вывод
 * уходит в браузер только из системного слоя (Response, layout).
 */
final class View
{
    /**
     * Рендерит файл шаблона в строку
     *
     * @param string $file Абсолютный путь к шаблону
     * @param array<string, mixed> $vars Переменные, доступные в шаблоне как $name
     * @param object|string|null $context Объект, доступный в шаблоне как $this,
     *                                    или имя класса для self:: и static:: в шаблоне
     */
    public static function render(string $file, array $vars = [], object|string|null $context = null): string
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

        if (is_object($context)) {
            $render = \Closure::bind($render, $context, $context::class);
        } elseif ($context) {
            $render = \Closure::bind($render, null, $context);
        }

        return $render($file, $vars);
    }
}
