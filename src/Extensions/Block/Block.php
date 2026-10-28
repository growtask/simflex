<?php

namespace Simflex\Core\Extensions\Block;

use Simflex\Core\Extensions\Code\Code;

/**
 * Выводит любой хранимый HTML-контент, формируемый в HTML редакторе
 */
class Block extends Code
{
    public static function extensionTitle(): string
    {
        return 'Текстовый блок';
    }

    protected static bool $editorFull = true;
}
