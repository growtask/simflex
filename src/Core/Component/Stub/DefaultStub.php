<?php

namespace Simflex\Core\Component\Stub;

use Simflex\Core\ComponentBase;

class DefaultStub extends ComponentBase
{

    protected function content(): void
    {
        echo 'This is DefaultStub component';
    }
}