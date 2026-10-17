<?php

namespace Simflex\Admin\Fields;

use Simflex\Extensions\Content\ContentTemplates;

/**
 * Page template key from provider/templates.php, shown by the template name
 */
class FieldContentTemplateSelect extends FieldExtensionSelect
{
    public static function typeLabel(): string
    {
        return 'Выбор шаблона страницы';
    }

    protected function options(): array
    {
        return array_map(fn($template) => $template->name, ContentTemplates::all());
    }
}
