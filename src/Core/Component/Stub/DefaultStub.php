<?php

namespace Simflex\Core\Component\Stub;

use Simflex\Core\ComponentBase;

class DefaultStub extends ComponentBase
{
    protected function content(): string
    {
        return 'This is DefaultStub component';
    }
}
