<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Presets\ParentField;
use Simflex\Admin\Table\Table;
use Simflex\Models\Seo as SeoModel;

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
