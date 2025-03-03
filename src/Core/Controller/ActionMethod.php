<?php

namespace Simflex\Core\Controller;

use Simflex\Core\Container;
use Simflex\Core\Log;

class ActionMethod
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
        $myPath = trim($this->action->action, '/');

        // match fast
        if ($path == $myPath) {
            return true;
        }

        // decompose paths
        $pathParts = explode('/', $path);
        $parts = explode('/', $myPath);

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

        // matched everything
        return true;
    }
}