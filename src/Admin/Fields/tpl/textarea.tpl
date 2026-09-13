<div class="form-control form-control--sm">
    <textarea class="form-control__textarea<?= $readonly ? ' sf-editor-readonly' : '' ?><?= $editor ? ' sf-editor-' . $editor : '' ?>" placeholder="<?= $placeholder ?>" name="<?= $name ?>"><?= htmlspecialchars($value) ?></textarea>
</div>