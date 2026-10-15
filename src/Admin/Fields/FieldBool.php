<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\FieldInt;

class FieldBool extends FieldInt
{

    public string $style = 'text-align:center;';
    public mixed $defaultValue = 0;

    public function input($value): string
    {
        return '<div class="form-control">
                                    <label class="form-control__switch">
                                        <input class="form-control__switch-check" type="checkbox" name="' . $this->inputName() . '" value="1" ' . ($value ? 'checked="checked"' : '') . ($this->readonly ? ' readonly' : '') . ' />
                                        <div class="form-control__switch-slider"></div>
                                    </label>
                                </div>';
         }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $postValue = $group === null ? @ $_POST[$this->name] : @ $_POST[$group][$this->name];
        return $this->e2n && empty($postValue) ? 0 : (isset($postValue) ? 1 : 0);
    }

    public function show($row): void
    {
        $value = $row[$this->name];
        $checked = $value ? 'checked' : '';
        echo <<<DATA
<div class="form-control">
                            <label class="form-control__switch" data-autoupdate>
                                <input class="form-control__switch-check" $checked type="checkbox" />
                                <div class="form-control__switch-slider"></div>
                            </label>
                        </div>
DATA;
    }

    public function showDetail($row): string
    {
        $value = $row[$this->name . ($this->fk ? '_label' : '')];
        return $value ? 'Да' : 'Нет';
    }

    public function filter($value): void
    {
        if ($this->filter) {
            echo '<div class="form-control form-control--sm">
                        <div class="form-control__dropdown">
                            <div class="form-control__dropdown-top">
                                <input class="form-control__dropdown-input" value="'.$value.'" type="hidden" name="filter[' . $this->name . ']" >
                                <div class="form-control__dropdown-current">—</div>
                                <button type="button" class="form-control__dropdown-toggle">
                                    <svg viewBox="0 0 24 24">
                                        <use xlink:href="'.asset('img/icons/svg-defs.svg').'#chevron-mini"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-control__dropdown-list">
                                <div data-value="1" class="form-control__dropdown-item">Да</div>
                                <div data-value="0" class="form-control__dropdown-item">Нет</div>
                                <div data-value="" class="form-control__dropdown-item">—</div> 
                            </div>
                        </div>
                    </div>';
        }
    }

    public static function typeLabel(): string
    {
        return 'Булевая переменная';
    }

    public static function typeParams(): array
    {
        return [];
    }

}
