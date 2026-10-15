<?php

namespace Simflex\Core;

use Simflex\Core\ExtensionBase;
use Simflex\Core\Path;

abstract class ComponentBase extends ExtensionBase
{
    /**
     * Url опубликованной статики компонента (см. Assets::publish)
     */
    protected function rootHref(): string
    {
        return Path::dirToHref($this->rootDir);
    }

    abstract protected function content();

    final public function execute(bool $is_print = false): string|false|null
    {
        if ($is_print) {
            $this->content();
        } else {
            ob_start();
            $this->content();
            return ob_get_clean();
        }
    }
}
