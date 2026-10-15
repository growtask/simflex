<?php

namespace Simflex\Admin\Base;

use Simflex\Admin\Base;
use Simflex\Admin\Plugins\Alert\Alert;
use Simflex\Core\Cron\CronRegistry;

/**
 * Cron jobs are declared in provider/cron.php: the admin panel only edits their activity and schedule
 */
class Cron extends Base
{
    public function __construct()
    {
        parent::__construct();
        CronRegistry::sync();
        $this->canAdd = $this->canCopy = $this->canDelete = false;
    }

    public function save(): string|int|false
    {
        if (empty($_POST['group_ids']) && empty($_POST['id'])) {
            Alert::error('Задачи cron объявляются в коде: provider/cron.php', './');
        }

        return parent::save();
    }
}
