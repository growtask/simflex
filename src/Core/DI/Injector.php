<?php

namespace Simflex\Core\DI;

use ReflectionException;
use ReflectionFunctionAbstract;
use Simflex\Core\Container;

class Injector
{
    /**
     * Подбирает зависимости для конструктора класса
     *
     * @param object|string $target Имя класса
     * @param mixed ...$params Дополнительные параметры
     * @return array Найденные зависимости
     * @throws DIException Если у параметра нет типа, тип встроенный или обнаружена циклическая зависимость
     * @throws ReflectionException
     */
    public static function resolveClass(object|string $target, ...$params): array
    {
        $ref = new \ReflectionClass($target);

        // получаем конструктор
        $ct = $ref->getConstructor();
        if (!$ct) {
            return [];
        }

        return static::resolve($ct, ...$params);
    }

    /**
     * Подбирает зависимости для метода класса
     *
     * @param object|string $target Имя класса
     * @param string $method Имя метода
     * @param mixed ...$params Дополнительные параметры
     * @return array Найденные зависимости
     * @throws DIException Если у параметра нет типа, тип встроенный или обнаружена циклическая зависимость
     * @throws ReflectionException
     */
    public static function resolveMethod(object|string $target, string $method, ...$params): array
    {
        $ref = new \ReflectionClass($target);
        $md = $ref->getMethod($method);

        return static::resolve($md, ...$params);
    }

    /**
     * Подбирает зависимости для функции
     *
     * @param ReflectionFunctionAbstract $fn Функция
     * @param mixed ...$params Дополнительные параметры
     * @return array Найденные зависимости
     * @throws DIException Если у параметра нет типа, тип встроенный или обнаружена циклическая зависимость
     * @throws ReflectionException
     */
    public static function resolve(ReflectionFunctionAbstract $fn, ...$params): array
    {
        $target = $fn->getName();

        $added = [];
        $args = [];

        // получаем параметры
        foreach ($fn->getParameters() as $parameter) {
            // для параметров без типа не работает
            if (!$parameter->hasType()) {
                throw new DIException("Parameter {$parameter->getName()} in {$target} has no type");
            }

            $type = $parameter->getType();

            // встроенные типы не подбираем
            if ($type->isBuiltin()) {
                throw new DIException("Parameter {$parameter->getName()} in {$target} is a built-in type");
            }

            $className = $type->getName();

            // защита от циклических зависимостей
            if (in_array($className, $added)) {
                throw new DIException("Circular dependency detected in {$target}");
            }

            // проверяем, есть ли нужный класс среди параметров
            foreach ($params as $p) {
                if ($p instanceof $className) {
                    $args[] = $p;
                    $added[] = $className;
                    continue 2;
                }
            }

            // ищем класс в контейнере
            $classRef = new \ReflectionClass($className);
            if (!$classRef->implementsInterface(\Simflex\Core\DI\Service::class)) {
                throw new DIException("Class {$className} does not implement Service interface");
            }

            /** @noinspection PhpUndefinedMethodInspection */
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
