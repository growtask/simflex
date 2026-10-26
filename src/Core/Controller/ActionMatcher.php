<?php

namespace Simflex\Core\Controller;

use Simflex\Core\Container;

/**
 * Связывает Action и имя метода.
 *
 * Разделители:
 * - `:` - начало извлекаемой части пути (например, для `/path/:name/to/something/` и пути
 *   `/path/hello/to/something/` получится `name` = `hello`)
 *
 * @see Action
 */
class ActionMatcher
{
    protected array $vars = [];

    public function __construct(
        public Action $action,
        public string $methodName
    ) {
    }

    /**
     * @return array Извлеченные переменные
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @return bool Есть ли обычные параметры (`:example`)
     */
    public function hasPlaceholders(): bool
    {
        return strpos($this->action->path, ':') !== false;
    }

    /**
     * Пытается сопоставить с текущим путем
     */
    public function match(): bool
    {
        $request = Container::getRequest();

        // сверяем метод, если задан не "all"
        if ($this->action->httpMethod != 'all' && $request->getRequestMethod() != $this->action->httpMethod) {
            return false;
        }

        $route = $request->route;
        $path = trim($request->getPath(), '/');

        // заменяем часть маршрута в пути
        $path = trim(substr($path, strlen($route->getBaseUri())), '/');
        $myPath = trim($this->action->path, '/');

        // быстрая проверка
        if ($path == $myPath) {
            return true;
        }

        // разбиваем пути на части
        $pathParts = explode('/', $path);
        $parts = explode('/', $myPath);

        // сверяем полный путь
        return $this->matchFull($parts, $pathParts);
    }

    protected function matchFull(array $parts, array $pathParts): bool
    {
        // проверяем, совпадает ли вообще количество частей
        if (count($parts) !== count($pathParts)) {
            return false;
        }

        // пробуем сопоставить
        for ($i = 0; $i < count($parts); ++$i) {
            $part = $parts[$i];
            $pathPart = $pathParts[$i];

            // пустых частей не должно быть ни в действии, ни в реальном пути
            if (empty($part) || empty($pathPart)) {
                return false;
            }

            // проверяем, нужно ли извлечь эту часть
            if ($part[0] == ':') {
                $varName = substr($part, 1);
                $this->vars[$varName] = $pathPart;
                continue;
            }

            // проверяем строгое совпадение
            if ($part != $pathPart) {
                return false;
            }
        }

        return true;
    }
}
