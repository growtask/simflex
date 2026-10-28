<?php

namespace Simflex\DB\Schema\Params;

class TableParams
{
    public bool $ifNotExists = false;
    public bool $temporary = false;
    public int $autoIncrement = 0;
    public string $comment = '';
    public string $collate = '';
    public string $characterSet = '';
    public bool $defaultCharacterSet = false;
}
