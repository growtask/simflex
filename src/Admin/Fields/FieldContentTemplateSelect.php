<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\Extensions\Content\ContentTemplates;

/**
 * Ключ шаблона страницы из provider/templates.php, показывается по названию шаблона
 */
class FieldContentTemplateSelect extends FieldExtensionSelect
{
    protected function options(): array
    {
        return array_map(fn($template) => $template->name, ContentTemplates::all());
    }
}
