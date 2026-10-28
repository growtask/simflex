<?php

namespace Simflex\Core\System;

abstract class ModuleBase extends ExtensionBase
{
    protected int $id = 0;
    protected string $name = '';
    protected string $title = '';
    protected array $params = [];
    protected string $position = '';

    /**
     * Запускать модуль после контента страницы
     */
    public static function modulePostexec(): bool
    {
        return false;
    }

    public function __construct($module)
    {
        $this->id = (int)$module['item_id'];
        $this->name = strtolower(substr($module['class'], 3));
        $this->title = $module['name'];
        $this->position = $module['posname'];
        $this->params = unserialize($module['params']) ?: [];
        $this->params['css_id'] = isset($this->params['css_id']) ? $this->params['css_id'] : 'module-' . $this->id;
        $this->params['is_title'] = isset($this->params['is_title']) ? $this->params['is_title'] : 0;
        $this->params['is_wrap'] = isset($this->params['is_wrap']) ? $this->params['is_wrap'] : 0;
    }

    /**
     * Контент модуля. Возвращает HTML, а не выводит его
     */
    abstract protected function content(): string;

    final public function execute(): string
    {
        $content = $this->content();
        if (Container::getRequest()->isAjax() || $content === '') {
            return $content;
        }

        $title = $this->params['is_title'] ? '<h2>' . $this->title . '</h2>' : '';
        if (!$this->params['is_wrap']) {
            return $title . $content;
        }

        $class = 'module-base module-' . $this->name . (isset($this->params['cssclass']) ? " {$this->params['cssclass']}" : '');
        return '<div id="' . $this->params['css_id'] . '" class="' . $class . '">'
            . $title
            . '<div class="module-body">' . $content . '</div>'
            . '</div>';
    }
}
