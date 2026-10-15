<?php

namespace Simflex\Core\DB;

use Simflex\Core\DB;

/**
 * Class ExprTypeSet
 * Syntax sugar for work with MySQL datatype SET
 * @example We need to remove 'value1' from field `tags` which value is 'value1,value2,value3':
 *          $model->update(['tags' => ExprTypeSet::remove('tags', 'value1')]);
 */
class ExprTypeSet extends Expr
{
    public static function add(string $fieldName, string $value): static
    {
        return new static("CONCAT(`$fieldName`,'," . DB::escape($value) . "')");
    }

    public static function remove(string $fieldName, string $value): static
    {
        return new static("
            TRIM(BOTH ',' FROM
              REPLACE(
                REPLACE(CONCAT(',',REPLACE(`$fieldName`, ',', ',,'), ','),'," . DB::escape($value) . ",', ''), ',,', ','
              )
            )");
    }

    public static function in(string $fieldName, string $value): static
    {
        return new static("FIND_IN_SET('" . DB::escape($value) . "',`$fieldName`)");
    }
}
