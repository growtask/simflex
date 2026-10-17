<?php
/**
 * @var array<array{name: string, title: string, active: bool, switchable: bool}> $rows
 */
?>
<div class="content">
    <div class="content__inner">
        <div class="content__head">
            <div class="content__head-wrap">
                <div class="content__head-bg"></div>
                <div class="content__head-left">
                    <h4 class="content__title"><?= htmlspecialchars($this->title ?: 'Привилегии') ?></h4>
                </div>
            </div>
        </div>
        <div class="content__body">
            <div class="privilege-tables">
                <table class="table privilege-table">
                    <thead>
                    <tr class="table__head-row">
                        <th class="table__head-col privilege-table__active"><span class="form-control__sort-title">Активно</span></th>
                        <th class="table__head-col privilege-table__name"><span class="form-control__sort-title">Название</span></th>
                        <th class="table__head-col"><span class="form-control__sort-title">Описание</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr class="table__row privilege-table__row" data-privilege="<?= htmlspecialchars($row['name']) ?>">
                            <td class="privilege-table__active">
                                <div class="form-control">
                                    <label class="form-control__switch"<?= $row['switchable'] ? '' : ' title="Без неё никто не сможет включить остальные привилегии обратно"' ?>>
                                        <input class="form-control__switch-check" type="checkbox"
                                            <?= $row['active'] ? 'checked' : '' ?><?= $row['switchable'] ? '' : ' disabled' ?> />
                                        <div class="form-control__switch-slider"></div>
                                    </label>
                                </div>
                            </td>
                            <td class="privilege-table__name"><div class="table__row-text"><?= htmlspecialchars($row['name']) ?></div></td>
                            <td><div class="table__row-text"><?= htmlspecialchars($row['title']) ?></div></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    .privilege-table th, .privilege-table td { padding: 12px 16px; text-align: left; }
    .privilege-table__row { border-top: 1px solid #eef1f5; }
    .privilege-table .table__row-text { padding: 0; margin: 0; }
    .privilege-table__active { width: 120px; }
    .privilege-table__name { width: 240px; }
    .privilege-table .form-control__switch:has(input:disabled) { opacity: .5; cursor: not-allowed; }
</style>
<script>
    document.querySelectorAll('.privilege-table [data-privilege] .form-control__switch-check').forEach(function (input) {
        input.addEventListener('change', function () {
            const body = new URLSearchParams({name: input.closest('[data-privilege]').dataset.privilege, active: input.checked ? '1' : '0'});
            fetch('?action=switch', {method: 'POST', body: body, headers: {'X-Requested-With': 'XMLHttpRequest'}})
                .then(function (res) { return res.json(); })
                .then(function (data) { input.checked = !!data.active; })
                .catch(function () { input.checked = !input.checked; });
        });
    });
</script>
