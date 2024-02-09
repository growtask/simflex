<?php


namespace Simflex\Core\Helpers;


class Str
{

    public static function translite($str)
    {
        $tl = \Transliterator::create('Any-Latin; Latin-ASCII; Lower()');
        return str_replace(' ', '-', preg_replace("/[^A-Za-z0-9\\- ]/", '',
                $tl->transliterate($str)
        ));
    }

    /**
     * @param $string
     * @return string
     */
    public static function transliteOld($string)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => "'", 'ы' => 'y', 'ъ' => "'",
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' =>  'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => "'", 'Ы' => 'Y', 'Ъ' => "'",
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        $string = strtr($string, $converter);
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('@\-+@', '-', $string);
        $string = preg_replace('@[^a-z0-9\-]@i', '', $string);
        $string = strtolower($string);
        return trim($string);
    }

    /**
     * @param string $str
     * @param bool $firstIsLower
     * @return string|string[]
     */
    public static function toCamel($str, $firstIsLower = true)
    {
        if ($firstIsLower) {
            $parts = explode('_', $str);
            $result = $parts[0] . implode('', array_map('ucfirst', array_slice($parts, 1)));
        } else {
            $result = str_replace('_', '', ucwords($str, '_'));
        }
        return $result;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function toUnderscore($str)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * Generates random alphanumeric string.
     *
     * @param int $size Out string size
     * @return string Out string
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
     * @param int $num Number
     * @param string $s Base string. Omit ending for feminine words
     * @param bool $isMale Whether masculine or not
     * @return string Final string
     */
    public static function pluralize(int $num, string $s, bool $isMale = true, bool $iNum = true): string
    {
        $out = ($iNum ? $num : '') . ' ' . $s;
        if ($num % 100 >= 10 && $num % 100 < 15) {
            return $out . ($isMale ? 'ов' : '');
        }

        switch ($num % 10) {
            case 1:
                $out .= $isMale ? '' : 'а';
                break;
            case 2:
            case 3:
            case 4:
                $out .= $isMale ? 'а' : (str_ends_with('ч') ? 'и' : 'ы');
                break;
            default:
                $out .= $isMale ? 'ов' : '';
                break;
        }

        return trim($out);
    }

    /**
     * @param int $num Number
     * @param string $s Base string. Omit ending for feminine words
     * @param bool $isMale Whether masculine or not
     * @return string Final string
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
            case 1: $rub = 'рубль'; break;
            case 2:
            case 3:
            case 4: $rub = 'рубля'; break;
            default: $rub = 'рублей';
        }

        return $str . ' ' . $rub . ' ' . $value[1] . ' копеек';
    }
}