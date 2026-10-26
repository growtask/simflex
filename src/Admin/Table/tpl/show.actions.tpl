<?php if ($this->canAdd): ?>
    <a href="?action=form" class="BtnPrimarySm BtnIconLeft">
        <svg class="notification__close-icon" fill="#ffffff" viewBox="0 0 24 24">
            <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#plus"></use>
        </svg>
        Добавить
    </a>
<?php endif ?>
<?php if ($this->canEdit && $this->canEditGroup): ?>
    <button disabled id="btn-control-edit" class="BtnSecondaryMonoSm BtnIconLeft action-with-select" onclick="editRows()">
        <svg class="notification__close-icon" stroke="#ffffff" fill="none"
             viewBox="0 0 24 24">
            <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#edit"></use>
        </svg>
        Редактировать
    </button>
<?php endif ?>
<?php if ($this->canAdd && $this->canCopy): ?>
    <button disabled id="btn-control-copy" class="BtnSecondaryMonoSm BtnIconLeft action-with-select"  onclick="copyRows()">
        <svg class="notification__close-icon" stroke="#ffffff" fill="none"
             viewBox="0 0 24 24">
            <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#copy"></use>
        </svg>
        Копировать
    </button>
<?php endif ?>
<?php if ($this->canDelete): ?>
    <button disabled id="btn-control-delete" class="BtnSecondarySm BtnIconLeft action-with-select" onclick="deleteRowsForce()" href="#delete-dialog" data-toggle="modal">
        <svg class="notification__close-icon" fill="#ffffff" viewBox="0 0 24 24">
            <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#close"></use>
        </svg>
        Удалить
    </button>
<?php endif ?>
