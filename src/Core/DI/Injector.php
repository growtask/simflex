<?php

namespace Simflex\Core\DI;

use Simflex\Core\Container;

class Injector
{
    /**
     * Shortcut for resolving dependencies for a class constructor
     *
     * @param object|string $target Class name
     * @param array ...$params Additional parameters
     * @return array Resolved dependencies
     * @throws DIException If a parameter has no type, is a built-in type, or a circular dependency is detected
     * @throws \ReflectionException
     */
    public static function resolveClass($target, ...$params): array
    {
        $ref = new \ReflectionClass($target);

        // get constructor
        $ct = $ref->getConstructor();
        if (!$ct) {
            return [];
        }

        return static::resolve($ct, ...$params);
    }

    /**
     * Shortcut for resolving dependencies for a method of a class
     *
     * @param object|string $target Target class name
     * @param string $method Method name
     * @param array ...$params Additional parameters
     * @return array Resolved dependencies
     * @throws DIException If a parameter has no type, is a built-in type, or a circular dependency is detected
     * @throws \ReflectionException
     */
    public static function resolveMethod($target, string $method, ...$params): array
    {
        $ref = new \ReflectionClass($target);
        $md = $ref->getMethod($method);
        if (!$md) {
            throw new \Exception("Method {$method} not found in {$target}");
        }

        return static::resolve($md, ...$params);
    }

    /**
     * Resolves dependencies for a function
     *
     * @param \ReflectionFunctionAbstract $fn Function to resolve
     * @param array ...$params Additional parameters
     * @return array Resolved dependencies
     * @throws DIException If a parameter has no type, is a built-in type, or a circular dependency is detected
     * @throws \ReflectionException
     */
    public static function resolve(\ReflectionFunctionAbstract $fn, ...$params): array
    {
        $added = [];
        $args = [];

        // get parameters
        foreach ($fn->getParameters() as $parameter) {
            // won't work for typeless parameters
            if (!$parameter->hasType()) {
                throw new DIException("Parameter {$parameter->getName()} in {$target} has no type");
            }

            $type = $parameter->getType();

            // won't resolve built-in types
            if ($type->isBuiltin()) {
                throw new DIException("Parameter {$parameter->getName()} in {$target} is a built-in type");
            }

            $className = $type->getName();

            // safeguard against circular dependencies
            if (in_array($className, $added)) {
                throw new DIException("Circular dependency detected in {$target}");
            }

            // check if params contain the class we need
            foreach ($params as $p) {
                if ($p instanceof $className) {
                    $args[] = $p;
                    $added[] = $className;
                    continue 2;
                }
            }

            // check container for the class
            $classRef = new \ReflectionClass($className);
            if (!$classRef->implementsInterface(\Simflex\Core\DI\Service::class)) {
                throw new DIException("Class {$className} does not implement Service interface");
            }

            $serviceName = $className::getServiceName();
            if (!Container::get($serviceName)) {
                throw new DIException("Service {$serviceName} not found in container");
            }

            $added[] = $className;
            $args[] = Container::get($serviceName);
        }

        return $args;
    }
}