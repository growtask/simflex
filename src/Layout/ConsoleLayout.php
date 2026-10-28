<?php

namespace Simflex\Core\Layout;

use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\Help;
use Simflex\Core\Console\ConsoleBase;
use Simflex\Core\System\Container;
use Simflex\Core\Http\Page;
use Simflex\Core\Http\Html\HtmlRequest;
use Simflex\Core\Logger\Log;

/**
 * Команды Layout: ./sf layout/<команда>
 */
class ConsoleLayout extends ConsoleBase
{
    /**
     * Рисует страницу и выводит поля, объявленные её Layout-компонентами (getFieldSchema()), в виде кода
     * ContentTemplateBlock: его нужно вставить в params шаблона страницы в provider/templates.php
     */
    #[Command('Generate the code of the layout fields of a page template')]
    public function generate(#[Help('Page path')] string $path): void
    {
        // подставляем фиктивные данные.
        Container::set('request', HtmlRequest::create($path));

        LayoutManager::$shouldGenerateFields = true;
        LayoutManager::$generatedFields = [];

        try {
            // компонент страницы выбирается так же, как при запросе из браузера
            $page = new Page();
            $page->initRequest();
            $page->getComponent()->execute();
        } catch (\Throwable $ex) {
            Log::error($ex->getMessage());
        }

        LayoutManager::$shouldGenerateFields = false;

        if (!LayoutManager::$generatedFields) {
            Log::notice('The layouts of the page declare no fields');
            return;
        }

        echo static::code(LayoutManager::$generatedFields), "\n";
    }

    /**
     * @param array<string, list<array>> $blocks название блока => поля, см. LayoutManager::$generatedFields
     */
    public static function code(array $blocks): string
    {
        $code = '';
        foreach ($blocks as $label => $fields) {
            $code .= 'new ContentTemplateBlock(' . var_export((string)$label, true) . ", [\n";
            foreach ($fields as $field) {
                $args = [
                    'name: ' . var_export($field['name'], true),
                    'class: \\' . ltrim($field['class'], '\\') . '::class',
                    'label: ' . var_export($field['label'], true),
                ];
                $params = [];
                if ($field['default'] !== '') {
                    $params['defaultValue'] = $field['default'];
                }
                foreach ($field['params'] as $key => $value) {
                    $params[lcfirst(str_replace('_', '', ucwords((string)$key, '_')))] = $value;
                }
                // именованные аргументы в порядке конструктора FieldDefinition
                foreach ((new \ReflectionMethod(FieldDefinition::class, '__construct'))->getParameters() as $parameter) {
                    if (array_key_exists($parameter->name, $params)) {
                        $args[] = $parameter->name . ': ' . var_export($params[$parameter->name], true);
                    }
                }
                $code .= '    new FieldDefinition(' . implode(', ', $args) . "),\n";
            }
            $code .= "]),\n";
        }

        return $code;
    }
}
