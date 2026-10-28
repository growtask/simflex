<?php

namespace Simflex\Models\Attrib;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class FieldOne
{
    public function __construct(
        public string $name,
        public string $model
    ) {
    }
}
