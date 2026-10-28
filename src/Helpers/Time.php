<?php

namespace Simflex\Core\Helpers;

use DateInterval;
use DateTime;

/**
 * Работа с датой и временем: разбор, преобразование форматов, русские названия месяцев и дней недели.
 *
 * Класс используется двумя способами: статические методы (`Time::mysql($value)`, `Time::convert(...)`) принимают
 * дату в любом поддерживаемом формате, а объект (`Time::create($value)->add('+1 day')->asMySQL()`) хранит дату
 * как unix-время и позволяет строить цепочки вызовов.
 *
 * Поддерживаемые входные форматы даты (`int|string`):
 * - `unix` - метка времени в секундах: `1318401180`;
 * - `normal` - `31.12.1999 10:00:00`, `31.12.1999 10:00`, `31.12.1999`;
 * - `mysql` - `1999-12-31 10:00:00`, `1999-12-31 10:00`, `1999-12-31`, `1999-12`;
 * - `excel` - `31/12/1999 10:00:00`, `31/12/1999`;
 * - `gmt` - формат ATOM: `1999-12-31T10:00:00+03:00`;
 * - `time` - только время `12:30` или `12:30:45`, превращается в количество секунд от полуночи.
 *
 * Дополнительные буквы формата в {@see Time::convert()} и {@see Time::format()} (помимо букв функции `date()`):
 * - `X` - месяц по-русски в именительном падеже, строчными: `январь`;
 * - `Q` - месяц по-русски в родительном падеже: `января`;
 * - `E` - месяц в именительном падеже с заглавной буквы: `Январь`;
 * - `K` - сокращённое название месяца (3 буквы): `янв`;
 * - `R` - сокращённый день недели (2 буквы): `ПН`;
 * - `J` - день недели полностью, строчными: `понедельник`.
 *
 * Значения `'-'`, `''` и `' - '` во всех методах-преобразователях возвращаются без изменений.
 */
class Time
{
    /** Определённые {@see Time::detectFormat()} форматы даты */
    public const string FORMAT_GMT = 'gmt';
    public const string FORMAT_NORMAL = 'normal';
    public const string FORMAT_MYSQL = 'mysql';
    public const string FORMAT_EXCEL = 'excel';
    public const string FORMAT_TIME = 'time';
    public const string FORMAT_UNIX = 'unix';

    /**
     * Время объекта в секундах
     */
    private int $time = 0;

    /** @var list<string> Дни недели (2 буквы), индекс 0 = воскресенье */
    public static array $wdays = ['ВС', 'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'];

