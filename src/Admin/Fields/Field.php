<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\Http\Request;
use Simflex\Core\Http\View;
use Simflex\Core\System\Container;

class Field
{
    public string $form = '';
    public string $name = '';
    public string $label = '';
    public bool $pk = false;
    public string $table = '';
    public string $tablePk = '';
    public string $help = '';
    public string $placeholder = '';
    public array $params = [];

    public ForeignKey|false $fk = false;
    public float $width = 0;
    public float $widthMob = 0;
    public bool $isVisible = true;
    public string $link = '';
    public bool $isnull = false;
    public bool $e2n = false;
    public mixed $defaultValue = '';
    public bool $autoIncrement = false;
    public bool $required = false;
    public string $editor = '';
    public bool $filter = false;

    /* ВВОД */
    public array $styleCol = [];
    public string $styleCell = '';
    public bool $hidden = false;
    public bool $readonly = false;
    public string $onchange = '';
    protected array $tree = [];
    public string|int|float|bool|array|null $value = '';
    public string|int $pkValue = '';
    protected int $pid;

    protected bool $isNumeric = false;

    /** Колонки у поля нет (FieldVirtual, FieldMultiKey и др.): значение не пишется в таблицу раздела */
    public bool $isVirtual = false;

    /**
     * Поле пишет свои данные по id записи (таблицы связей FieldMultiKey, FieldRelation). У новой записи id ещё нет:
     * Table::save() вызывает getPOST() таких полей ещё раз после вставки, а до неё они ничего не пишут
     */
    public bool $isSavedById = false;

    /** @var array|null Отправленные значения формы, если не POST запроса (группа параметров, см. setPosted()) */
    protected ?array $posted = null;

    public function __construct($row)
    {
        $this->name = $row['name'];
        $this->label = $row['label'];
        $this->table = $row['table'];
        $this->help = (string)@$row['help'];
        $this->placeholder = (string)@$row['placeholder'];

        if (isset($row['params']) && is_string($row['params'])) {
            $row['params'] = unserialize($row['params']);
        }

        $params = isset($row['params']['main']) ? $row['params']['main'] : [];
        $this->params = $params;

        $this->setWidth(@$params['width']);

        if ($params['width_mob'] ?? '') {
            $this->widthMob = (float)$params['width_mob'];
        }

        if (!empty($params['pk'])) {
            $this->pk = (bool)$params['pk'];
        }
        if (!empty($params['is_fk'])) {
            $this->fk = new ForeignKey($params);
        }
        if (!empty($params['style_col'])) {
            $this->styleCol['from_param'] = $params['style_col'];
        }
        if (!empty($params['style_cell'])) {
            $this->styleCell .= $params['style_cell'];
        }
        if (!empty($params['link'])) {
            $this->link = $params['link'];
        }
        if (!empty($params['hidden'])) {
            $this->hidden = (bool)$params['hidden'];
        }
        if (!empty($params['e2n'])) {
            $this->e2n = (bool)$params['e2n'];
            $this->isnull = (bool)$params['e2n'];
        }
        if (!empty($params['isnull'])) {
            $this->isnull = (bool)$params['isnull'];
        }
        if (!empty($params['defaultValue'])) {
            $this->defaultValue = $params['defaultValue'];
        }
        if (!empty($params['autoIncrement'])) {
            $this->autoIncrement = $params['autoIncrement'];
        }
        if (!empty($params['required'])) {
            $this->required = (bool)(int)$params['required'];
        }
        if (!empty($params['readonly'])) {
            $this->readonly = (bool)(int)$params['readonly'];
        }
        if (!empty($params['editor'])) {
            $this->editor = $params['editor'];
        }

        if (!empty($params['filter'])) {
            $this->filter = $params['filter'];
        }

        if (!empty($params['onchange'])) {
            $this->onchange = $params['onchange'];
        }
    }

    /**
     *
     * @param float $width
     */
    public function setWidth($width): void
    {
        $this->width = (empty($width) || (int)$width === 0) ? 0 : (float)$width;
        $this->isVisible = $this->width > 0;
        $this->styleCol['width'] = $this->width > 1
            ? 'width: ' . $this->width . 'px'
            : (($this->width < 1 && $this->width > 0) ? 'width: ' . round($this->width * 100) . '%' : '');
    }

    public static function setFieldValue(Field &$field, $group, $params, $row): void
    {
        $p = false;
        $params = $params ?: [];
        $value = '';
        if (is_array($group) && $group['name']) {
            $value = isset($params[$group['name']]) && array_key_exists($field->name, $params[$group['name']])
                ? $params[$group['name']][$field->name]
                : $field->defaultValue;
        } else {
            $value = array_key_exists($field->name, $params) ? $params[$field->name] : $field->defaultValue;
        }
        if ($p) {
            $value = $row[$field->name];
        }
        $field->value = $value;
    }

