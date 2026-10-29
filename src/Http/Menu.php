<?php

namespace Simflex\Core\Http;

use Simflex\Core\Models\Menu as MenuModel;
use Simflex\Core\System\Container;

/**
 * Меню сайта (таблица menu): активные пункты, загружаются один раз за запрос. Пункт - строка таблицы
 * и class - класс компонента пункта (menu.component)
 */
class Menu
{
    /** @var array<int, array<int, array>>|null id родителя => id пункта => пункт */
    protected static ?array $tree = null;
    /** @var array<int, array> */
    protected static array $byId = [];
    /** @var array<string, array> ссылка => пункт */
    protected static array $byLink = [];

    /**
     * @return array<int, array<int, array>> Дерево: id родителя (0 - корень) => id пункта => пункт
     */
    public static function getTree(): array
    {
        static::load();
        return static::$tree;
    }

    /**
     * @return array<int, array> id => пункт
     */
    public static function getById(): array
    {
        static::load();
        return static::$byId;
    }

    /**
     * @return array<string, array> ссылка => пункт
     */
    public static function getByLink(): array
    {
        static::load();
        return static::$byLink;
    }

    /**
     * Корневые пункты места (Models\Menu::PLACE_MAIN, PLACE_FOOTER) без скрытых, в порядке npp. Для подвала это
     * заголовки колонок
     *
     * @return array<int, array> id => пункт
     */
    public static function getRoots(string $place): array
    {
        return array_filter(
            static::getTree()[0] ?? [],
            fn(array $item) => empty($item['hidden']) && ($item['place'] ?? MenuModel::PLACE_MAIN) === $place
        );
    }

    /**
     * Вложенные пункты $pid без скрытых, в порядке npp
     *
     * @return array<int, array> id => пункт
     */
    public static function getChildren(int $pid): array
    {
        return array_filter(static::getTree()[$pid] ?? [], fn(array $item) => empty($item['hidden']));
    }

    /**
     * Пункт меню текущей страницы: ссылка пункта совпадает с путём запроса
     */
    public static function getCurrent(): ?array
    {
        return static::getByLink()[Container::getRequest()->getPath()] ?? null;
    }

    /**
     * Перечитать меню из базы при следующем обращении
     */
    public static function reset(): void
    {
        static::$tree = null;
        static::$byId = static::$byLink = [];
    }

    protected static function load(): void
    {
        if (static::$tree !== null) {
            return;
        }

        static::$tree = [];
        $rows = MenuModel::findAdv()->asArray()->select(['*', 'component AS class'])->where(['active' => 1])->orderBy('npp')->all();
        foreach ($rows as $row) {
            static::$tree[(int)$row['menu_pid']][(int)$row['menu_id']] = $row;
            static::$byId[(int)$row['menu_id']] = $row;
            static::$byLink[$row['link']] ??= $row;
        }
    }
}
