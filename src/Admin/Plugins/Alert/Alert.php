<?php

namespace Simflex\Admin\Plugins\Alert;


use Simflex\Core\Session;

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
     * @param string $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function error(array|string $mixed, string|false $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, "danger", $mixed);
        if ($goto) {
            header("location: " . $goto);
            exit;
        }
    }

    /**
     * Добавить предупреждающее сообщение
     * @param array|string $mixed сообщение
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function warning(array|string $mixed, string|false $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, "warning", $mixed);
        if ($goto) {
            header("location: " . $goto);
            exit;
        }
    }

    /**
     * Добавить сообщение об успешной операции
     * @param array|string $mixed сообщение
     * @param string $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function success(array|string $mixed, string|false $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, "success", $mixed);
        if ($goto) {
            header("location: " . $goto);
            exit;
        }
    }

    /**
     * Добавить информационное сообщение
     * @param array|string $mixed сообщение
     * @param string $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function info(array|string $mixed, string|false $goto = false, string $page = ''): void
    {
        self::init();
        self::pushMsg($page, "info", $mixed);
        if ($goto) {
            header("location: " . $goto);
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
        self::echoSpecified($page, "danger");
        self::echoSpecified($page, "success");
        self::echoSpecified($page, "warning");
        self::echoSpecified($page, "info");

        Session::set('admin.alerts', []);
    }

    /**
     * Получить все накопившиеся сообщения по идентификатору. Формат HTML
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     * @return string|false HTML
     */
    public static function getHTML(string $page = ''): string|false
    {
        ob_start();
        self::output($page);
        return ob_get_clean();
    }

    /**
     * Проверка, есть или нет сообщения в буфере
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function has(string $page = ''): int|bool
    {
        self::init();
        return !empty(self::$s[$page]["danger"])
            || !empty(self::$s[$page]["success"])
            || !empty(self::$s[$page]["warning"])
            || !empty(self::$s[$page]["info"]);
    }

    /**
     * Проверка, есть или нет ошибки в буфере
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function hasErrors(string $page = ''): bool
    {
        self::init();
        return !empty(self::$s[$page]["danger"]);
    }

    protected static function echoSpecified(string $page, string $section): void
    {
        if (isset(self::$s[$page][$section]) && count(self::$s[$page][$section])) {
            $txt = "";
            if (count(self::$s[$page][$section]) == 1) {
                foreach (self::$s[$page][$section] as $txt) {
                    break;
                }
            } else {
                $txt = "<ul><li>" . implode("</li><li>", self::$s[$page][$section]) . "</li></ul>";
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

                include dirname(__FILE__) . '/alert.tpl';
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

        Session::set('admin.alerts', self::$s);
    }

}
