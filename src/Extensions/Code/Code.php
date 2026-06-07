<?php

namespace Simflex\Extensions\Code;

use Simflex\Core\ModuleBase;

/**
 * Выводит любой хранимый контент, используется для счётчиков,
 * и любого html-кода сторонних внешних сервисов
 */
class Code extends ModuleBase
{
    protected function content(): void
    {
        echo $this->params['content'] ?? '';
    }
}
