<?php if ($wrap): ?><div class="form-control form-control--sm"><?php endif ?>
<select name="<?= $name ?>"<?php if ($onchange !== null): ?> onchange="<?= $onchange ?>"<?php endif ?> class="<?= $class ?>"<?= $multiple ? ' multiple' : '' ?>>
    <?php foreach ($options as [$optionValue, $optionLabel, $selected]): ?>
    <option value="<?= htmlspecialchars($optionValue) ?>"<?= $selected ? ' selected' : '' ?>><?= htmlspecialchars($optionLabel) ?></option>
    <?php endforeach ?>
</select>
<?php if ($wrap): ?></div><?php endif ?>