<?php

/**
 * Registry of core admin section handlers: name => Handler class (the handler of an Admin\Menu item).
 * The name of a Table is its database table. This is how \Simflex\Admin\Structure\Repository discovers
 * the table classes; add new core handlers here. Project-specific ones go in the app's own
 * Admin/Structure/handlers.php.
 */

use Simflex\Admin\Structure\Handlers\Account;
use Simflex\Admin\Structure\Handlers\Content;
use Simflex\Admin\Structure\Handlers\Cron;
use Simflex\Admin\Structure\Handlers\Log;
use Simflex\Admin\Structure\Handlers\Menu;
use Simflex\Admin\Structure\Handlers\ModuleItem;
use Simflex\Admin\Structure\Handlers\Privilege;
use Simflex\Admin\Structure\Handlers\Seo;
use Simflex\Admin\Structure\Handlers\Settings;
use Simflex\Admin\Structure\Handlers\User;
use Simflex\Admin\Structure\Handlers\UserPrivilegePersonal;
use Simflex\Admin\Structure\Handlers\UserRole;
use Simflex\Admin\Structure\Handlers\UserRolePrivilege;

return [
    // sections without a table
    'account' => Account::class,
    'privilege' => Privilege::class,
    // tables
    'content' => Content::class,
    'cron' => Cron::class,
    'log' => Log::class,
    'menu' => Menu::class,
    'module_item' => ModuleItem::class,
    'seo' => Seo::class,
    'settings' => Settings::class,
    'user' => User::class,
    'user_privilege_personal' => UserPrivilegePersonal::class,
    'user_role' => UserRole::class,
    'user_role_privilege' => UserRolePrivilege::class,
];
