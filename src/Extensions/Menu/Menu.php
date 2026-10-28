<?php

namespace Simflex\Extensions\Menu;

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Core\Container;
use Simflex\Core\ModuleBase;
use Simflex\Core\SiteMenu;

/**
 * Модуль «Меню»: выводит меню сайта (таблица menu) списком ul/li.
 *
 * Параметры: start_id - пункт, от которого строится меню (0 - корень), max_level - сколько уровней вложенности
 * выводить (0 - только первый), is_child - вывести подпункты того пункта уровня start_id, в который входит
 * текущая страница. Ссылке текущего раздела добавляется класс active, пункту с выводимыми подпунктами - has-children.
 * Вёрстку меняют наследником с другими шаблонами (tpl/index.tpl, tpl/treenode.tpl)
 */
class Menu extends ModuleBase
{
    /** @var array<int, array<int, array>> Пункты меню: id родителя => id пункта => пункт */
    protected array $menu = [];

    public static function extensionTitle(): string
    {
        return 'Меню';
    }

    public static function adminParams(): array
    {
        return [
            new FieldDefinition(name: 'start_id', class: FieldInt::class, label: 'Начальный пункт (id)', defaultValue: '0'),
            new FieldDefinition(name: 'max_level', class: FieldInt::class, label: 'Уровней вложенности', defaultValue: '0'),
            new FieldDefinition(name: 'is_child', class: FieldBool::class, label: 'Подпункты текущего раздела'),
        ];
    }

    protected function content(): string
    {
        $this->menu = SiteMenu::getTree();

        $startId = (int)($this->params['start_id'] ?? 0);
        $this->params['max_level'] = (int)($this->params['max_level'] ?? 0);

        $pid = $startId;
        if (!empty($this->params['is_child'])) {
            $pid = -1;
            foreach ($this->menu[$startId] ?? [] as $item) {
                if ($this->isCurrent($item['link'])) {
                    $pid = (int)$item['menu_id'];
                }
            }
        }

        return $this->render($this->getTemplate('index.tpl'), ['pid' => $pid]);
    }

    /**
     * Уровень меню: подпункты $pid
     */
    protected function tree(int $pid = 0, int $lvl = 0): string
    {
        if (!isset($this->menu[$pid])) {
            return '';
        }

        $path = Container::getRequest()->getPath();
        foreach ($this->menu[$pid] as $id => $item) {
            $classes = [];
            if (empty($item['hidden'])) {
                if ($item['link'] === '/' ? $path === '/' : $this->isCurrent($item['link'])) {
                    $classes[] = 'active';
                }
                if ($lvl < $this->params['max_level'] && isset($this->menu[$id])) {
                    $classes[] = 'has-children';
                }
            }
            $this->menu[$pid][$id]['class-a'] = $classes;
        }

        return $this->render($this->getTemplate('treenode.tpl'), ['pid' => $pid, 'lvl' => $lvl]);
    }

    /**
     * Текущая страница - раздел $link или страница внутри него
     */
    protected function isCurrent(string $link): bool
    {
        return str_starts_with(Container::getRequest()->getPath(), $link);
    }

    /**
     * Шаблон модуля: из tpl/ наследника, если он там есть, иначе из tpl/ ядра
     */
    protected function getTemplate(string $name): string
    {
        $file = $this->getRootDir() . '/tpl/' . $name;
        return is_file($file) ? $file : __DIR__ . '/tpl/' . $name;
    }
}
