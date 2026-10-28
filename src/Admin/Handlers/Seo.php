<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Admin\Fields\FieldText;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Admin\Table\Presets\IdField;
use Simflex\Core\Admin\Table\Presets\ParentField;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\Models\Seo as SeoModel;

class Seo extends Table
{
    protected static string $model = SeoModel::class;

    public static function getFields(): array
    {
        return [
            new IdField(
                name: 'seo_id',
                width: '54',
                filter: true,
                widthMob: '60',
            ),
            new ParentField(
                name: 'seo_pid',
                table: 'seo',
                key: 'seo_id',
                display: 'title',
            ),
            new FieldDefinition(
                name: 'link',
                class: FieldString::class,
                label: 'Ссылка',
                width: '300',
                required: true,
                filter: true,
            ),
            new FieldDefinition(
                name: 'title',
                class: FieldString::class,
                label: 'Заголовок',
                width: '1',
                filter: true,
                widthMob: '180',
            ),
            new FieldDefinition(
                name: 'description',
                class: FieldText::class,
                label: 'Описание',
            ),
            new FieldDefinition(
                name: 'keywords',
                class: FieldText::class,
                label: 'Keywords',
            ),
        ];
    }
}
