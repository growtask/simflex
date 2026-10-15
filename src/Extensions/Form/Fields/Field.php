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
    public int $is_text = 0;
    public bool $required = false;
    public array|int $v_requied = [];
    public array $v_length = [];
    public array $v_equal = [];
    public array $v_mask = [];
    public array $v_unique = [];
    protected string $defval = '';

    public function __construct(ModuleForm $form, string $name, string $label, string $comment, int $is_text = 0, string $placeholder = '', bool $required = false)
    {
        $this->form = $form;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->comment = $comment;
        $this->is_text = $is_text;
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
        if ($this->is_text) {
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
        $this->v_requied = is_array($this->v_requied) ? $this->v_requied : [];
        if (!empty($this->v_requied['is']) && $_POST[$formName][$this->name] === '') {
            $err[$this->name][] = empty($this->v_requied['comment']) ? "обязательно для заполнения" : $this->v_requied['comment'];
        }

        if (empty($err[$this->name])) {
            /* LENGTH */
            $len = mb_strlen($_POST[$formName][$this->name]);
            $this->v_length = is_array($this->v_length) ? $this->v_length : [];
            $this->v_length['min'] = isset($this->v_length['min']) ? (int)$this->v_length['min'] : 0;
            $this->v_length['max'] = isset($this->v_length['max']) ? (int)$this->v_length['max'] : 0;
            if (($this->v_length['min'] > 0 && $this->v_length['min'] > $len) || ($this->v_length['max'] > 0 && $this->v_length['max'] < $len)) {
                $err[$this->name][] = empty($this->v_length['comment']) ? "недопустимая длина" : $this->v_length['comment'];
            }

            /* EQUAL */
            $this->v_equal = is_array($this->v_equal) ? $this->v_equal : [];
            $this->v_equal['field'] = isset($this->v_equal['field']) ? (string)$this->v_equal['field'] : '';
            if ($this->v_equal['field']) {
                $_POST[$formName][$this->v_equal['field']] = $_POST[$formName][$this->v_equal['field']] ?? '';
                if ($_POST[$formName][$this->name] !== $_POST[$formName][$this->v_equal['field']]) {
                    $err[$this->name][] = empty($this->v_equal['comment']) ? "неверное значение" : $this->v_equal['comment'];
                }
            }

            /* MASK */
            if ($_POST[$formName][$this->name] !== '') {
                $this->v_mask = is_array($this->v_mask) ? $this->v_mask : [];
                $this->v_mask['pattern'] = isset($this->v_mask['pattern']) ? (string)$this->v_mask['pattern'] : '';
                if ($this->v_mask['pattern'] && !preg_match($this->v_mask['pattern'], $_POST[$formName][$this->name])) {
                    $err[$this->name][] = empty($this->v_mask['comment']) ? "некорректное значение" : $this->v_mask['comment'];
                }
            }

            /* UNIQUE */
            $this->v_unique = is_array($this->v_unique) ? $this->v_unique : [];
            $this->v_unique['key'] = isset($this->v_unique['key']) ? (string)$this->v_unique['key'] : '';
            if ($this->v_unique['key'] && preg_match('@([a-z0-9\_]+)\.([a-z0-9\_]+)@', $this->v_unique['key'], $key)) {
                $q = "SELECT COUNT(*) cnt FROM " . DB::wrapName($key[1]) . " WHERE " . DB::wrapName($key[2]) . " = ?";
                if ((int)DB::result($q, 'cnt', [$_POST[$formName][$this->name]]) > 0) {
                    $err[$this->name][] = empty($this->v_unique['comment']) ? "занято" : $this->v_unique['comment'];
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
