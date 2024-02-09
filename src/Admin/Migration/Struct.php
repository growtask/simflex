<?php

namespace Simflex\Admin\Migration;

use Simflex\Admin\Migration\Exception\FieldAlreadyExistsException;
use Simflex\Admin\Migration\Exception\FieldTypeNotFoundException;
use Simflex\Admin\Migration\Exception\TableNotFoundException;
use Simflex\Core\DB;

class Struct
{
    public const PARAM_POS = 'pos';
    public const PARAM_POS_GROUP = 'pos_group';

    public const POS_LEFT = '';
    /**
     * Inserts a new field in table
     *
     * @param string $table Table name
     * @param string $type Field type
     * @param string $name Field name
     * @param string $label Label
     * @param int $npp Order number
     * @param array $params Additional params
     * @return void
     * @throws FieldAlreadyExistsException
     * @throws TableNotFoundException
     */
    public static function addField(
        string $table,
        string $type,
        string $name,
        string $label,
        int $npp = 0,
        array $params = []
    ) {
        // get table ID
        $tableId = DB::result('select table_id from struct_table where name = ?', 0, [$table]);
        if (!$tableId) {
            throw new TableNotFoundException($table);
        }

        // check if field already exists
        if (DB::result('select id from struct_data where table_id = ? and name = ?', 0, [$tableId, $name])) {
            throw new FieldAlreadyExistsException($name);
        }

        // locate field
        $fieldId = DB::result('select field_id from struct_field where class = ?', 0, ['\\' . $type]);
        if (!$fieldId) {
            throw new FieldTypeNotFoundException($type);
        }

        // insert new entry
        DB::query('insert into struct_data (npp, table_id, field_id, name, label, params) values (?, ?, ?, ?, ?, ?)', [
            $npp,
            $tableId,
            $fieldId,
            $name,
            $label,
            serialize(['main' => $params])
        ]);
    }

    public const POS_RIGHT = 'right';

    /**
     * Deletes field
     *
     * @param string $table Table name
     * @param string $name Field name
     * @return void
     * @throws TableNotFoundException
     */
    public static function removeField(string $table, string $name)
    {
        // get table ID
        $tableId = DB::result('select table_id from struct_table where name = ?', 0, [$table]);
        if (!$tableId) {
            throw new TableNotFoundException($table);
        }

        // delete the entry
        DB::query('delete from struct_data where table_id = ? and name = ?', [$tableId, $name]);
    }
}