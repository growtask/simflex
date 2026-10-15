<?php

namespace Simflex\Extensions\Code;

use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\ParamGroup;
use Simflex\Core\ModuleBase;

/**
 * Выводит любой хранимый контент, используется для счётчиков,
 * и любого html-кода сторонних внешних сервисов
 */
class Code extends ModuleBase
{
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
                        params: ['main' => new FieldParams(editorFull: static::$editorFull)],
                    ),
                ],
            ),
        ];
    }

    protected function content(): void
    {
        echo $this->params['content'] ?? '';
    }
}
