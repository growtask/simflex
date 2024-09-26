<?php
namespace Simflex\Core\DB\Schema\Params;

class IndexParams
{
    public const TYPE_NONE = '';
    public const TYPE_FULLTEXT = 'FULLTEXT';
    public const TYPE_SPATIAL = 'SPATIAL';

    public string $type = self::TYPE_NONE;
    public bool $isKey = false;
    public array $keyParts = [];
}