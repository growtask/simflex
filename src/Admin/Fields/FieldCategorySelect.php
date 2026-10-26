<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\DB;

/**
 * @todo Incomplete: для таблицы shared_category нет миграций, поэтому этот код падает на SQL
 *       в реальном окружении. Будет вынесено в отдельный composer-пакет.
 */
class FieldCategorySelect extends Field
{
    public function input($value): string
    {
        $this->syncCategories();

        $selected = explode(',', $value);
        $options = [];
        foreach (DB::assoc('SELECT name FROM shared_category') as $cat) {
            $options[] = [$cat['name'], $cat['name'], in_array($cat['name'], $selected)];
        }
        return $this->render('select', [
            'name' => $this->inputName() . '[]',
            'onchange' => null,
            'class' => 'form-control cat-select',
            'multiple' => true,
            'wrap' => false,
            'options' => $options,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        $data = $_POST[$this->inputName()] ?: [];
        foreach ($data as $val) {
            if (!DB::result('SELECT COUNT(*) as c FROM shared_category WHERE name = ?', 'c', [$val])) {
                DB::query('INSERT INTO shared_category (name) VALUES (?)', [$val]);
            }
        }

        return "'" . DB::escape(implode(',', $data)) . "'";
    }

    protected function syncCategories(): void
    {
        // DB::assoc возвращает список строк-массивов: array_diff по ним сравнивает "Array" с "Array",
        // поэтому сравниваем плоские списки имён
        $realCats = array_column(DB::assoc("SELECT content_id, title FROM content WHERE template = 'category'") ?: [], 'content_id', 'title');
        $existing = array_column(DB::assoc('SELECT name FROM shared_category') ?: [], 'name');

        $toAdd = array_diff(array_map('strval', array_keys($realCats)), $existing);
        foreach ($toAdd as $name) {
            DB::query('INSERT INTO shared_category (name, linked_to) VALUES (?, ?)', [$name, $realCats[$name]]);
        }
    }
}
