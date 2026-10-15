<div class="form-control form-control--sm">
    <div class="form-control__dropdown<?= $disabled ? ' disabled' : '' ?>"<?php if ($action !== ''): ?> data-action="<?= $action ?>" data-ajax="true"<?php endif ?>>
        <div class="form-control__dropdown-top">
            <input class="form-control__dropdown-input"<?php foreach ($data as $attr => $attrValue): ?> <?= $attr ?>="<?= $attrValue ?>"<?php endforeach ?><?php if ($onchange !== null): ?> onchange="<?= $onchange ?>"<?php endif ?> value="<?= $value ?>" type="hidden" name="<?= $name ?>"<?= $readonly ? ' readonly' : '' ?>>
            <?php if ($search): ?>
            <input placeholder="Начните вводить название..." class="form-control__dropdown-text" type="text"<?= $readonly ? ' readonly' : '' ?>>
            <?php endif ?>
            <div class="form-control__dropdown-current"><?= $current ?></div>
            <button type="button" class="form-control__dropdown-toggle"<?= $readonly ? ' readonly' : '' ?>>
                <svg viewBox="0 0 24 24">
                    <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#chevron-mini"></use>
                </svg>
            </button>
        </div>
        <div class="form-control__dropdown-list">
            <?php foreach ($items as [$itemValue, $itemLabel]): ?>
            <div data-value="<?= $itemValue ?>" class="form-control__dropdown-item"><?= $itemLabel ?></div>
            <?php endforeach ?>
        </div>
    </div>
</div>