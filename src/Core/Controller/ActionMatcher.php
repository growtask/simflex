<?php

namespace Simflex\Core\Controller;

use Simflex\Core\Container;
use Simflex\Core\Log;

/**
 * Combines Action and method name.
 *
 * Delimiteres:
 * - `:` - start position of a path part to be extracted (example - `/path/:name/to/something/`, if `/path/hello/to/something/` then `name` = `hello`)
 *
 * @see Action
 */
class ActionMatcher
{
    protected array $vars = [];

    public function __construct(public Action $action, public string $methodName)
    {
    }

    /**
     * @return array Extracted vars
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @return bool If has normal placeholders (`:example`)
     */
    public function hasPlaceholders(): bool
    {
        return strpos($this->action->path, ':') !== false;
    }

    /**
     * Attempt matching against the current path
     * @return bool
     */
    public function match(): bool
    {
        $request = Container::getRequest();

        // match method, if something but "all" was set
        if ($this->action->method != 'all' && $request->getRequestMethod() != $this->action->method) {
            return false;
        }

        $route = $request->getRoute();
        $path = trim($request->getPath(), '/');

        // replace route part in the path
        $path = trim(substr($path, strlen($route->getBaseUri())), '/');
        $myPath = trim($this->action->path, '/');

        // match fast
        if ($path == $myPath) {
            return true;
        }

        // decompose paths
        $pathParts = explode('/', $path);
        $parts = explode('/', $myPath);

        // match full path
        return $this->matchFull($parts, $pathParts);
    }

    protected function matchFull(array $parts, array $pathParts): bool
    {
        // check if we even get the same amount of items here
        if (count($parts) !== count($pathParts)) {
            return false;
        }

        // try matching
        for ($i = 0; $i < count($parts); ++$i) {
            $part = $parts[$i];
            $pathPart = $pathParts[$i];

            // we shouldn't get empty ones in the action, nor in the actual path
            if (empty($part) || empty($pathPart)) {
                return false;
            }

            // check if this one should be extracted
            if ($part[0] == ':') {
                $varName = substr($part, 1);
                $this->vars[$varName] = $pathPart;
                continue;
            }

            // check if we match strict
            if ($part != $pathPart) {
                return false;
            }
        }

        return true;
    }
}