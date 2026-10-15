<?php

/**
 * Стабы для IDE (PhpStorm и т.п.). Файл никогда не подключается и не выполняется.
 *
 * Константы ниже объявляются в проекте-шаблоне (simflex-template), а не в ядре.
 * Здесь они нужны только для того, чтобы IDE не ругалась на них в коде ядра.
 */

exit('IDE stubs file, do not include');

/**
 * Корень проекта-шаблона. Задаётся в constants.php шаблона: const SF_ROOT_PATH = __DIR__;
 * При установке ядра через composer — это каталог на три уровня выше vendor/growtask/simflex.
 */
define('SF_ROOT_PATH', dirname(__DIR__, 3));

/**
 * Текущая точка входа (SF_LOCATION_SITE / SF_LOCATION_ADMIN / SF_LOCATION_CLI / SF_LOCATION_API).
 * Задаётся в точках входа шаблона: index.php, Admin/index.php, console.php, cron.php, fm.php.
 */
define('SF_LOCATION', SF_LOCATION_SITE);
