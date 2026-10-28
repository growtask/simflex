<?php

namespace Simflex\Core\Admin;

use Simflex\Core\Http\Request;
use Simflex\Core\Http\View;
use Simflex\Core\System\Container;

/**
 * Раздел админки, указанный в пункте меню (MenuItem::$handler). Табличный раздел наследует Table,
 * раздел без таблицы наследует Handler и возвращает своё содержимое из content()
 */
abstract class Handler
{
    /** Заголовок раздела: название его пункта меню */
    public string $title = '';

    /** Запрос: параметры читаются только через него, а не из $_GET, $_POST, $_REQUEST */
    protected Request $request;

    public function __construct()
    {
        $this->request = Container::getRequest() ?? new Request();
        $this->title = (string)(Page::get()->getCurrentMenuItem('name') ?? '');
    }

    /**
     * Содержимое раздела. Возвращает HTML (или JSON для ajax-запросов)
     */
    abstract public function content(): string;

    /**
     * Рендерит шаблон в строку, $this в шаблоне — раздел
     *
     * @param string $file Абсолютный путь к шаблону
     * @param array<string, mixed> $vars Переменные, доступные в шаблоне как $name
     */
    protected function render(string $file, array $vars = []): string
    {
        return View::render($file, $vars, $this);
    }
}
