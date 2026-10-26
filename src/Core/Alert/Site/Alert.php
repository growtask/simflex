<?php

namespace Simflex\Core\Alert\Site;

use Simflex\Core\Core;
use Simflex\Core\Page;
use Simflex\Core\View;

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
     * @param string|false $goto (по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
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
     * @param string|false $goto (по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
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
     * @param string|false $goto (по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
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
     * @param string|false $goto (по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
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
     * @param string|false $goto (по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
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
     * Получить все накопившиеся сообщения по идентификатору и очистить буфер. Формат HTML
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
     * @return string HTML
     */
    public static function getHTML(string $page = ''): string
    {
        self::init();
        return self::renderSection($page, 'error')
            . self::renderSection($page, 'success')
            . self::renderSection($page, 'warning')
            . self::renderSection($page, 'info')
            . self::renderSection($page, 'text')
            . "\n" . '<div class="alert-buffer"' . ($page ? ' id="alert-buffer-' . $page . '"' : '') . '></div>' . "\n";
    }

    /**
     * Проверка, есть или нет сообщения в буфере
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
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
     * @param string $page (по умолчанию '') ограничение, идентификатор. Например: "profile"
     */
    public static function hasErrors(string $page = ''): bool
    {
        self::init();
        return isset(self::$s[$page]['error']) && count(self::$s[$page]['error']);
    }

    protected static function renderSection(string $page, string $section): string
    {
        $html = '';
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
                $html = $section == 'text'
                    ? $txt
                    : View::render(__DIR__ . '/alert.tpl', ['section' => $section, 'txt' => $txt]);
                self::$s[$page][$section] = [];
            }
        }
        return $html;
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
