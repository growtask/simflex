<?php

namespace Simflex\Http\Routing;

use Simflex\Core\Container;

class Resolver
{
    /** @var array<string, array{class: string}> */
    protected array $menu = [];

    public function resolve(string|array $routesFile): Route
    {
        if (is_array($routesFile)) {
            $routes = $routesFile;
        } else {
            $routes = include $routesFile;
        }

        $request = Container::getRequest();
        if ($route = & $routes[trim($request->getPath(), '/')]) {
            return static::makeRoute($route, trim($request->getPath(), '/'));
        }

        $uri = $request->getUrlParts();
        $path = '';
        $uris = [];
        foreach ($uri as $u) {
            if ($u) {
                $path .= '/' . $u;
                $uris[] = $path;
            }
        }

        foreach (array_reverse($uris) as $path) {
            if ($route = & $routes[$path]) {
                return static::makeRoute($route, $path);
            }
        }

        if ($resolved = $this->resolveByMenu()) {
            return static::makeRoute($resolved['class'], $resolved['path']);
        }
        if ($routeDefault = $routes['/'] ?? null) {
            return static::makeRoute($routeDefault, '/');
        }
        if ($classDefault = Container::getConfig()->defaultComponent) {
            return static::makeRoute($classDefault, '/');
        }
        throw new \Exception("Can't resolve route {$request->getPath()}");
    }

    /**
     * @param array<string, array{class: string}> $menu Пункты меню сайта с компонентом: ссылка => пункт
     */
    public function setMenu(array $menu): static
    {
        $this->menu = $menu;
        return $this;
    }

    /**
     * Маршрутизация по меню сайта: самый глубокий пункт меню с компонентом, ссылка которого - начало пути запроса
     * @return array{class: string, path: string}|null
     */
    protected function resolveByMenu(): ?array
    {
        $path = '/';
        $found = isset($this->menu['/']) ? ['class' => $this->menu['/']['class'], 'path' => '/'] : null;
        foreach (Container::getRequest()->getUrlParts() as $part) {
            if ($part === '') {
                continue;
            }
            $path .= $part . '/';
            if (isset($this->menu[$path])) {
                $found = ['class' => $this->menu[$path]['class'], 'path' => $path];
            }
        }

        return $found;
    }

    protected static function makeRoute(array|Route|string $route, string $baseUri): Route
    {
        if (is_string($route)) {
            return new Route($route, $baseUri);
        }
        if (is_array($route)) {
            return new Route($route[0], $baseUri, $route[1] ?? null);
        }
        if ($route instanceof Route) {
            return $route;
        }
        throw new \InvalidArgumentException("Can't resolve route " . var_export($route, true));
    }
}
