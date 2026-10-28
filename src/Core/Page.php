<?php

namespace Simflex\Core;

use Simflex\DB\DB;

/**
 * Страница сайта: содержимое компонента и модули по позициям. Выполняется при загрузке (init()), тема выводит
 * результат: content(), position(), meta() (заголовок, мета-теги, стили), metaJS() (скрипты).
 *
 * Заголовок и мета-теги - PageSeo, стили и скрипты - PageAssets
 */
class Page
{
    protected static string $content = '';
    /** @var array<string, list<string>> позиция => HTML модулей */
    protected static array $positions = [];

    /**
     * Выполняет модули и компонент страницы. Модули с modulePostexec() выполняются после компонента,
     * остальные - до; {position_<имя>} в HTML компонента заменяется модулями позиции
     */
    public static function init(): void
    {
        $component = Core::getComponent();

        $rows = static::moduleRows(DB::assoc(
            'SELECT item_id, module, menu_id, posname, name, params FROM module_item WHERE active = 1 ORDER BY npp, item_id'
        ));
        $menuId = (int)(SiteMenu::getCurrent()['menu_id'] ?? 0);
        $rows = array_filter($rows, fn(array $row) => empty($row['menu_id']) || (int)$row['menu_id'] === $menuId);

        static::executeModules(array_filter($rows, fn(array $row) => !$row['postexec']));

        static::$content = $component->execute();
        if (str_contains(static::$content, '{position_')) {
            foreach (static::$positions as $name => $html) {
                static::$content = str_replace('{position_' . $name . '}', implode("\n", $html), static::$content);
            }
        }

        static::executeModules(array_filter($rows, fn(array $row) => $row['postexec']));
    }

    public static function content(): string
    {
        return static::$content;
    }

    /**
     * HTML модулей позиции
     * @param string $delimiter Разделитель модулей, например '<hr/>'
     */
    public static function position(string $name, string $delimiter = ''): string
    {
        return implode($delimiter, static::$positions[$name] ?? []);
    }

    /**
     * HTML активного модуля по id экземпляра (module_item.item_id), для ajax-запросов
     */
    public static function moduleById(int $id): string
    {
        $rows = static::moduleRows(DB::assoc(
            'SELECT item_id, module, menu_id, posname, name, params FROM module_item WHERE active = 1 AND item_id = ?',
            false,
            false,
            [$id]
        ));

        return $rows ? (new $rows[0]['class']($rows[0]))->execute() : '';
    }

    /**
     * Добавляет class и postexec из модулей, объявленных в коде, пропускает записи неизвестных модулей
     */
    public static function moduleRows(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $module = ExtensionRegistry::module((string)$row['module']);
            if (!$module) {
                continue;
            }
            $row['class'] = ltrim($row['module'], '\\');
            $row['postexec'] = $module['postexec'];
            $out[] = $row;
        }

        return $out;
    }

    /**
     * Заголовок, мета-теги и стили страницы для head темы
     */
    public static function meta(): string
    {
        $html = PageSeo::getHeadHtml() . PageAssets::getCssHtml();
        if (Container::getConfig()->devMode) {
            $html .= Container::get('debugbar')->renderHead();
        }

        return $html;
    }

    /**
     * Скрипты страницы, выводятся в конце body темы
     */
    public static function metaJS(): string
    {
        $html = PageAssets::getJsHtml();
        if (Container::getConfig()->devMode) {
            $html .= Container::get('debugbar')->render();
        }

        return $html;
    }

    protected static function executeModules(array $rows): void
    {
        foreach ($rows as $row) {
            static::$positions[$row['posname']][] = (new $row['class']($row))->execute();
        }
    }
}
