<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Table\Table;
use Simflex\DB\DB;
use Simflex\DB\AQ;

/**
 * Связи «многие ко многим»: записи другой таблицы, выбранные тегами. Колонки у поля нет, связи хранятся в таблице
 * связей. Параметры типа поля (проще через пресет \Simflex\Admin\Table\Presets\MultiKeyField):
 * - table_values — таблица выбираемых записей;
 * - table_relations — таблица связей;
 * - key — первичный ключ table_values, он же колонка таблицы связей с выбранной записью;
 * - key_alias — колонка table_values, которая показывается вместо ключа;
 * - relation_key — колонка таблицы связей с id записи раздела, по умолчанию — первичный ключ раздела;
 * - parent_key — колонка родителя в table_values: запись подписывается «Имя (Родитель)».
 * Связи пишутся по id записи, поэтому у новой записи сохраняются после её вставки (Table::save())
 */
class FieldMultiKey extends Field
{
    /** @var array<int, string>|null подписи связей записей показываемого списка: id записи => «Имя, Имя» */
    protected ?array $listLabels = null;

    public function __construct($row)
    {
        parent::__construct($row);
        $this->isVirtual = true;
        $this->isSavedById = true;
    }

    /**
     * Колонка таблицы связей с id записи раздела
     */
    public function getRelationKey(): string
    {
        return (string)($this->params['relation_key'] ?? '') ?: $this->tablePk;
    }

    public function input($value): string
    {
        // форма, показанная снова после ошибки сохранения, — с выбранными в ней записями
        if (isset($_POST[$this->name])) {
            $selected = $this->getPostedIds();
        } else {
            $id = (int)($_REQUEST[$this->tablePk] ?? 0);
            $selected = $id ? $this->getSelectedIds($id) : [];
        }

        $items = [];
        foreach ($this->getValues() as $valueId => $label) {
            $items[] = [$valueId, htmlspecialchars($label)];
        }

        return $this->render('tags', [
            'readonly' => true,
            'ajax' => false,
            'addNew' => false,
            'name' => $this->name,
            'value' => implode(',', $selected),
            'items' => $items,
        ]);
    }

    /**
     * Сохраняет связи записи. Без id записи (новая запись до вставки) и без значения в форме ничего не пишет
     */
    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $id = (int)($_REQUEST[$this->tablePk] ?? 0);
        if (!$id || !isset($_POST[$this->name])) {
            return '';
        }

        $relations = DB::wrapName((string)$this->params['table_relations']);
        $relationKey = DB::wrapName($this->getRelationKey());
        $key = DB::wrapName((string)$this->params['key']);
        DB::query("DELETE FROM $relations WHERE $relationKey = ?", [$id]);
        foreach ($this->getPostedIds() as $valueId) {
            DB::query("INSERT INTO $relations ($relationKey, $key) VALUES (?, ?)", [$id, $valueId]);
        }

        return '';
    }

    public function show($row): string
    {
        $this->listLabels ??= $this->getListLabels();

        return $this->cell($this->listLabels[(int)$row[$this->tablePk]] ?? '');
    }

    public function filter($value): string
    {
        $value = (string)$value;
        $label = $value !== '' ? ($this->getValues([(int)$value])[(int)$value] ?? '') : '—';

        return $this->dropdown([
            'name' => 'filter[' . $this->name . ']',
            'value' => $value,
            'action' => 'searchMultiKey',
            'search' => true,
            'items' => [[$value, htmlspecialchars($label)]],
        ]);
    }

    public function selectQueryField(): string
    {
        return '';
    }

    /**
     * Выбираемые записи: ключ => подпись (с родителем, если задан parent_key), по порядку ключа
     *
     * @param int[]|null $ids Только эти записи
     * @param string $search Только записи, в подписи которых есть этот текст
     * @param int $limit Не больше стольких записей, 0 — все
     * @return array<int, string>
     */
    public function getValues(?array $ids = null, string $search = '', int $limit = 0): array
    {
        if ($ids === []) {
            return [];
        }

        $values = (string)$this->params['table_values'];
        $key = (string)$this->params['key'];
        $label = DB::wrapName((string)$this->params['key_alias']);
        $parentKey = (string)($this->params['parent_key'] ?? '');

        $select = ['t.' . DB::wrapName($key) . ' AS id', "t.$label AS name"];
        $query = (new AQ())->asArray()->from($values, 't')->orderBy('t.' . DB::wrapName($key));
        if ($parentKey !== '') {
            $select[] = "p.$label AS parent";
            $query->leftJoin($values, 't.' . $parentKey, 'p.' . $key, alias: 'p');
        }
        $query->select($select);
        if ($ids !== null) {
            $query->andWhere(['t.' . $key => array_map('intval', $ids)]);
        }
        if ($search !== '') {
            $query->andWhere("t.$label LIKE :search")->bind(['search' => '%' . $search . '%']);
        }
        if ($limit > 0) {
            $query->limit($limit);
        }

        $out = [];
        foreach ($query->all() as $row) {
            $out[(int)$row['id']] = $row['name'] . (($row['parent'] ?? '') !== '' ? ' (' . $row['parent'] . ')' : '');
        }

        return $out;
    }

    /**
     * Ключи записей, связанных с записью раздела
     *
     * @return int[]
     */
    protected function getSelectedIds(int $id): array
    {
        return array_map('intval', (new AQ())->asArray()->from((string)$this->params['table_relations'])
            ->select(DB::wrapName((string)$this->params['key']))
            ->where([$this->getRelationKey() => $id])
            ->fetchColumn());
    }

    /**
     * Ключи записей, выбранных в форме: «1,2,3»
     *
     * @return int[]
     */
    protected function getPostedIds(): array
    {
        return array_values(array_unique(array_filter(array_map('intval', explode(',', (string)$_POST[$this->name])))));
    }

    /**
     * Подписи связей записей показываемого списка (Table::$currentQuery, без него — всех записей):
     * id записи => «Имя, Имя»
     *
     * @return array<int, string>
     */
    protected function getListLabels(): array
    {
        $relationKey = 'r.' . DB::wrapName($this->getRelationKey());
        $query = (new AQ())->asArray()->from((string)$this->params['table_relations'], 'r')
            ->select(["$relationKey AS record_id", 'r.' . DB::wrapName((string)$this->params['key']) . ' AS value_id']);
        if (Table::$currentQuery) {
            $list = (clone Table::$currentQuery)
                ->select(DB::wrapName($this->table) . '.' . DB::wrapName($this->tablePk))
                ->orderBy(null)
                ->limit('');
            $query->where("$relationKey IN (" . $list->build() . ')')->bind($list->getBinds());
        }
        $relations = $query->all();

        $labels = $this->getValues(array_values(array_unique(array_map(fn($row) => (int)$row['value_id'], $relations))));
        $out = [];
        foreach ($relations as $row) {
            if (isset($labels[(int)$row['value_id']])) {
                $out[(int)$row['record_id']][] = $labels[(int)$row['value_id']];
            }
        }

        return array_map(fn(array $items) => implode(', ', $items), $out);
    }
}
