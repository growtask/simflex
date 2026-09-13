<div class="form-control form-control--sm">
    <input style="width:64px;height:64px;" name="<?= $name ?>" value="<?= htmlspecialchars($value) ?>"<?php if (!empty($placeholder)): ?> placeholder="<?= $placeholder ?>"<?php endif ?><?= $readonly ? ' readonly' : '' ?> type="color" class="">
</div>