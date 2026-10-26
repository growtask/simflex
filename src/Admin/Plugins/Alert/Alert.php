<?php

namespace Simflex\Admin\Plugins\Alert;

use Simflex\Core\Session;
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
            self::$s = Session::get('admin.alerts') ?? [];
            self::$isInited = true;
        }
        if (!isset(self::$s)) {
            self::$s = [];
        }
    }

    /**
     * Добавить сообщение об ошибке
     * @param array|string $mixed сообщение
     * @param string $goto (необязательный, по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (необязательный, по умолчанию '') ограничение, идентификатор. Например: "profile"
     */
    public static function error(array|string $mixed, string|false $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, 'danger', $mixed);
        if ($goto) {
            header('location: ' . $goto);
            exit;
        }
    }

    /**
     * Добавить предупреждающее сообщение
     * @param array|string $mixed сообщение
     * @param string $page (необязательный, по умолчанию '') ограничение, идентификатор. Например: "profile"
     */
    public static function warning(array|string $mixed, string|false $goto = false, string $page = ''): void
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
     * @param string $goto (необязательный, по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (необязательный, по умолчанию '') ограничение, идентификатор. Например: "profile"
     */
    public static function success(array|string $mixed, string|false $goto = false, string $page = ''): void
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
     * @param string $goto (необязательный, по умолчанию false) header("location: ".$goto) и exit;
     * @param string $page (необязательный, по умолчанию '') ограничение, идентификатор. Например: "profile"
     */
    public static function info(array|string $mixed, string|false $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, 'info', $mixed);
        if ($goto) {
            header('location: ' . $goto);
            exit;
        }
    }

    /**
     * Получить все накопившиеся сообщения по идентификатору и очистить буфер. Формат HTML
     * @param string $page (необязательный, по умолчанию '') ограничение, идентификатор. Например: "profile"
     * @return string HTML
     */
    public static function getHTML(string $page = ''): string
    {
        self::init();
        $html = self::renderSection($page, 'danger')
            . self::renderSection($page, 'success')
            . self::renderSection($page, 'warning')
            . self::renderSection($page, 'info');

        Session::set('admin.alerts', []);
        return $html;
    }

    /**
     * Проверка, есть или нет сообщения в буфере
     * @param string $page (необязательный, по умолчанию '') ограничение, идентификатор. Например: "profile"
     */
    public static function has(string $page = ''): int|bool
    {
        self::init();
        return !empty(self::$s[$page]['danger'])
            || !empty(self::$s[$page]['success'])
            || !empty(self::$s[$page]['warning'])
            || !empty(self::$s[$page]['info']);
    }

    /**
     * Проверка, есть или нет ошибки в буфере
     * @param string $page (необязательный, по умолчанию '') ограничение, идентификатор. Например: "profile"
     */
    public static function hasErrors(string $page = ''): bool
    {
        self::init();
        return !empty(self::$s[$page]['danger']);
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
                switch ($section) {
                    case 'success':
                        $type = 'success';
                        $title = 'Успешно!';
                        break;
                    case 'danger':
                        $type = 'error';
                        $title = 'Ошибка!';
                        break;
                    case 'warning':
                        $type = 'notice';
                        $title = 'Внимание!';
                        break;
                    case 'info':
                    default:
                        $type = 'info';
                        $title = 'Информация';
                        break;
                }

                $html = View::render(__DIR__ . '/alert.tpl', ['type' => $type, 'title' => $title, 'txt' => $txt]);
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

        Session::set('admin.alerts', self::$s);
    }
}
