<div class="form-control form-control--sm">
    <input name="<?= $name ?>" value="<?= htmlspecialchars($value) ?>"<?php if (!empty($placeholder)): ?> placeholder="<?= $placeholder ?>"<?php endif ?><?= $readonly ? ' readonly' : '' ?> type="text" class="form-control__input">
</div>