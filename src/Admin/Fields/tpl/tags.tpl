<div class="form-control__tags" data-readonly="<?= $readonly ? 'true' : 'false' ?>"<?= $ajax ? ' data-ajax="true"' : '' ?>>
    <input class="form-control__tags-value" value="<?= $value ?>" type="hidden" name="<?= $name ?>">
    <ul class="form-control__tags-list">
        <input type="text" class="form-control__tags-input">
    </ul>
    <div class="form-control__tags-popup list1">
        <div class="list1__wrapper">
            <ul class="list1__items">
                <?php if ($addNew): ?>
                <li class="list1__item list1__item--add-new" data-value="" style="display: none"><strong>Добавить <span></span></strong></li>
                <?php endif ?>
                <?php foreach ($items as [$itemValue, $itemLabel]): ?>
                <li data-value="<?= $itemValue ?>" class="list1__item"><?= $itemLabel ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>