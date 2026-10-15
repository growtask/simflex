<?php

namespace Simflex\Extensions\Form\Fields;

use Simflex\Extensions\Form\Fields\Field;

class FieldEmail extends Field
{

    public array $v_mask = [
        'pattern' => '/^[a-z0-9_\-\.]{1,20}@[a-z0-9\-\.]{1,20}\.[a-z]{2,8}$/i'
    ];

}
