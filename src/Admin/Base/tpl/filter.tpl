<div id="admin-filter">
    <form id="admin-filter-form" method="post" action="<?= \Simflex\Admin\Core::path() ?>">
        <table>
            <tr>
                <td>
                    <?php foreach ($this->fields as $field): ?>
                        <?= $this->filterField($field) ?>
                    <?php endforeach ?>
                </td>
            </tr>
        </table>
    </form>
</div>
