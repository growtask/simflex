<?php

namespace Simflex\Core;

abstract class ComponentBase extends ExtensionBase
{
    /**
     * Url опубликованной статики компонента (см. Assets::publish)
     */
    protected function rootHref(): string
    {
        return Path::dirToHref($this->rootDir);
    }

    /**
     * Component content. Returns HTML, does not output it
     */
    abstract protected function content(): string;

    final public function execute(): string
    {
        return $this->content();
    }
}
