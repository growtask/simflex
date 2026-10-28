<?php

namespace Simflex\Core\DB\Schema\Params;

class ConstraintParams
{
    public const REFERENCE_OPTION_RESTRICT = 'RESTRICT';
    public const REFERENCE_OPTION_CASCADE = 'CASCADE';
    public const REFERENCE_OPTION_SET_NULL = 'SET NULL';
    public const REFERENCE_OPTION_NO_ACTION = 'NO ACTION';
    public const REFERENCE_OPTION_SET_DEFAULT = 'SET DEFAULT';

    public const REFERENCE_MATCH_FULL = 'FULL';
    public const REFERENCE_MATCH_PARTIAL = 'PARTIAL';
    public const REFERENCE_MATCH_SIMPLE = 'SIMPLE';

    public bool $isPrimary = false;
    public bool $isUnique = false;
    public bool $isUniqueIndex = false;
    public bool $isForeign = false;
    public array $keyParts = [];
    public array $referenceColumns = [];
    public string $reference = '';
    public string $onDelete = '';
    public string $onUpdate = '';
    public string $match = '';
}
