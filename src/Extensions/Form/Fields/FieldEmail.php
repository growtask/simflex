<?php

namespace Simflex\Extensions\Form\Fields;

class FieldEmail extends Field
{
    public array $vMask = [
        'pattern' => '/^[a-z0-9_\-\.]{1,20}@[a-z0-9\-\.]{1,20}\.[a-z]{2,8}$/i',
    ];

}
