<?php

namespace Simflex\Core\DB;

use Simflex\Core\DB;

/**
 * Класс ExprTypeSet
 * Синтаксический сахар для работы с типом MySQL SET
 * @example Нужно убрать 'value1' из поля `tags` со значением 'value1,value2,value3':
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
