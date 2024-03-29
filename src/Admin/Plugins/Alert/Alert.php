<?php

namespace Simflex\Admin\Plugins\Alert;


class Alert {

    protected static $s;
    protected static $isInited = false;

    private function __construct() {
        
    }

    public static function init() {
        if (!self::$isInited) {
            self::$s = & $_SESSION['admin_plug_alert'];
            self::$isInited = true;
        }
        if (!isset(self::$s)) {
            self::$s = array();
        }
    }

    /**
     * Добавить сообщение об ошибке
     * @param array|string $mixed сообщение
     * @param string $goto (optional = false) header("location: ".$goto) and exit;
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     */
    public static function error($mixed, $goto = false, $page = '') {
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
    public static function warning($mixed, $goto = false, $page = '') {
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
    public static function success($mixed, $goto = false, $page = '') {
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
    public static function info($mixed, $goto = false, $page = '') {
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
    public static function output($page = '') {
        self::init();
        self::echoSpecified($page, "danger");
        self::echoSpecified($page, "success");
        self::echoSpecified($page, "warning");
        self::echoSpecified($page, "info");
    }

    /**
     * Получить все накопившиеся сообщения по идентификатору. Формат HTML
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     * @return string HTML
     */
    public static function getHTML($page = '') {
        ob_start();
        self::echoAll($page);
        return ob_get_clean();
    }

    /**
     * Проверка, есть или нет сообщения в буфере
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     * @return bool
     */
    public static function has($page = '') {
        self::init();
        $has = false;
        $has |= isset(self::$s[$page]["error"]) && count(self::$s[$page]["error"]);
        $has |= isset(self::$s[$page]["success"]) && count(self::$s[$page]["success"]);
        $has |= isset(self::$s[$page]["warning"]) && count(self::$s[$page]["warning"]);
        $has |= isset(self::$s[$page]["info"]) && count(self::$s[$page]["info"]);
        return $has;
    }

    /**
     * Проверка, есть или нет ошибки в буфере
     * @param string $page (optional = '') ограничение, идентификатор. Например: "profile"
     * @return bool
     */
    public static function hasErrors($page = '') {
        self::init();
        return isset(self::$s[$page]["error"]) && count(self::$s[$page]["error"]);
    }

    protected static function echoSpecified($page, $section) {
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
                    case 'success': $type = 'success'; $title = 'Успешно!'; break;
                    case 'danger': $type = 'error'; $title = 'Ошибка!'; break;
                    case 'warning': $type = 'notice'; $title = 'Внимание!'; break;
                    case 'info':
                    default: $type = 'info'; $title = 'Информация'; break;
                }

                include dirname(__FILE__) . '/alert.tpl';
                self::$s[$page][$section] = array();
            }
        }
    }

    protected static function pushMsg($page, $section, $mixed) {
        if (is_array($mixed)) {
            if (!isset(self::$s[$page][$section])) {
                self::$s[$page][$section] = array();
            }
            self::$s[$page][$section] = array_merge(self::$s[$page][$section], $mixed);
        } else {
            self::$s[$page][$section][md5($mixed)] = $mixed;
        }
    }

}
