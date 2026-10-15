<?php

namespace Simflex\Extensions\Block;

use Simflex\Extensions\Code\Code;

/**
 * Выводит любой хранимый HTML-контент, формируемый в HTML редакторе
 */
class Block extends Code
{
    protected static bool $editorFull = true;
}
