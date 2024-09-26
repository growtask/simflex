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
 * CLI command bootstrapper
 */
class CliBootstrap
{
    /**
     * @var array|mixed List of all available providers
     */
    protected array $providers;

    /**
     * @var string|mixed User input provider
     */
    public string $provider;

    /**
     * @var string|mixed User input command (method)
     */
    public string $command;

    /**
     * @var ReflectionClass Loaded reflection class
     */
    protected ReflectionClass $class;

    /**
     * @var ReflectionMethod Loaded method
     */
    protected ReflectionMethod $method;

    /**
     * @var Help Method's attribute
     */
    protected Help $methodHelp;

    /**
     * @param array{0: string, 1: string} $cmd Command list
     */
    public function __construct(array $cmd)
    {
        $this->providers = include Container::getConfig()->files['commands'];

        $this->provider = $cmd[0];
        $this->command = $cmd[1];
    }

    /**
     * @return bool true if provider was found
     */
    public function hasProvider(): bool
    {
        return in_array($this->provider, array_keys($this->providers));
    }

    /**
     * Attempts to load the class
     * @return bool true if loaded
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
     * Attempts to load the method
     * @return bool true if loaded
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
     * Executes the command
     * @param array $args Argument list
     * @return void
     */
    public function execute(array $args): void
    {
        try {
            // setup class
            $ctor = $this->class->getConstructor();
            $instance = $this->class->newInstance(...($ctor ? Injector::resolve($ctor) : []));

            // resolve method
            $argList = [];
            $lastIdx = 0;
            foreach ($this->method->getParameters() as $param) {
                $lowerName = strtolower($param->getName());
                if (isset($args[$lowerName])) {
                    $argList[] = $args[$lowerName];
                } elseif (isset($args[$lastIdx])) {
                    $argList[] = $args[$lastIdx++];
                } elseif ($default = $param->getDefaultValue()) {
                    $argList[] = $default;
                } else {
                    Log::error('No argument provided for parameter {param}', ['param' => $param->getName()]);
                    return;
                }
            }

            // invoke method
            $this->method->invoke($instance, ...$argList);
        } catch (Exception $ex) {
            Log::emergency('Unable to execute. Error: {error}', ['error' => $ex->getMessage()]);
        }
    }

    /**
     * Returns command attribute for an item, if any
     * @param ReflectionMethod|ReflectionParameter $item item
     * @param string $class attribute class
     * @return Help|null object or null
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
     * Prints all available providers to log
     * @return void
     */
    public function printProviders(): void
    {
        foreach (array_keys($this->providers) as $key) {
            Log::notice("\t- {key}", ['key' => $key]);
        }
    }

    /**
     * Prints all available methods to log
     * @return void
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
     * Prints current method's help to log
     * @return void
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
                    'help' => $help ? $help->help : 'No help provided.'
                ]
            );
        }
    }

    /**
     * Formats parameter name
     * @param ReflectionParameter $param Parameter
     * @return string Formatted name
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