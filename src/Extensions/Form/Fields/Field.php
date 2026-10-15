<?php

namespace Simflex\Extensions\Form\Fields;

use Simflex\Extensions\Form\ModuleForm;
use Simflex\Core\DB;

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

    public function html(): void
    {
        $formName = $this->form->getName();
        $val = $_POST[$formName][$this->name] ?? $this->defval;
        $inputParams = ['class' => 'edit', 'type' => 'text', 'name' => "{$formName}[$this->name]"];
        $inputParams['value'] = htmlspecialchars($val);
        $this->placeholder && $inputParams['placeholder'] = $this->placeholder;
        $this->required && $inputParams['required'] = 'required';
        $tmp = [];
        foreach ($inputParams as $key => $value) {
            $tmp[] = $key . '="' . htmlspecialchars($value) . '"';
        }
        if ($this->isText) {
            echo '<span class="plug-form-field"><textarea ' . implode(' ', $tmp) . ' cols="20" rows="4">', htmlspecialchars(
                $val
            ), '</textarea></span>';
        } else {
            echo '<span class="plug-form-field"><input ' . implode(' ', $tmp) . ' /></span>';
        }
    }

    public function &check(): array
    {
        $formName = $this->form->getName();
        $err = [];
        $_POST[$formName][$this->name] = $_POST[$formName][$this->name] ?? '';

        /* REQUIED */
        $this->vRequired = is_array($this->vRequired) ? $this->vRequired : [];
        if (!empty($this->vRequired['is']) && $_POST[$formName][$this->name] === '') {
            $err[$this->name][] = empty($this->vRequired['comment']) ? 'обязательно для заполнения' : $this->vRequired['comment'];
        }

        if (empty($err[$this->name])) {
            /* LENGTH */
            $len = mb_strlen($_POST[$formName][$this->name]);
            $this->vLength = is_array($this->vLength) ? $this->vLength : [];
            $this->vLength['min'] = isset($this->vLength['min']) ? (int)$this->vLength['min'] : 0;
            $this->vLength['max'] = isset($this->vLength['max']) ? (int)$this->vLength['max'] : 0;
            if (($this->vLength['min'] > 0 && $this->vLength['min'] > $len) || ($this->vLength['max'] > 0 && $this->vLength['max'] < $len)) {
                $err[$this->name][] = empty($this->vLength['comment']) ? 'недопустимая длина' : $this->vLength['comment'];
            }

            /* EQUAL */
            $this->vEqual = is_array($this->vEqual) ? $this->vEqual : [];
            $this->vEqual['field'] = isset($this->vEqual['field']) ? (string)$this->vEqual['field'] : '';
            if ($this->vEqual['field']) {
                $_POST[$formName][$this->vEqual['field']] = $_POST[$formName][$this->vEqual['field']] ?? '';
                if ($_POST[$formName][$this->name] !== $_POST[$formName][$this->vEqual['field']]) {
                    $err[$this->name][] = empty($this->vEqual['comment']) ? 'неверное значение' : $this->vEqual['comment'];
                }
            }

            /* MASK */
            if ($_POST[$formName][$this->name] !== '') {
                $this->vMask = is_array($this->vMask) ? $this->vMask : [];
                $this->vMask['pattern'] = isset($this->vMask['pattern']) ? (string)$this->vMask['pattern'] : '';
                if ($this->vMask['pattern'] && !preg_match($this->vMask['pattern'], $_POST[$formName][$this->name])) {
                    $err[$this->name][] = empty($this->vMask['comment']) ? 'некорректное значение' : $this->vMask['comment'];
                }
            }

            /* UNIQUE */
            $this->vUnique = is_array($this->vUnique) ? $this->vUnique : [];
            $this->vUnique['key'] = isset($this->vUnique['key']) ? (string)$this->vUnique['key'] : '';
            if ($this->vUnique['key'] && preg_match('@([a-z0-9\_]+)\.([a-z0-9\_]+)@', $this->vUnique['key'], $key)) {
                $q = 'SELECT COUNT(*) cnt FROM ' . DB::wrapName($key[1]) . ' WHERE ' . DB::wrapName($key[2]) . ' = ?';
                if ((int)DB::result($q, 'cnt', [$_POST[$formName][$this->name]]) > 0) {
                    $err[$this->name][] = empty($this->vUnique['comment']) ? 'занято' : $this->vUnique['comment'];
                }
            }
        }

        return $err;
    }

    public function getPOST(): mixed
    {
        $formName = $this->form->getName();
        return $_POST[$formName][$this->name] ?? '';
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
