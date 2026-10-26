<?php

namespace Simflex\Core\Console;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Simflex\Core\ConsoleBase;
use Simflex\Core\Container;
use Simflex\Core\DI\Injector;
use Simflex\Core\Log;

/**
 * Запуск CLI-команд
 */
class Cli
{
    /**
     * @var array|mixed Список всех доступных провайдеров
     */
    protected array $providers;

    /**
     * @var string|mixed Провайдер, введенный пользователем
     */
    public string $provider;

    /**
     * @var string|mixed Команда (метод), введенная пользователем
     */
    public string $command;

    /**
     * @var ReflectionClass Загруженный класс
     */
    protected ReflectionClass $class;

    /**
     * @var ReflectionMethod Загруженный метод
     */
    protected ReflectionMethod $method;

    /**
     * @var Help Атрибут метода
     */
    protected Help $methodHelp;

    /**
     * @param array{0: string, 1: string} $cmd Команда
     */
    public function __construct(array $cmd)
    {
        $this->providers = Container::getConfig()->getCommands();

        $this->provider = $cmd[0];
        $this->command = $cmd[1];
    }

    /**
     * @return bool true, если провайдер найден
     */
    public function hasProvider(): bool
    {
        return in_array($this->provider, array_keys($this->providers));
    }

    /**
     * Пытается загрузить класс
     * @return bool true, если загружен
     */
    public function tryLoadClass(): bool
    {
        try {
            $this->class = new ReflectionClass($this->providers[$this->provider] ?? '');
            return $this->class->isSubclassOf(ConsoleBase::class);
        } catch (ReflectionException) {
            return false;
        }
    }

    /**
     * Пытается загрузить метод
     * @return bool true, если загружен
     */
    public function tryLoadMethod(): bool
    {
        try {
            $this->method = $this->class->getMethod($this->command);
            if (!($attrib = $this->getAttribute($this->method, Command::class))) {
                return false;
            }

            $this->methodHelp = $attrib;
            return true;
        } catch (ReflectionException) {
            return false;
        }
    }

    /**
     * Выполняет команду
     * @param array $args Список аргументов
     */
    public function execute(array $args): void
    {
        try {
            // готовим класс
            $ctor = $this->class->getConstructor();
            $instance = $this->class->newInstance(...($ctor ? Injector::resolve($ctor) : []));

            // находим метод
            $argList = [];
            $lastIdx = 0;
            foreach ($this->method->getParameters() as $param) {
                $lowerName = strtolower($param->getName());

                try {
                    $default = $param->getDefaultValue();
                } catch (Exception) {
                }

                if (isset($args[$lowerName])) {
                    $argList[] = $args[$lowerName];
                } elseif (isset($args[$lastIdx])) {
                    $argList[] = $args[$lastIdx++];
                } elseif (isset($default)) {
                    $argList[] = $default;
                } else {
                    Log::error('No argument provided for parameter "{param}"', ['param' => $param->getName()]);
                    return;
                }
            }

            // вызываем метод
            $this->method->invoke($instance, ...$argList);
        } catch (Exception $ex) {
            Log::emergency('Unable to execute. Error: {error}', ['error' => $ex->getMessage()]);
        }
    }

    /**
     * Возвращает атрибут команды элемента, если он есть
     * @param ReflectionMethod|ReflectionParameter $item элемент
     * @param string $class класс атрибута
     * @return Help|null объект или null
     */
    protected function getAttribute(ReflectionMethod|ReflectionParameter $item, string $class): ?Help
    {
        $attribs = $item->getAttributes($class);
        if (!$attribs) {
            return null;
        }

        return $attribs[0]->newInstance();
    }

    /**
     * Выводит в лог все доступные провайдеры
     */
    public function printProviders(): void
    {
        foreach (array_keys($this->providers) as $key) {
            Log::notice("\t- {key}", ['key' => $key]);
        }
    }

    /**
     * Выводит в лог все доступные методы
     */
    public function printMethods(): void
    {
        foreach ($this->class->getMethods() as $method) {
            if (!($attrib = $this->getAttribute($method, Command::class))) {
                continue;
            }

            Log::notice("\t- {name} - {help}", ['name' => $method->getName(), 'help' => $attrib->help]);
        }
    }

    /**
     * Выводит в лог справку по текущему методу
     */
    public function printHelp(): void
    {
        $params = $this->method->getParameters();
        $cmdLine = implode(
            ' ',
            array_map(fn(ReflectionParameter $param) => $this->formatParameterName($param), $params)
        );

        Log::notice(
            '{provider}/{method} {cmdLine}',
            ['provider' => $this->provider, 'method' => $this->command, 'cmdLine' => $cmdLine]
        );

        Log::notice('- {help}', ['help' => $this->methodHelp->help]);
        if (!$params) {
            return;
        }

        $idx = 0;
        Log::notice('- Arguments:');
        foreach ($params as $param) {
            $help = $this->getAttribute($param, Help::class);
            Log::notice(
                "\t- {name} - {help}",
                [
                    'name' => $this->formatParameterName($param, $idx++),
                    'help' => $help ? $help->help : 'No help provided.',
                ]
            );
        }
    }

    /**
     * Форматирует имя параметра
     * @param ReflectionParameter $param Параметр
     * @return string Отформатированное имя
     */
    protected function formatParameterName(ReflectionParameter $param, ?int $idx = null): string
    {
        $out = '';

        try {
            $default = $param->getDefaultValue();
        } catch (ReflectionException) {
        }

        if (isset($default)) {
            $out .= '[';
        }

        if (!is_null($idx)) {
            $out .= $idx . '/';
        }

        $out .= $param->getName();
        if (isset($default)) {
            $out .= '=' . $default . ']';
        }

        return $out;
    }
}
