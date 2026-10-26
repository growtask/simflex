<?php

if (!isset($isGroup)) {
    $isGroup = false;
}
?>
<div class="data-point">
    <div class="data-point__title">
        <span class="data-point__text"><?= $field->label ?></span>
        <span class="data-point__help"><?= $field->help ?></span>
    </div>
    <?= $field->input($field->value); ?>
</div>