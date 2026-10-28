<?php

namespace Simflex\DB;

/**
 * Класс ExprOr
 * Объединяет условия через OR. Значения экранируются прямо в выражении (без параметров запроса),
 * поэтому его можно безопасно использовать внутри Where / AQ::where() / Model::find().
 *
 * Использование:
 * $where = ['param' => 1, new ExprOr(['param2' => 2, 'param3' => [3, 4]])];
 * Model::findOne($where);
 * С вложенным Expr: Model::findOne(new ExprOr(['param2' => 2, new Expr('param2 IN (4,5)')]));
 */
class ExprOr extends Expr
{
    protected array $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
        parent::__construct('');
    }

    public function __toString(): string
    {
        $values = [];
        foreach ($this->conditions as $key => $value) {
            if ((string)$key === (string)(int)$key) {
                if ($value = (string)$value) {
                    $values[] = $value;
                }
                continue;
            }

            $column = implode('.', array_map(fn($i) => DB::wrapName($i), explode('.', $key)));
            if (is_null($value)) {
                $values[] = "$column IS NULL";
            } elseif (is_array($value)) {
                if ($value) {
                    $values[] = "$column IN (" . implode(',', array_map(fn($v) => DB::wrapString((string)$v), $value)) . ')';
                }
            } else {
                $values[] = "$column = " . DB::wrapString((string)$value);
            }
        }

        return $values ? '(' . implode(' OR ', $values) . ')' : '(0)';
    }
}
