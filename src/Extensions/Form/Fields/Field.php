<?php

namespace Simflex\Core\Extensions\Form\Fields;

use Simflex\Core\Extensions\Form\ModuleForm;
use Simflex\Core\DB\AQ;
use Simflex\Core\System\Container;

abstract class Field
{
    protected ModuleForm $form;
    protected string $name;
    protected string $label;
    public string $placeholder;
    public string $comment;
    public int $isText = 0;
    public bool $required = false;
    public array|int $vRequired = [];
    public array $vLength = [];
    public array $vEqual = [];
    public array $vMask = [];
    public array $vUnique = [];
    protected string $defval = '';

    public function __construct(ModuleForm $form, string $name, string $label, string $comment, int $isText = 0, string $placeholder = '', bool $required = false)
    {
        $this->form = $form;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->comment = $comment;
        $this->isText = $isText;
        $this->required = $required;
    }

    /**
     * Отправленное значение поля формы ($_POST[<форма>][<поле>] запроса)
     * @param string|null $name Поле формы, по умолчанию - это
     */
    protected function getPosted(?string $name = null): mixed
    {
        return (Container::getRequest()?->post($this->form->getName()) ?? [])[$name ?? $this->name] ?? null;
    }

    public function html(): string
    {
        $formName = $this->form->getName();
        $val = $this->getPosted() ?? $this->defval;
        $inputParams = ['class' => 'edit', 'type' => 'text', 'name' => "{$formName}[$this->name]"];
        $inputParams['value'] = htmlspecialchars($val);
        $this->placeholder && $inputParams['placeholder'] = $this->placeholder;
        $this->required && $inputParams['required'] = 'required';
        $tmp = [];
        foreach ($inputParams as $key => $value) {
            $tmp[] = $key . '="' . htmlspecialchars($value) . '"';
        }
        if ($this->isText) {
            return '<span class="plug-form-field"><textarea ' . implode(' ', $tmp) . ' cols="20" rows="4">'
                . htmlspecialchars($val) . '</textarea></span>';
        }
        return '<span class="plug-form-field"><input ' . implode(' ', $tmp) . ' /></span>';
    }

    public function &check(): array
    {
        $err = [];
        $value = (string)($this->getPosted() ?? '');

        /* ОБЯЗАТЕЛЬНОСТЬ */
        $this->vRequired = is_array($this->vRequired) ? $this->vRequired : [];
        if (!empty($this->vRequired['is']) && $value === '') {
            $err[$this->name][] = empty($this->vRequired['comment']) ? 'обязательно для заполнения' : $this->vRequired['comment'];
        }

        if (empty($err[$this->name])) {
            /* ДЛИНА */
            $len = mb_strlen($value);
            $this->vLength = is_array($this->vLength) ? $this->vLength : [];
            $this->vLength['min'] = isset($this->vLength['min']) ? (int)$this->vLength['min'] : 0;
            $this->vLength['max'] = isset($this->vLength['max']) ? (int)$this->vLength['max'] : 0;
            if (($this->vLength['min'] > 0 && $this->vLength['min'] > $len) || ($this->vLength['max'] > 0 && $this->vLength['max'] < $len)) {
                $err[$this->name][] = empty($this->vLength['comment']) ? 'недопустимая длина' : $this->vLength['comment'];
            }

            /* РАВЕНСТВО */
            $this->vEqual = is_array($this->vEqual) ? $this->vEqual : [];
            $this->vEqual['field'] = isset($this->vEqual['field']) ? (string)$this->vEqual['field'] : '';
            if ($this->vEqual['field']) {
                if ($value !== (string)($this->getPosted($this->vEqual['field']) ?? '')) {
                    $err[$this->name][] = empty($this->vEqual['comment']) ? 'неверное значение' : $this->vEqual['comment'];
                }
            }

            /* МАСКА */
            if ($value !== '') {
                $this->vMask = is_array($this->vMask) ? $this->vMask : [];
                $this->vMask['pattern'] = isset($this->vMask['pattern']) ? (string)$this->vMask['pattern'] : '';
                if ($this->vMask['pattern'] && !preg_match($this->vMask['pattern'], $value)) {
                    $err[$this->name][] = empty($this->vMask['comment']) ? 'некорректное значение' : $this->vMask['comment'];
                }
            }

            /* УНИКАЛЬНОСТЬ */
            $this->vUnique = is_array($this->vUnique) ? $this->vUnique : [];
            $this->vUnique['key'] = isset($this->vUnique['key']) ? (string)$this->vUnique['key'] : '';
            if ($this->vUnique['key'] && preg_match('@([a-z0-9\_]+)\.([a-z0-9\_]+)@', $this->vUnique['key'], $key)) {
                if ((int)new AQ()->select('COUNT(*)')->from($key[1])->where([$key[2] => $value])->fetchScalar() > 0) {
                    $err[$this->name][] = empty($this->vUnique['comment']) ? 'занято' : $this->vUnique['comment'];
                }
            }
        }

        return $err;
    }

    public function getPOST(): mixed
    {
        return $this->getPosted() ?? '';
    }

    public function defval(string $value): void
    {
        $this->defval = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
