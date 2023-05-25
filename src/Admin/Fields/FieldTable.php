<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Fields\Field;
use Simflex\Core\Container;

class FieldTable extends Field
{
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
        if (!strstr(Container::getRequest()->getPath(), 'content_template_param') &&
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