<?php
namespace Simflex\Core\DB\Schema\Params;

class ConstraintParams
{
    const REFERENCE_OPTION_RESTRICT = 'RESTRICT';
    const REFERENCE_OPTION_CASCADE = 'CASCADE';
    const REFERENCE_OPTION_SET_NULL = 'SET NULL';
    const REFERENCE_OPTION_NO_ACTION = 'NO ACTION';
    const REFERENCE_OPTION_SET_DEFAULT = 'SET DEFAULT';

    const REFERENCE_MATCH_FULL = 'FULL';
    const REFERENCE_MATCH_PARTIAL = 'PARTIAL';
    const REFERENCE_MATCH_SIMPLE = 'SIMPLE';

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