    public function input($value): string
    {
        return $this->render('input', ['value' => $value] + $this->inputVars());
    }

    /**
     * Рендерит шаблон из каталога tpl рядом с этим файлом
     *
     * @param string $tpl Имя шаблона без расширения
     * @param array<string, mixed> $vars Переменные, доступные в шаблоне как $name, вместе с $this
     */
    protected function render(string $tpl, array $vars = []): string
    {
        return View::render(__DIR__ . '/tpl/' . $tpl . '.tpl', $vars, $this);
    }

    protected function inputVars(): array
    {
        return [
            'name' => $this->inputName(),
            'placeholder' => $this->placeholder,
            'readonly' => $this->readonly,
        ];
    }

    protected function cell(string $value, string $kind = 'text'): string
    {
        return $this->render('cell', [
            'name' => $this->name,
            'isId' => (bool)$this->fk,
            'kind' => $kind,
            'value' => $value,
        ]);
    }

    /**
     * @param array $vars name, value, onchange, data, readonly, disabled, action, search, current, items [[value, label]]
     */
    protected function dropdown(array $vars): string
    {
        return $this->render('dropdown', $vars + [
            'name' => '',
            'value' => '',
            'onchange' => null,
            'data' => [],
            'readonly' => false,
            'disabled' => false,
            'action' => '',
            'search' => false,
            'current' => '—',
            'items' => [],
        ]);
    }

    public function inputName(): string
    {
        return $this->form ? $this->form . '[' . $this->name . ']' : $this->name;
    }

    public function inputHidden($value): string
    {
        $value = $this->value ?: $value;
        return $this->render('input-hidden', ['name' => $this->inputName(), 'value' => $value]);
    }

    /**
     * Значения формы, из которых поле берёт своё значение (getPosted()): по умолчанию - POST запроса,
     * для полей группы параметров раздел передаёт значения группы. null - снова POST запроса
     */
    public function setPosted(?array $posted): void
    {
        $this->posted = $posted;
    }

    /**
     * Отправленное значение формы
     * @param string|null $name Имя значения, по умолчанию - имя поля
     * @param string|int|null $group Группа: значение $posted[$group][$name]
     */
    protected function getPosted(?string $name = null, string|int|null $group = null): mixed
    {
        $posted = $this->posted ?? $this->getRequest()->post();
        if ($group !== null) {
            $posted = $posted[$group] ?? [];
        }

        return $posted[$name ?? $this->name] ?? null;
    }

    /**
     * Id записи раздела: pkValue (после вставки новой записи его задаёт раздел) или параметр запроса
     */
    protected function getRecordId(): int
    {
        return (int)($this->pkValue ?: $this->getRequest()->request($this->tablePk, 0));
    }

    protected function getRequest(): Request
    {
        return Container::getRequest() ?? new Request();
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        if ($simple) {
            return $this->getPosted(group: $group);
        }
        $value = $this->getPosted();
        return $this->e2n && $value === '' ? null : $value;
    }

    public function check(): array
    {
        $errors = [];
        if ($this->required && in_array($this->getPosted(), [null, ''], true)) {
            $errors[] = 'Обязательно для заполнения';
        }
        return $errors;
    }

    public function show($row): string
    {
        $value = $this->value ?: $row[$this->name . ($this->fk ? '_label' : '')];
        $value = strip_tags((string)$value, ['br', 'strong', 'b', 'em', 'i', 'span']);

        if ($this->name === 'name') {
            return $this->render('link', [
                'href' => '?action=form&' . $this->tablePk . '=' . $this->pkValue,
                'class' => 'table__row-' . $this->name,
                'label' => $value,
                'blank' => false,
            ]);
        } else {
            $isNumericReal = $this->isNumeric || ((string)intval($value) == $value);
            return $this->cell($value, $isNumericReal ? 'num' : 'text');
        }
    }

    /**
     * Показывать в карточке записи в режиме просмотра
     */
    public function showDetail($row): ?string
    {
        $value = strip_tags((string)$row[$this->name . ($this->fk ? '_label' : '')], ['br', 'strong', 'b', 'em', 'i']);
        return $value;
    }

    public function delete($value): bool
    {
        return true;
    }

    public function defval(): mixed
    {
        return $this->defaultValue;
    }

    public function filter($value): string
    {
        if ($this->filter) {
            $inExtra = $this->width === 0.0;
            return $this->render('filter', [
                'name' => $this->name,
                'placeholder' => $inExtra ? $this->label : '',
                'value' => $value,
            ]);
        }
        return '';
    }

    /**
     * Регистрирует ресурсы поля, возвращает HTML, нужный интерфейсу поля (скрипты инициализации)
     */
    public function loadUI(bool $onForm = false): string
    {
        return '';
    }

    public function selectQueryField(): string
    {
        return '' . $this->table . '.' . $this->name;
    }
}
