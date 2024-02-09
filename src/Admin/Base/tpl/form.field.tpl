<?php
if (!isset($isGroup)) {
    $isGroup = false;
}
?>
<div class="data-point">
<!--        --><?php //if ($isGroup): ?>
<!--            <div style="position: absolute; top: 2px; left: 0">-->
<!--                <input class="group-set" type="checkbox" name="set_param[--><?php //echo $field->name ?><!--]" value="" title="Изменить это поле" />-->
<!--            </div>-->
<!--        --><?php //endif ?>
        <span class="data-point__text"><?= $field->label?></span>
        <?=$field->input($field->value); ?>
</div>