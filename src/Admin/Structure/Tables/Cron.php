<?php

namespace Simflex\Admin\Structure\Tables;

use Simflex\Admin\Base\Cron as CronDriver;
use Simflex\Admin\Fields\FieldCronJobSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Presets\ActiveField;
use Simflex\Admin\Structure\Presets\IdField;
use Simflex\Admin\Structure\Table;
use Simflex\Core\Privilege;

/**
 * Jobs are declared in provider/cron.php, the table keeps only their activity and schedule
 */
class Cron extends Table
{
    public function __construct()
    {
        parent::__construct(
            orderBy: 'job',
            privilegeEdit: Privilege::DEV,
            class: CronDriver::class,
            fields: [
                new ActiveField(width: '85', filter: false),
                new IdField(),
                new FieldDefinition(
                    name: 'job',
                    class: FieldCronJobSelect::class,
                    label: 'Задача',
                    help: 'Задачи объявляются в provider/cron.php',
                    params: [
                        'main' => new FieldParams(
                            width: '1',
                            readonly: true,
                        ),
                    ],
                ),
                new FieldDefinition(
                    name: 'timing',
                    class: FieldString::class,
                    label: 'Время',
                    help: 'Как в *nix crontab. Например */10 * * * *',
                    params: [
                        'main' => new FieldParams(
                            width: '160',
                            required: true,
                        ),
                    ],
                ),
            ],
            params: [
            ],
        );
    }
}
