<?php

namespace Simflex\Core\Extensions\Code;

use Simflex\Core\Admin\Fields\FieldText;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Admin\Table\ParamGroup;
use Simflex\Core\System\ModuleBase;

/**
 * Выводит любой хранимый контент, используется для счётчиков,
 * и любого html-кода сторонних внешних сервисов
 */
class Code extends ModuleBase
{
    public static function extensionTitle(): string
    {
        return 'Код';
    }

    protected static bool $editorFull = false;

    public static function adminParams(): array
    {
        return [
            new ParamGroup(
                name: 'content',
                label: 'Контент',
                fields: [
                    new FieldDefinition(
                        name: 'content',
                        class: FieldText::class,
                        label: 'Текст',
                        editorFull: static::$editorFull,
                    ),
                ],
            ),
        ];
    }

    protected function content(): string
    {
        return (string)($this->params['content'] ?? '');
    }
}
