<?php

namespace Simflex\Admin\Fields;

use Simflex\Admin\Fields\Field;
use Simflex\Core\DB;

/**
 * @todo Incomplete: there are no migrations for the shared_category table, so this code fails on SQL
 *       in a real environment. To be moved to a separate composer package.
 */
class FieldCategorySelect extends Field
{
    public function input($value): string
    {
        $this->syncCategories();

        $existing = DB::assoc('SELECT name FROM shared_category');

        $out = '<select name="' . $this->inputName() . '[]" class="form-control cat-select" multiple>';
        foreach ($existing as $cat) {
            $out .= '<option ' . (in_array($cat['name'], explode(',', $value)) ? ' selected' : '') . '>' . $cat['name'] . '</option>';
        }
        return $out .'</select>';
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
        $realCats = array_column(DB::assoc('SELECT content_id, title FROM content WHERE template_id = 6') ?: [], 'content_id', 'title');
        $existing = array_column(DB::assoc('SELECT name FROM shared_category') ?: [], 'name');

        $toAdd = array_diff(array_map('strval', array_keys($realCats)), $existing);
        foreach ($toAdd as $name) {
            DB::query('INSERT INTO shared_category (name, linked_to) VALUES (?, ?)', [$name, $realCats[$name]]);
        }
    }
}
