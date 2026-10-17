<?php

namespace Simflex\Extensions\Content;

use Simflex\Admin\Structure\Data\FieldDefinition;

/**
 * Block of the page template's fields in the page form. The block only groups them on the screen:
 * the values are kept in content.params by the field name, not by the block
 */
readonly class ContentTemplateBlock
{
    /**
     * @param string $label Block title
     * @param FieldDefinition[] $fields
     * @param string $pos Form column: left|right
     */
    public function __construct(
        public string $label,
        public array $fields,
        public string $pos = 'left',
    ) {
    }
}
