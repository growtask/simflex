<?php

/**
 * Registry of core admin tables: model name (as used in admin_menu.model) => Table class.
 * This is how \Simflex\Admin\Structure\Repository discovers table classes; add new core
 * tables here. Project-specific tables go in the app's own Admin/Structure/tables.php.
 */

use Simflex\Admin\Structure\Tables\AdminMenu;
use Simflex\Admin\Structure\Tables\Content;
use Simflex\Admin\Structure\Tables\ContentTemplate;
use Simflex\Admin\Structure\Tables\ContentTemplateParam;
use Simflex\Admin\Structure\Tables\Cron;
use Simflex\Admin\Structure\Tables\Log;
use Simflex\Admin\Structure\Tables\Menu;
use Simflex\Admin\Structure\Tables\ModuleItem;
use Simflex\Admin\Structure\Tables\Seo;
use Simflex\Admin\Structure\Tables\Settings;
use Simflex\Admin\Structure\Tables\User;
use Simflex\Admin\Structure\Tables\UserPriv;
use Simflex\Admin\Structure\Tables\UserPrivPersonal;
use Simflex\Admin\Structure\Tables\UserRole;
use Simflex\Admin\Structure\Tables\UserRolePriv;

return [
    'admin_menu' => AdminMenu::class,
    'content' => Content::class,
    'content_template' => ContentTemplate::class,
    'content_template_param' => ContentTemplateParam::class,
    'cron' => Cron::class,
    'log' => Log::class,
    'menu' => Menu::class,
    'module_item' => ModuleItem::class,
    'seo' => Seo::class,
    'settings' => Settings::class,
    'user' => User::class,
    'user_priv' => UserPriv::class,
    'user_priv_personal' => UserPrivPersonal::class,
    'user_role' => UserRole::class,
    'user_role_priv' => UserRolePriv::class,
];
