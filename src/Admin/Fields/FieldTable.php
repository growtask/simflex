<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Fields\Field;
use Simflex\Core\Container;

class FieldTable extends Field
{
    /**
     * String field
     */
    public const TYPE_TEXT = 'text';

    /**
     * Combobox (select) field
     */
    public const TYPE_COMBO = 'combo';

    /**
     * WYSIWYG editor field
     */
    public const TYPE_EDITOR = 'editor';

    /**
     * Image upload field
     */
    public const TYPE_IMAGE = 'image';

    /**
     * File upload field
     */
    public const TYPE_FILE = 'file';

    public const STRUCT_VALUE = [
        [
            'n' => 'n',
            't' => 'text',
            'l' => 'Имя',
            'v' => '',
            'e' => '',
        ],
        [
            'n' => 't',
            't' => 'combo',
            'l' => 'Тип',
            'v' => 'text',
            'e' => 'text=Текст,,int=Число,,combo=Список,,editor=Редактор,,image=Изображение,,file=Файл',
        ],
        [
            'n' => 'l',
            't' => 'text',
            'l' => 'Заголовок',
            'v' => '',
            'e' => '',
        ],
        [
            'n' => 'v',
            't' => 'text',
            'l' => 'Значение',
            'v' => '',
            'e' => '',
        ],
        [
            'n' => 'e',
            't' => 'text',
            'l' => 'Дополнительно',
            'v' => '',
            'e' => '',
        ],
    ];

    public static function makeStruct(
        string $name,
        string $type,
        string $label,
        string $value = '',
        string $extra = ''
    ): array {
        return [
            'n' => $name,
            't' => $type,
            'l' => $label,
            'v' => $value,
            'e' => $extra,
        ];
    }

    public static function makeValue(array $struct, array $values): string
    {
        return json_encode(['s' => $struct, 'v' => $values], JSON_UNESCAPED_UNICODE);
    }

    public function input($value)
    {
        // fallback value = []
        $value = !json_decode($value, true) ? '[]' : $value;
        if ($value == '[]' && $this->params['struct']) {
            $s = json_decode($this->params['struct'], true);
            $v = json_decode($value, true);

            $v['s'] = $s['v'];
            $v['v'] = [];
            $value = json_encode($v);
        }

        $tmpVal = json_decode($value, true);
        if (!strstr(Container::getRequest()->getPath(), 'content_template_param') && !strstr(
                Container::getRequest()->getPath(),
                'structure'
            ) &&
            $this->params['struct'] && $tmpVal['s'] != $this->params['struct']) {
            $s = json_decode($this->params['struct'], true);
            $v = json_decode($value, true);

            $v['s'] = $s['v'];
            foreach ($v['s'] as $s) {
                for ($i = 0; $i < count($v['v']); ++$i) {
                    if (!isset($v['v'][$i][$s['n']])) {
                        $v['v'][$i][$s['n']] = '';
                    }
                }
            }

            $value = json_encode($v);
        }

        // generate id for the js
        $jsId = (string)crc32($this->inputName());

        ob_start();
        include 'tpl/table.tpl';
        return ob_get_clean();
    }
}