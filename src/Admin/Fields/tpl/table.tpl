<input data-id="<?= $jsId ?>" type="hidden" name="<?= $this->inputName() ?>" value="<?= htmlspecialchars($value) ?>" />
<div data-id="<?= $jsId ?>" data-field="<?= $this->name ?>" class="data-point">
    <button type="button" class="BtnPrimarySm BtnIconLeft content__body-table-btn btn-modal-point" onclick="TableEditor.onRowEdit('<?= $jsId ?>', null);return false;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M2.51578 12C2.51578 11.4477 2.96349 11 3.51578 11L11.0011 11L11.0011 3.51472C11.0011 2.96243 11.4488 2.51472 12.0011 2.51472C12.5533 2.51472 13.0011 2.96243 13.0011 3.51472L13.0011 11H20.4863C21.0386 11 21.4863 11.4477 21.4863 12C21.4863 12.5523 21.0386 13 20.4863 13H13.0011L13.0011 20.4853C13.0011 21.0376 12.5533 21.4853 12.0011 21.4853C11.4488 21.4853 11.0011 21.0376 11.0011 20.4853L11.0011 13L3.51578 13C2.96349 13 2.51578 12.5523 2.51578 12Z"
                  fill="#ffffff" />
        </svg>
        Добавить строку</button>
    <div class="table__wrap">
        <table class="table table-content">
            <thead class="table__head">
                <tr class="table__head-row">
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script>TableEditor.init('<?= $jsId ?>');</script>