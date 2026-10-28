<?php

/**
 * Реестр обработчиков разделов админки ядра (классы Handler, обработчики пунктов Admin\Menu).
 * По нему \Simflex\Core\Admin\Table\Repository находит табличные разделы по именам их таблиц (их моделей):
 * внешние ключи ссылаются на таблицы по имени. Обработчики проекта указываются в собственном
 * Admin/handlers.php приложения.
 */

use Simflex\Core\Admin\Handlers;

return [
    // разделы без таблицы
    Handlers\Account::class,
    Handlers\Group::class,
    Handlers\Privilege::class,
    // таблицы
    Handlers\Content::class,
    Handlers\Cron::class,
    Handlers\Log::class,
    Handlers\Menu::class,
    Handlers\ModuleItem::class,
    Handlers\Seo::class,
    Handlers\Settings::class,
    Handlers\User::class,
    Handlers\UserPrivilegePersonal::class,
    Handlers\UserRole::class,
    Handlers\UserRolePrivilege::class,
];
