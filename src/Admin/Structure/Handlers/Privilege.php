<?php

namespace Simflex\Admin\Structure\Handlers;

use Simflex\Admin\Base\Privileges;
use Simflex\Admin\Structure\Handler;

/**
 * Privileges are declared in code (\Simflex\Core\Privilege): the section lists them and switches their activity,
 * kept in the optional user_privilege table. Not a table section
 */
class Privilege extends Handler
{
    public function __construct()
    {
        parent::__construct(class: Privileges::class);
    }
}
