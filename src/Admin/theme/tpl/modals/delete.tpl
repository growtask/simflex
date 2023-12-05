<div class="modal-delete">
    <div class="modal-delete__mask"></div>
    <div class="modal-delete__inner">
        <div class="modal-delete__head">
            <button class="BtnIconSecondaryMonoSm modal-delete__close" onclick="closeDeleteModal()">
                <svg viewBox="0 0 24 24" fill="#ffffff">
                    <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#close"></use>
                </svg>
            </button>
            <div class="modal-delete__title">Удалить запись?</div>
        </div>
        <div class="modal-delete__btns">
            <button class="BtnSecondarySm">Удалить</button>
            <button class="BtnOutlineMonoSm">Отмена</button>
        </div>
    </div>
</div>