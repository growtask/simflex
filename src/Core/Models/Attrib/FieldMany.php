<?php
namespace Simflex\Core\Models\Attrib;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class FieldMany
{
    public function __construct(
        public string $name,
        public string $model
    )
    {
    }
}