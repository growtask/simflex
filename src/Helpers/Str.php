<?php

namespace Simflex\Helpers;

class Str
{
    /**
     * Русские буквы в привычном для URL виде (я → ya, ж → zh, щ → sch), ISO 9 из ICU дает вместо этого â, ž, ŝ
     */
    protected const CYRILLIC = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
        'я' => 'ya',
    ];

    /**
     * Слаг для URL: "Вторая запись" → "vtoraya-zapis"
     */
    public static function translite($str): string
    {
        $str = strtr(mb_strtolower((string)$str), static::CYRILLIC);
        // прочие алфавиты и диакритика
        $str = \Transliterator::create('Any-Latin; Latin-ASCII; Lower()')->transliterate($str);
        $str = preg_replace('/[^a-z0-9\\- ]/', '', $str);
        return trim(preg_replace('/[ -]+/', '-', $str), '-');
    }

    public static function sizeToStr(string $path): string
    {
        $fileSize = filesize($_SERVER['DOCUMENT_ROOT'] . $path);
        $sizeDenom = 'б';
        if ($fileSize > 1024) {
            $fileSize /= 1024;
            $sizeDenom = 'кб';
        }
        if ($fileSize > 1024) {
            $fileSize /= 1024;
            $sizeDenom = 'мб';
        }
        if ($fileSize > 1024) {
            $fileSize /= 1024;
            $sizeDenom = 'гб';
        }

        return round($fileSize) . $sizeDenom;
    }

    public static function toCamel(string $str, bool $firstIsLower = true): string
    {
        if ($firstIsLower) {
            $parts = explode('_', $str);
            $result = $parts[0] . implode('', array_map('ucfirst', array_slice($parts, 1)));
        } else {
            $result = str_replace('_', '', ucwords($str, '_'));
        }
        return $result;
    }

    public static function toUnderscore(string $str): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * Генерирует случайную строку из букв и цифр.
     *
     * @param int $size Длина строки
     * @return string Строка
     * @throws \Exception
     */
    public static function random(int $size): string
    {
        static $alphabet = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';

        $str = '';
        while ($size--) {
            $str .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $str;
    }

    /**
     * @param int $num Число
     * @param string $s Основа слова. Для слов женского рода без окончания
     * @param bool $isMale Мужской род или нет
     * @return string Итоговая строка
     */
    public static function pluralize(int $num, string $s, bool $isMale = true, bool $iNum = true): string
    {
        $isNeuter = str_ends_with($s, 'е');
        if ($isNeuter) {
            $s = mb_substr($s, 0, mb_strlen($s) - 1);
        }

        $out = ($iNum ? $num : '') . ' ' . $s;
        if ($num % 100 >= 10 && $num % 100 < 15) {
            return $out . ($isNeuter ? 'ий' : ($isMale ? 'ов' : ''));
        }

        switch ($num % 10) {
            case 1:
                $out .= $isNeuter ? 'е' : ($isMale ? '' : 'а');
                break;
            case 2:
            case 3:
            case 4:
                $out .= $isNeuter ? 'я' : ($isMale ? 'а' : (preg_match('/[гкхжчшщ]$/u', $s) ? 'и' : 'ы'));
                break;
            default:
                $out .= $isNeuter ? 'ий' : ($isMale ? 'ов' : '');
                break;
        }

        return trim($out);
    }

    /**
     * @param int $num Число
     * @param string $s Основа слова. Для слов женского рода без окончания
     * @param bool $isMale Мужской род или нет
     * @return string Итоговая строка
     */
    public static function pluralizeParental(int $num, string $s, bool $isMale = true): string
    {
        $out = $num . ' ' . $s;
        if ($num % 100 >= 10 && $num % 100 < 15) {
            return $out . ($isMale ? 'ов' : '');
        }

        switch ($num % 10) {
            case 1:
                $out .= $isMale ? 'а' : 'ы';
                break;
            default:
                $out .= $isMale ? 'ов' : '';
                break;
        }

        return $out;
    }

    public static function spellNum(int $num): string
    {
        $f = new \NumberFormatter('ru', \NumberFormatter::SPELLOUT);
        return $f->format($num);
    }

    public static function sumToNumber(float $sum): string
    {
        $value = explode('.', number_format($sum, 2, '.', ''));
        $f = new \NumberFormatter('ru', \NumberFormatter::SPELLOUT);
        $str = $f->format($value[0]);
        $num = $value[0] % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1:
                $rub = 'рубль';
                break;
            case 2:
            case 3:
            case 4:
                $rub = 'рубля';
                break;
            default:
                $rub = 'рублей';
        }

        return $str . ' ' . $rub . ' ' . $value[1] . ' копеек';
    }

    public static function price(float $sum): string
    {
        return number_format($sum, 0, '.', ' ') . ' ₽';
    }

    /**
     * Подстановка в строку. Заменяет подстроки {key} в тексте значениями из массива $data.
     * @param string $in Исходная строка
     * @param array $data Массив значений для подстановки
     * @return string Строка с подставленными значениями
     */
    public static function interpolate(string $in, array $data): string
    {
        return str_replace(array_map(fn(string $key) => sprintf('{%s}', $key), array_keys($data)), array_values($data), $in);
    }
}
