<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Fields\FieldTypeSelect;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\TableDefinition;
use Simflex\Admin\Structure\Table;

class ContentTemplateParam extends Table
{
    public function name(): string
    {
        return 'content_template_param';
    }

    public function definition(): TableDefinition
    {
        return new TableDefinition(
            privAdd: 1,
            privEdit: 1,
            privDelete: 1,
            fields: [
                new FieldDefinition(
                    name: 'group_name',
                    label: 'Группа',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '140',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'ctp_id',
                    label: 'ID',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: '№ п/п',
                    class: FieldNPP::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Шаблон',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
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
                    label: 'PID',
                    class: FieldInt::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Позиция',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            width: '107',
                            filter: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'field_type',
                    label: 'Тип поля',
                    class: FieldTypeSelect::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Название',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Ярлык',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
                    label: 'Help',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(),
                    ],
                ),
                new FieldDefinition(
                    name: 'params',
                    label: 'Параметры',
                    class: FieldText::class,
                    help: '',
                    placeholder: '',
                    params: [
                        'main' => new FieldParams(
                            e2n: true,
                            hidden: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'default_value',
                    label: 'Значение по умолчанию',
                    class: FieldString::class,
                    help: '',
                    placeholder: '',
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
