<?php

namespace Simflex\Admin\Fields;

use Simflex\DB\AQ;
use Simflex\DB\DB;

/**
 * Таблица записей другой таблицы в форме (например, фото отзыва): строки добавляются, меняются и удаляются ajax-
 * действиями раздела vtAdd, vtEdit, vtDelete, страница строк — vtGet. Раздел задаёт запросы строк и их количества
 * (обычно в initTable()) и колонки в формате FieldTable. Файлы колонок image/file загружаются действием
 * upload_table_file раздела (Table::uploadTableFile())
 */
class FieldVirtualTable extends Field
{
    /**
     * Запрос строк страницы, первая колонка __id — id строки: запрос AQ (количество строк считается по нему же,
     * без ORDER BY и LIMIT) или SQL с параметрами $binds
     */
    public string|AQ $query = '';
    /** Запрос количества строк, если $query — SQL */
    public string $queryCount = '';
    /** @var array<string|int, mixed> параметры SQL-запросов $query и $queryCount (одни на оба запроса) */
    public array $binds = [];
    public array $cols = [];
    public string $button = '';
    public string $customjs = '';

    public function __construct($row)
    {
        parent::__construct($row);
        $this->isVirtual = true;
    }

    public function input($value): string
    {
        if (!($_REQUEST[$this->tablePk] ?? 0)) {
            return $this->render('vtable-notice');
        }

        $value = $this->getValue();
        $jsId = (string)crc32($this->inputName());
        $total = $this->getCount();

        return $this->render('vtable', compact('value', 'jsId', 'total')) . $this->customjs;
    }

    public function getCount(): mixed
    {
        if ($this->query instanceof AQ) {
            return (clone $this->query)->select('COUNT(*)')->orderBy(null)->limit('')->fetchScalar();
        }

        return DB::result($this->queryCount, 0, $this->binds);
    }

    /**
     * Строки страницы
     *
     * @return list<array<string, mixed>>
     */
    protected function getRows(): array
    {
        if ($this->query instanceof AQ) {
            return (clone $this->query)->asArray()->all();
        }

        return DB::assoc($this->query, false, false, $this->binds) ?: [];
    }

    public function getValue(): string|false
    {
        $vv = [];
        foreach ($this->getRows() as $r) {
            foreach ($r as $k => $v) {
                $v = (string)$v;
                if ($k === 'img_json') {
                    // первое по порядку изображение из значения FieldTable, без них — картинка по умолчанию
                    $js = json_decode($v, true)['v'] ?? [];
                    usort($js, fn($a, $b) => (int)($a['npp'] ?? 0) <=> (int)($b['npp'] ?? 0));
                    $r['img'] = $js[0]['img'] ?? asset('img/default-img.png');
                    unset($r['img_json']);
                    continue;
                }

                if (str_starts_with($v, 'RM:')) {
                    $d = explode(';;', substr($v, 3));
                    $r[$k] = $d[0];
                    $r['__rm_id'] = $d[1];
                }

                if (str_starts_with($v, 'RMJ:')) {
                    $d = explode(';;', substr($v, 4));
                    $r[$k] = $d[0];
                    $r['__rmj_id'] = $d[1];
                }
            }

            $vv[] = $r;
        }

        return json_encode([
            's' => $this->cols,
            'v' => $vv,
        ]);
    }

    public function getPOST(bool $simple = false, string|int|null $group = null): mixed
    {
        return '';
    }
}
