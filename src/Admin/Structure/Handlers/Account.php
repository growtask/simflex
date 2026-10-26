<?php

namespace Simflex\Admin\Structure\Handlers;

use Simflex\Admin\Modules\Account\Account as AccountModule;
use Simflex\Admin\Structure\Handler;

/**
 * Account of the current user. Not a table section
 */
class Account extends Handler
{
    public function __construct()
    {
        parent::__construct(class: AccountModule::class);
    }
}
