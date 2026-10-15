<?php

namespace Simflex\Core\Alert\Site;

use Simflex\Core\Core;
use Simflex\Core\Page;

class Alert
{
    protected static array $s;
    protected static bool $isInited = false;

    private function __construct()
    {

    }

    public static function init(): void
    {
        if (!self::$isInited) {
            Page::css(Core::webVendorPath() . '/Core/Alert/Site/alert.css');
            $_SESSION['plug_alert'] ??= [];
            self::$s = &$_SESSION['plug_alert'];
            self::$isInited = true;
        }
        if (!isset(self::$s)) {
            self::$s = [];
        }
    }

    /**
     * Добавить сообщение об ошибке
     * @param array|string $mixed сообщение
     * @param string|false $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function error(array|string $mixed, $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, 'error', $mixed);
        if ($goto) {
            header('location: ' . $goto);
            exit;
        }
    }

    /**
     * Добавить предупреждающее сообщение
     * @param array|string $mixed сообщение
     * @param string|false $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function warning(array|string $mixed, $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, 'warning', $mixed);
        if ($goto) {
            header('location: ' . $goto);
            exit;
        }
    }

    /**
     * Добавить сообщение об успешной операции
     * @param array|string $mixed сообщение
     * @param string|false $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function success(array|string $mixed, $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, 'success', $mixed);
        if ($goto) {
            header('location: ' . $goto);
            exit;
        }
    }

    /**
     * Добавить информационное сообщение
     * @param array|string $mixed сообщение
     * @param string|false $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function info(array|string $mixed, $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, 'info', $mixed);
        if ($goto) {
            header('location: ' . $goto);
            exit;
        }
    }

    /**
     * Добавить текст
     * @param array|string $mixed сообщение
     * @param string|false $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function text(array|string $mixed, $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, 'text', $mixed);
        if ($goto) {
            header('location: ' . $goto);
            exit;
        }
    }

    /**
     * Вывести все накопившиеся сообщения по идентификатору
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function output(string $page = ''): void
    {
        self::init();
        self::echoSpecified($page, 'error');
        self::echoSpecified($page, 'success');
        self::echoSpecified($page, 'warning');
        self::echoSpecified($page, 'info');
        self::echoSpecified($page, 'text');
        echo "\n" . '<div class="alert-buffer"' . ($page ? ' id="alert-buffer-' . $page . '"' : '') . '></div>' . "\n";
    }

    /**
     * Получить все накопившиеся сообщения по идентификатору. Формат HTML
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     * @return string HTML
     */
    public static function getHTML(string $page = ''): string
    {
        ob_start();
        self::output($page);
        return ob_get_clean();
    }

    /**
     * Проверка, есть или нет сообщения в буфере
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function has(string $page = ''): bool
    {
        self::init();
        $has = false;
        $has |= isset(self::$s[$page]['error']) && count(self::$s[$page]['error']);
        $has |= isset(self::$s[$page]['success']) && count(self::$s[$page]['success']);
        $has |= isset(self::$s[$page]['warning']) && count(self::$s[$page]['warning']);
        $has |= isset(self::$s[$page]['info']) && count(self::$s[$page]['info']);
        $has |= isset(self::$s[$page]['text']) && count(self::$s[$page]['text']);
        return $has;
    }

    /**
     * Проверка, есть или нет ошибки в буфере
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function hasErrors(string $page = ''): bool
    {
        self::init();
        return isset(self::$s[$page]['error']) && count(self::$s[$page]['error']);
    }

    protected static function echoSpecified(string $page, string $section): void
    {
        if (isset(self::$s[$page][$section]) && count(self::$s[$page][$section])) {
            $txt = '';
            if (count(self::$s[$page][$section]) == 1) {
                foreach (self::$s[$page][$section] as $txt) {
                    break;
                }
            } else {
                $txt = '<ul><li>' . implode('</li><li>', self::$s[$page][$section]) . '</li></ul>';
            }
            if ($txt) {
                if ($section == 'text') {
                    echo $txt;
                } else {
                    include dirname(__FILE__) . '/alert.tpl';
                }
                self::$s[$page][$section] = [];
            }
        }
    }

    protected static function pushMsg(string $page, string $section, array|string $mixed): void
    {
        if (is_array($mixed)) {
            if (!isset(self::$s[$page][$section])) {
                self::$s[$page][$section] = [];
            }
            self::$s[$page][$section] = array_merge(self::$s[$page][$section], $mixed);
        } else {
            self::$s[$page][$section][md5($mixed)] = $mixed;
        }
    }
}