    /** @var list<string> Дни недели полностью, индекс 0 = воскресенье */
    public static array $wdaysFull = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];

    /** @var list<string> Дни недели полностью строчными, индекс 0 = воскресенье */
    public static array $wdaysFullLC = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];

    /** @var list<string> Сокращённые названия месяцев, индекс января = 1 */
    public static array $months = ['', 'янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

    /** @var list<string> Месяцы в родительном падеже (31 «января»), индекс января = 1 */
    public static array $monthsDat = ['', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

    /** @var list<string> Месяцы в именительном падеже строчными, индекс января = 1 */
    public static array $monthsFull = ['', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];

    /** @var list<string> Месяцы в именительном падеже с заглавной буквы, индекс января = 1 */
    public static array $monthsFullUCF = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

    /**
     * @param int|string|false $time Дата в любом поддерживаемом формате; false - текущее время
     */
    public function __construct(int|string|false $time = false)
    {
        if ($time === false) {
            $time = time();
        }
        $this->time = self::unix($time);
    }

    /**
     * Создаёт объект класса
     *
     * @param int|string|false $time Дата в любом поддерживаемом формате; false - текущее время
     */
    public static function create(int|string|false $time = false): static
    {
        return new static($time);
    }

    /**
     * Добавляет к дате интервал
     *
     * @param string|int $interval Строка вида "+1 year" или число секунд (3600)
     */
    public function add(string|int $interval): static
    {
        $this->time = self::applyInterval($this->time, $interval) ?? $this->time;
        return $this;
    }

    /**
     * Возвращает дату в формате GMT (ATOM)
     */
    public function asGMT(): string
    {
        return date(DATE_ATOM, $this->time);
    }

    /**
     * Возвращает дату в формате unixtime, в секундах
     */
    public function asUnix(): int
    {
        return $this->time;
    }

    /**
     * Возвращает дату в формате MySQL: 1999-12-31 00:00:00
     *
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; не действует, если часы:минуты не показываются
     */
    public function asMySQL(bool|string $showHourMin = 'auto', bool|string $showSec = false): string
    {
        return self::convert($this->time, 'Y-m-d H:i:s', $showHourMin, $showSec);
    }

    /**
     * Возвращает дату в читаемом формате: 31.12.1999 00:00:00
     *
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; не действует, если часы:минуты не показываются
     */
    public function asNormal(bool|string $showHourMin = 'auto', bool|string $showSec = false): string
    {
        return self::convert($this->time, 'd.m.Y H:i:s', $showHourMin, $showSec);
    }

    /**
     * Возвращает дату в читаемом формате: 31 янв 1999 00:00:00
     *
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; не действует, если часы:минуты не показываются
     */
    public function asUser(bool|string $showHourMin = 'auto', bool|string $showSec = false): string
    {
        return self::convert($this->time, 'd K Y H:i:s', $showHourMin, $showSec);
    }

    /**
     * Возвращает дату в читаемом формате: 31 января 1999 00:00:00
     *
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; не действует, если часы:минуты не показываются
     */
    public function asUserFull(bool|string $showHourMin = 'auto', bool|string $showSec = false): string
    {
        return self::convert($this->time, 'd Q Y H:i:s', $showHourMin, $showSec);
    }

    /**
     * Возвращает год и месяц: 1999-01
     */
    public function asYearMonth(): string
    {
        return self::convert($this->time, 'Y-m', false);
    }

    /**
     * Форматирует дату объекта. Поддерживает буквы функции `date()` и дополнительные X, Q, E, K, R, J
     * (см. описание класса).
     *
     * @param string $format Формат, например "Y-m-d"
     */
    public function format(string $format): string
    {
        return self::convert($this->time, $format, false);
    }

    /**
     * Строковое представление объекта: дата в формате MySQL
     */
    public function __toString(): string
    {
        return $this->asMySQL();
    }

    /**
     * Определяет формат даты по её записи
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     * @return string Одна из констант FORMAT_*: gmt, normal, mysql, excel, time, unix
     */
    public static function detectFormat(int|string $value): string
    {
        $value = (string)$value;
        return match (true) {
            str_contains($value, 'T') => self::FORMAT_GMT,
            str_contains($value, '.') => self::FORMAT_NORMAL,
            str_contains($value, '-') => self::FORMAT_MYSQL,
            str_contains($value, '/') => self::FORMAT_EXCEL,
            str_contains($value, ':') => self::FORMAT_TIME,
            default => self::FORMAT_UNIX,
        };
    }

    /**
     * Преобразует дату в читаемый формат: 12 янв 2012 12:23:34. Если указано время, перед ним вставляется «в».
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; не действует, если часы:минуты не показываются
     */
    public static function user(int|string $value, bool|string $showHourMin = 'auto', bool|string $showSec = false): string
    {
        return self::userView($value, $showHourMin, $showSec, self::$months);
    }

    /**
     * Преобразует дату в читаемый формат: 12 января 2012 12:23:34. Если время не указано, в конце добавляется
     * «года»: 12 января 2012 года.
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; не действует, если часы:минуты не показываются
     */
    public static function userFull(int|string $value, bool|string $showHourMin = 'auto', bool|string $showSec = false): string
    {
        $ret = self::userView($value, $showHourMin, $showSec, self::$monthsDat);
        if (!str_contains($ret, ':')) {
            $ret .= ' года';
        }
        return $ret;
    }

    /**
     * Преобразует дату в формат MySQL: 2012-01-12 12:23:34
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; 'auto' - если есть в исходном формате
     */
    public static function mysql(int|string $value, bool|string $showHourMin = 'auto', bool|string $showSec = 'auto'): string
    {
        return self::convert($value, 'Y-m-d', $showHourMin, $showSec);
    }

    /**
     * Преобразует дату в читаемый формат normal: 12.01.2012 12:23:34
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; 'auto' - если есть в исходном формате
     */
    public static function normal(int|string $value, bool|string $showHourMin = 'auto', bool|string $showSec = 'auto'): string
    {
        return self::convert($value, 'd.m.Y', $showHourMin, $showSec);
    }

    /**
     * Возвращает временную метку unix в секундах (пример: 1318401180). Для формата `time` (12:30) возвращает
     * количество секунд от полуночи, для нераспознанного формата - 0.
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     */
    public static function unix(int|string $value): int
    {
        $format = self::detectFormat($value);
        $value = (string)$value;

        switch ($format) {
            case self::FORMAT_NORMAL:
                $value = self::padDateTime($value);
                preg_match("/(\d{2}).(\d{2}).(\d{4}) (\d{1,2}):(\d{2}):(\d{2})/", $value, $parts);
                return mktime((int)$parts[4], (int)$parts[5], (int)$parts[6], (int)$parts[2], (int)$parts[1], (int)$parts[3]);

            case self::FORMAT_GMT:
                return (int)strtotime($value);

            case self::FORMAT_MYSQL:
                if (strlen($value) === 7) {
                    $value .= '-01';
                }
                $value = self::padDateTime($value);
                preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/", $value, $parts);
                return mktime((int)$parts[4], (int)$parts[5], (int)$parts[6], (int)$parts[2], (int)$parts[3], (int)$parts[1]);

            case self::FORMAT_EXCEL:
                $value = self::padDateTime($value);
                // dd/mm/yyyy hh:ii:ss
                preg_match("/(\d{2})\/(\d{2})\/(\d{4}) (\d{1,2}):(\d{2}):(\d{2})/", $value, $parts);
                return mktime((int)$parts[4], (int)$parts[5], (int)$parts[6], (int)$parts[2], (int)$parts[1], (int)$parts[3]);

            case self::FORMAT_TIME:
                $parts = explode(':', $value);
                return match (count($parts)) {
                    3 => (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2],
                    2 => (int)$parts[0] * 3600 + (int)$parts[1] * 60,
                    default => 0,
                };

            default:
                return (int)$value;
        }
    }

    /**
     * Форматирует дату в нужный формат. Поддерживает буквы функции `date()` и дополнительные X, Q, E, K, R, J
     * (см. описание класса). Значения '-', '' и ' - ' возвращаются без изменений.
     *
     * Если в формате нет часов (H), а `$showHourMin` разрешает их показ, в конец формата добавляется ` H:i`
     * (и `:s` при `$showSec`).
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     * @param string $format Формат результата, например "Y-m-d"
     * @param bool|string $showHourMin Показывать часы:минуты; 'auto' - если есть в исходном формате
     * @param bool|string $showSec Показывать секунды; 'auto' - если есть в исходном формате; не действует,
     *        если часы:минуты не показываются
     */
    public static function convert(
        int|string $value,
        string $format,
        bool|string $showHourMin = 'auto',
        bool|string $showSec = 'auto'
    ): string {
        if (in_array($value, ['-', '', ' - '], true)) {
            return (string)$value;
        }

        $unix = self::unix($value);
        $sourceFormat = self::detectFormat($value);
        if ($sourceFormat === self::FORMAT_TIME) {
            return gmdate($format, $unix);
        }

        if (!str_contains($format, 'H')) {
            $parts = explode(':', (string)$value);
            $hasHourMin = count($parts) > 1;
            $hasSec = count($parts) === 3;
            $isUnix = $sourceFormat === self::FORMAT_UNIX;
            if (
                $hasHourMin && $showHourMin === 'auto' ||
                $showHourMin === true ||
                $isUnix && $showHourMin === 'auto'
            ) {
                $format .= ' H:i';
                if (
                    $hasSec && $showSec === 'auto' ||
                    $showSec === true ||
                    $isUnix && $showSec === 'auto'
                ) {
                    $format .= ':s';
                }
            }
        }

        // Пользовательские буквы заменяются в формате маркерами с номером месяца (m) или дня недели (w),
        // которые date() выводит как есть, а затем маркеры заменяются названиями
        $letters = [];
        foreach (self::getCustomLetters() as $letter => [$open, $close, $dateLetter]) {
            if (str_contains($format, $letter)) {
                $format = str_replace($letter, " $open#$dateLetter#$close ", $format);
                $letters[] = $letter;
            }
        }

        $ret = date($format, $unix);

        foreach ($letters as $letter) {
            [$open, $close, , $names] = self::getCustomLetters()[$letter];
            $pattern = '/ ' . preg_quote($open . '#', '/') . '(\d+)' . preg_quote('#' . $close, '/') . ' /i';
            $ret = (string)preg_replace_callback($pattern, static fn(array $m): string => $names[(int)$m[1]], $ret);
        }
        return $ret;
    }

    /**
     * Вычисляет время между двумя датами
     *
     * @param int|string $value1 Дата, из которой вычитаем
     * @param int|string $value2 Дата, которую вычитаем
     * @param string $to Формат результата: 'unix' - секунды (int), 'normal' - строка 02:09:30
     * @param bool $showSec Выводить секунды при формате 'normal'
     * @return int|string Секунды, строка времени или '-', если одна из дат равна '-'
     */
    public static function subTimes(int|string $value1, int|string $value2, string $to = 'unix', bool $showSec = true): int|string
    {
        if ($value1 === '-' || $value2 === '-') {
            return '-';
        }

        $resTime = self::unix($value1) - self::unix($value2);
        if ($to === 'normal') {
            return self::secondsToTime($resTime, $showSec);
        }
        return $resTime;
    }

    /**
     * Переводит секунды в строку времени: 129 в 00:02:09. Отрицательное значение выводится со знаком «-».
     *
     * @param int|float $seconds Количество секунд (округляется до целого)
     * @param bool $showSec Выводить секунды
     */
    public static function secondsToTime(int|float $seconds, bool $showSec = true): string
    {
        $total = (int)abs(round($seconds));
        $hour = intdiv($total, 3600);
        $minute = intdiv($total - $hour * 3600, 60);
        $second = $total - $hour * 3600 - $minute * 60;

        $ret = sprintf('%02d:%02d', $hour, $minute);
        if ($showSec) {
            $ret .= sprintf(':%02d', $second);
        }

        return $seconds >= 0 ? $ret : "-$ret";
    }

    /**
     * Переводит строку времени в секунды: 02:09 в 129, 1:02:09 в 3729. Пустое значение даёт 0.
     *
     * @param string $value Время вида «мм:сс» или «чч:мм:сс»
     */
    public static function unixtimeFromTime(string $value): int
    {
        if ($value === '' || $value === '0') {
            return 0;
        }
        $res = [];
        preg_match('/([0-9]*:)?([0-9]+):([0-9]+)/', $value, $res);
        return (int)($res[1] ?? 0) * 3600 + (int)($res[2] ?? 0) * 60 + (int)($res[3] ?? 0);
    }

    /**
     * Формирует массив периодов между датой начала и датой конца
     *
     * Пример: `formDates('2008-01-01', '2008-03-01', 1)` вернёт
     * ```
     * [
     *     ['period' => 'январь 2008', 'period_sys' => '01.2008', 'isnow' => false,
     *      'beg' => '2008-01-01 00:00:00', 'end' => '2008-01-31 23:59:59'],
     *     ['period' => 'февраль 2008', ...],
     * ]
     * ```
     * Конец периода всегда содержит 31 число (используется для выборок «до»). Для кварталов и лет ключи
     * `period_sys` и `isnow` не заполняются.
     *
     * @param int|string $beginDate Дата начала в любом поддерживаемом формате
     * @param int|string $endDate Дата конца в любом поддерживаемом формате
     * @param int $delimiter Длина периода: 1 - месяц, 2 - квартал, 3 - год
     * @return list<array<string, int|string|bool>>
     */
    public static function formDates(int|string $beginDate, int|string $endDate, int $delimiter): array
    {
        $endDate = self::mysql($endDate, true, true);

        $begDateU = self::unix($beginDate);
        $endDateU = self::unix($endDate);
        $begYear = (int)self::convert($beginDate, 'Y', false);
        $endYear = (int)self::convert($endDate, 'Y', false);
        $begMonth = (int)self::convert($beginDate, 'm', false);
        $endMonth = (int)self::convert($endDate, 'm', false);
        $ret = [];

        switch ($delimiter) {
            case 1:
                $cnt = (int)floor(($endDateU - $begDateU) / 2678400) + 1;
                for ($i = 0; $i < $cnt; $i++) {
                    $y = $begYear + intdiv($begMonth - 1 + $i, 12);
                    $mi = ($begMonth - 1 + $i % 12) % 12;
                    $ret[$i]['period'] = self::$monthsFull[$mi + 1] . ' ' . $y;
                    $ret[$i]['period_sys'] = sprintf('%02d', $mi + 1) . '.' . $y;
                    $ret[$i]['isnow'] = (int)date('m') === $mi + 1 && (int)date('Y') === $y;
                    $mi = sprintf('%02d', $mi + 1);
                    $ret[$i]['beg'] = $y . '-' . $mi . '-01 00:00:00';
                    $ret[$i]['end'] = $y . '-' . $mi . '-31 23:59:59';
                }
                break;
            case 2:
                $cnt = (int)floor(($endDateU - $begDateU) / 8035200) + 1;
                if ($endMonth - $begMonth <= 3 && (($begMonth - 1) % 3) !== 0) {
                    $cnt++;
                }
                for ($i = 0; $i < $cnt; $i++) {
                    $y = $begYear + intdiv(intdiv($begMonth - 1, 3) + $i, 4);
                    $qi = (intdiv($begMonth - 1, 3) + $i % 4) % 4 + 1;
                    $ret[$i]['period'] = $qi . ' квартал ' . $y;
                    $mib = sprintf('%02d', $qi * 3 - 2);
                    $mie = sprintf('%02d', $qi * 3);
                    $ret[$i]['beg'] = $y . '-' . $mib . '-01 00:00:00';
                    $ret[$i]['end'] = $y . '-' . $mie . '-31 23:59:59';
                }
                break;
            case 3:
                for ($i = $begYear; $i <= $endYear; $i++) {
                    $ret[$i - $begYear]['period'] = $i;
                    $ret[$i - $begYear]['beg'] = $i . '-01-01 00:00:00';
                    $ret[$i - $begYear]['end'] = $i . '-12-31 23:59:59';
                }
                break;
        }

        return $ret;
    }

    /**
     * Возвращает день недели в двухбуквенном формате (ПН, ВТ, СР...)
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     */
    public static function wday(int|string $value): string
    {
        return self::$wdays[(int)date('w', self::unix($value))];
    }

    /**
     * Возвращает день недели полностью (Понедельник)
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     */
    public static function wdayFull(int|string $value): string
    {
        return self::$wdaysFull[(int)date('w', self::unix($value))];
    }

    /**
     * Возвращает время из даты
     *
     * @param int|string $value Дата со временем в любом поддерживаемом формате
     * @param bool|string $showSec Показывать секунды; любое значение, кроме false, включает секунды (в том числе 'auto')
     * @return string 12:34 или 12:34:56
     */
    public static function extractTime(int|string $value, bool|string $showSec = 'auto'): string
    {
        $unix = self::unix(self::mysql($value, 'auto', $showSec));
        return date($showSec ? 'H:i:s' : 'H:i', $unix);
    }

    /**
     * Добавляет к дате интервал, сохраняя формат даты. Недопустимый интервал (0, не строка с пробелом)
     * оставляет значение без изменений.
     *
     * @param int|string $value Дата в любом поддерживаемом формате
     * @param string|int $interval Строка вида "+1 year" или число секунд (3600)
     * @param bool $toUnix Вернуть unix-время, не форматируя в исходный формат (работает быстрее)
     * @return int|string Дата в исходном формате (для unix и нераспознанных форматов - int) или исходное значение
     */
    public static function addInterval(int|string $value, string|int $interval, bool $toUnix = false): int|string
    {
        $format = self::detectFormat($value);
        $unix = self::applyInterval(self::unix($value), $interval);
        if ($unix === null) {
            return $value;
        }

        if ($toUnix) {
            return $unix;
        }
        return match ($format) {
            self::FORMAT_NORMAL => self::normal($unix),
            self::FORMAT_MYSQL => self::mysql($unix),
            self::FORMAT_GMT => date(DATE_ATOM, $unix),
            default => $unix,
        };
    }

    /**
     * Вычисляет разницу между двумя датами
     *
     * @param int|string|Time $value Дата, с которой сравниваем
     * @param bool $dropTime Не учитывать часы и минуты
     */
    public function diff(int|string|self $value, bool $dropTime = false): TimeDiff
    {
        $showHourMin = $dropTime ? false : 'auto';
        $datetime1 = new DateTime($this->asMySQL($showHourMin));
        $datetime2 = new DateTime($value instanceof self ? $value->asMySQL($showHourMin) : self::mysql($value, $showHourMin));
        return new TimeDiff($datetime1->diff($datetime2));
    }

    /**
     * Возвращает дату начала недели (понедельник) для указанной даты
     *
     * @param string $forDate Дата в формате Y-m-d
     * @return string Дата в формате Y-m-d
     */
    public static function weekStart(string $forDate): string
    {
        $dateTime = new DateTime($forDate);
        $dateTime->setISODate((int)$dateTime->format('o'), (int)$dateTime->format('W'), 1);
        return $dateTime->format('Y-m-d');
    }

    /**
     * Возвращает дату конца недели (воскресенье) для указанной даты
     *
     * @param string $forDate Дата в формате Y-m-d
     * @return string Дата в формате Y-m-d
     */
    public static function weekEnd(string $forDate): string
    {
        $dateTime = new DateTime($forDate);
        $dateTime->setISODate((int)$dateTime->format('o'), (int)$dateTime->format('W'), 7);
        return $dateTime->format('Y-m-d');
    }

    /**
     * Возвращает дату конца месяца для указанной даты
     *
     * @param string $forDate Дата в формате Y-m-d
     * @return string Дата в формате Y-m-d
     * @throws \Exception
     */
    public static function monthEnd(string $forDate): string
    {
        $dateTime = new DateTime($forDate);
        $dateTime = new DateTime($dateTime->add(new DateInterval('P1M'))->format('Y-m') . '-01');
        return $dateTime->sub(new DateInterval('P1D'))->format('Y-m-d');
    }

    /**
     * Общая часть user() и userFull(): дата dd.mm.YYYY с названием месяца из списка вместо номера
     *
     * @param list<string> $monthNames Названия месяцев с индексом января = 1
     */
    private static function userView(int|string $value, bool|string $showHourMin, bool|string $showSec, array $monthNames): string
    {
        $ret = self::convert($value, 'd.m.Y', $showHourMin, $showSec);
        $month = self::convert($value, 'm', false);
        $ret = str_replace(' ', ' в ', $ret);
        return str_replace(".$month.", ' ' . $monthNames[(int)$month] . ' ', $ret);
    }

    /**
     * Дополняет неполную дату до формата «дата время с секундами» (10 символов - дата, 16 - без секунд)
     */
    private static function padDateTime(string $value): string
    {
        if (strlen($value) === 10) {
            $value .= ' 00:00:00';
        }
        if (strlen($value) === 16) {
            $value .= ':00';
        }
        return $value;
    }

    /**
     * Применяет интервал к unix-времени
     *
     * @param string|int $interval Строка вида "+1 year" (с пробелом) или число секунд
     * @return int|null Новое время или null, если интервал пустой
     */
    private static function applyInterval(int $unix, string|int $interval): ?int
    {
        if (strpos((string)$interval, ' ') > 0) {
            return (int)strtotime((string)$interval, $unix);
        }
        if ((int)$interval !== 0) {
            return $unix + (int)$interval;
        }
        return null;
    }

    /**
     * Пользовательские буквы формата: [начало маркера, конец маркера, буква date() для номера, список названий]
     *
     * @return array<string, array{string, string, string, list<string>}>
     */
    private static function getCustomLetters(): array
    {
        return [
            'X' => ['@', '@', 'm', static::$monthsFull],
            'Q' => ['@@', '@@', 'm', static::$monthsDat],
            'E' => ['@~@', '@~@', 'm', static::$monthsFullUCF],
            'R' => ['@-', '-@', 'w', static::$wdays],
            'J' => ['@--', '--@', 'w', static::$wdaysFullLC],
            'K' => ['@-_-', '-_-@', 'm', static::$months],
        ];
    }
}
