<table class="modal-info__table">
    <?php foreach (array_keys($row) as $key): ?>
    <?php
    try {
        $value = $this->showDetailPrepareValue($row, $key);
    } catch (Exception $e) {
        if ($e->getCode() == 999) {
            continue;
        }
    }
    ?>
    <tr class="modal-info__table-row">
        <th><?= $this->fields[$key]->label ?></th>
        <td><?= $value ?></td>
    </tr>
    <?php endforeach ?>
    <?= $this->showDetailExtra($row) ?>
</table>