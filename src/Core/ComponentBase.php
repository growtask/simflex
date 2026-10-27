<?php

namespace Simflex\Core;

abstract class ComponentBase extends ExtensionBase
{
    /**
     * Url опубликованной статики компонента (см. Assets::publish)
     */
    protected function rootHref(): string
    {
        return Path::dirToHref($this->getRootDir());
    }

    /**
     * Контент компонента. Возвращает HTML, а не выводит его
     */
    abstract protected function content(): string;

    final public function execute(): string
    {
        return $this->content();
    }
}
