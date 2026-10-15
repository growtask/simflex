<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\DB;
use Simflex\Core\DB\AQ;
use Simflex\Admin\Structure\Repository as StructureRepository;

class FieldInt extends Field
{
    public mixed $defaultValue = 0;

    public string $filterAndWhere = '';
    /** @var callable return [x => [y => [label => z], q => [label => k]]] */
    public \Closure|array|string $filterDataProvider;

    public function __construct($row)
    {
        parent::__construct($row);
        $this->isNumeric = !$this->fk;
        $this->filterDataProvider = function () {
            return $this->tree();
        };
    }

    protected function bigint($value): string
    {
        return sprintf('%.0f', $value);
    }

    /**
     * Finds the column of $table that is marked as the self-referencing "parent" (fk_is_pid) column.
     *
     * @return string Wrapped column name, or '0' if the table has no such column
     */
    protected function fkPidColumn(string $table): string
    {
        $fkTableFields = StructureRepository::fields($table);
        foreach ($fkTableFields as $fkTableField) {
            if ($fkTableField['class'] == 'FieldInt' || str_ends_with($fkTableField['class'], '\\FieldInt')) {
                $fkParams = $fkTableField['params'];
                if (!empty($fkParams['main']['fk_is_pid'])) {
                    return DB::wrapName($fkTableField['name']);
                }
            }
        }

        return '0';
    }

    public function &tree(): array
    {
        $tree = [];
        $fkPID = $this->fkPidColumn($this->fk->table);

        $query = (new AQ())
            ->select("{$this->fk->key} id, $fkPID pid, {$this->fk->label} label")
            ->from($this->fk->table)
            ->orderBy($this->fk->key);
        if (isset($_REQUEST[$this->fk->key])) {
            $query->andWhere("{$this->fk->key} <> " . $this->bigint($_REQUEST[$this->fk->key]));
        }
        if ($this->filterAndWhere) {
            $query->andWhere($this->filterAndWhere);
        }
        $r = DB::query($query);
        while ($row = DB::fetch($r)) {
            $tree[$this->bigint($row['pid'])][$this->bigint($row['id'])] = $row;
        }
        return $tree;
    }

    public function input($value): string
    {
        if (!$this->fk) {
            return $this->render('input', ['value' => $value] + $this->inputVars());
        }

        if ($this->params['fk_is_pid']) {
            $pidColumn = $this->fkPidColumn($this->params['fk_table']);
            $items = DB::assoc("select `{$this->params['fk_key']}` as id, `{$this->params['fk_label']}` as name, $pidColumn as pid from `{$this->params['fk_table']}` limit 20");
            $sel = DB::query("select `{$this->params['fk_key']}` as id, `{$this->params['fk_label']}` as name, $pidColumn as pid from
                                  `{$this->params['fk_table']}` where {$this->params['fk_key']} = ?", [$value]);
        } else {
            $items = DB::assoc("select `{$this->params['fk_key']}` as id, `{$this->params['fk_label']}` as name from `{$this->params['fk_table']}` limit 20");
            $sel = DB::query("select `{$this->params['fk_key']}` as id, `{$this->params['fk_label']}` as name from 
                                  `{$this->params['fk_table']}` where {$this->params['fk_key']} = ?", [$value]);
        }

        $sel = DB::fetch($sel) ?: [];

        $listItems = [];
        $hasId = false;
        foreach ($items as $r) {
            if ($r['id'] == ($sel['id'] ?? null)) {
                $hasId = true;
            }

            if ($this->params['fk_is_pid']) {
                $pidName = DB::result('select title from content where content_id = ?', 0, [$r['pid']]);
                if ($pidName) {
                    $r['name'] = $pidName . ' -> ' . $r['name'];
                }
            }
            $listItems[] = [$r['id'], $r['name']];
        }

        if (!$hasId && $sel) {
            if ($this->params['fk_is_pid']) {
                $pidName = DB::result('select title from content where content_id = ?', 0, [$sel['pid']]);
                if ($pidName) {
                    $sel['name'] = $pidName . ' -> ' . $sel['name'];
                }
            }
            array_unshift($listItems, [$sel['id'], $sel['name']]);
        }

        return $this->dropdown([
            'name' => $this->name,
            'value' => !$value ? '' : $value,
            'onchange' => $this->onchange,
            'readonly' => $this->readonly,
            'disabled' => $this->readonly,
            'action' => 'searchInt',
            'search' => true,
            'current' => $sel['name'] ?? '',
            'items' => $listItems,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        return $this->e2n && $_POST[$this->name] === '' ? null : (int)$_POST[$this->name];
    }

    public function filter($value): void
    {
        if (!$this->filter) {
            return;
        }
        if ($this->fk) {
            $sel = DB::query("select `{$this->params['fk_key']}` as id, `{$this->params['fk_label']}` as name from
                                      `{$this->params['fk_table']}` where {$this->params['fk_key']} = ?", [$value]);
            $sel = DB::fetch($sel) ?: [];

            echo $this->dropdown([
                'name' => 'filter[' . $this->name . ']',
                'value' => $value,
                'action' => 'searchInt',
                'search' => true,
                'items' => $sel ? [[$sel['id'], $sel['name']]] : [],
            ]);
        } else {
            parent::filter($value);
        }
    }

    public static function typeLabel(): string
    {
        return 'Целое число';
    }

    public static function typeParams(): array
    {
        return [
            [
                'name' => 'is_fk',
                'label' => 'Внешний ключ',
                'type' => \Simflex\Admin\Fields\FieldBool::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'fk_table',
                'label' => 'Внешний ключ. Таблица',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'fk_key',
                'label' => 'Внешний ключ. Ключ',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'fk_label',
                'label' => 'Внешний ключ. Ярлык',
                'type' => \Simflex\Admin\Fields\FieldString::class,
                'help' => '',
                'default_value' => '',
            ],
            [
                'name' => 'fk_is_pid',
                'label' => 'Внешний ключ. Поле PID',
                'type' => \Simflex\Admin\Fields\FieldBool::class,
                'help' => '',
                'default_value' => '',
            ],
        ];
    }
}
