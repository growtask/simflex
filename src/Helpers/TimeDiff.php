<?php

namespace Simflex\Core\Helpers;

use DateInterval;

/**
 * Разница между двумя датами: обёртка над {@see DateInterval} с выводом в виде «2 дня 3 часа 40 минут».
 * Создаётся методом {@see Time::diff()}.
 */
class TimeDiff
{
    public function __construct(private readonly DateInterval $value)
    {
    }

    /**
     * Количество полных дней между датами
     */
    public function __toString(): string
    {
        return (string)$this->value->days;
    }

    /**
     * Форматирует интервал средствами {@see DateInterval::format()}
     *
     * @see https://www.php.net/manual/ru/dateinterval.format.php
     * @param string $format %d - дни, %Y - годы, %H - часы и т.п.
     */
    public function format(string $format): string
    {
        return $this->value->format($format);
    }

    /**
     * Возвращает строковое представление интервала из первых значимых (ненулевых) единиц
     *
     * @param int $ss Максимальное количество единиц
     * @return string Например, «2 дня 3 часа 40 минут»
     */
    public function significant(int $ss = 3): string
    {
        $parts = [];
        if ($this->value->y) {
            $parts[] = $this->value->y . ' ' . Declension::byCount($this->value->y, 'год', 'года', 'лет');
        }
        if ($this->value->m) {
            $parts[] = $this->value->m . ' ' . Declension::byCountMonths($this->value->m);
        }
        if ($this->value->d) {
            $parts[] = $this->value->d . ' ' . Declension::byCountDays($this->value->d);
        }
        if ($this->value->h) {
            $parts[] = $this->value->h . ' ' . Declension::byCount($this->value->h, 'час', 'часа', 'часов');
        }
        if ($this->value->i) {
            $parts[] = $this->value->i . ' ' . Declension::byCount($this->value->i, 'минута', 'минуты', 'минут');
        }
        if ($this->value->s) {
            $parts[] = $this->value->s . ' ' . Declension::byCount($this->value->s, 'секунда', 'секунды', 'секунд');
        }
        return implode(' ', array_slice($parts, 0, $ss));
    }

    /**
     * Возвращает исходный интервал
     */
    public function getRawDateInterval(): DateInterval
    {
        return $this->value;
    }
}
