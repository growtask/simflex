<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Fields\FieldTypeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Table;

class ContentTemplateParam extends Table
{
    public function __construct()
    {
        parent::__construct(
            name: 'content_template_param',
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            fields: [
                new FieldDefinition(
                    name: 'group_name',
                    class: FieldString::class,
                    label: 'Группа',
                    params: [
                        'main' => new FieldParams(
                            width: '140',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'ctp_id',
                    class: FieldString::class,
                    label: 'ID',
                    params: [
                        'main' => new FieldParams(
                            pk: true,
                            e2n: true,
                            hidden: true,
                            width: '54',
                            filter: true,
                            widthMob: '40',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'npp',
                    class: FieldNPP::class,
                    label: '№ п/п',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                            defaultValue: '0',
                            screenWidth: '991',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'template_id',
                    class: FieldInt::class,
                    label: 'Шаблон',
                    params: [
                        'main' => new FieldParams(
                            width: '250',
                            filter: true,
                            isFk: true,
                            fkTable: 'content_template',
                            fkKey: 'template_id',
                            fkLabel: 'template_name',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'param_pid',
                    class: FieldInt::class,
                    label: 'PID',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            isFk: true,
                            fkTable: 'content_template_param',
                            fkKey: 'ctp_id',
                            fkLabel: 'label',
                            fkIsPid: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'position',
                    class: FieldString::class,
                    label: 'Позиция',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'field_type',
                    class: FieldTypeSelect::class,
                    label: 'Тип поля',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            width: '150',
                            filter: true,
                            onchange: 'onChangeField(this)',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'name',
                    class: FieldString::class,
                    label: 'Название',
                    params: [
                        'main' => new FieldParams(
                            width: '140',
                            filter: true,
                            widthMob: '100',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'label',
                    class: FieldString::class,
                    label: 'Ярлык',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            filter: true,
                            widthMob: '100',
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'help',
                    class: FieldString::class,
                    label: 'Help',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'params',
                    class: FieldText::class,
                    label: 'Параметры',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'default_value',
                    class: FieldString::class,
                    label: 'Значение по умолчанию',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}
