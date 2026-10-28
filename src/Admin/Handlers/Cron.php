<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldCronJobSelect;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Plugins\Alert\Alert;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\ActiveField;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Table;
use Simflex\Cron\CronRegistry;
use Simflex\Core\Privilege;
use Simflex\Models\Cron as CronModel;

/**
 * Задачи объявлены в provider/cron.php, в таблице хранятся только их активность и расписание
 */
class Cron extends Table
{
    protected static string $model = CronModel::class;
    protected static ?string $defaultOrderBy = 'job';
    protected static ?string $privilegeEdit = Privilege::DEV;

    public static function getFields(): array
    {
        return [
            new ActiveField(width: '85', filter: false),
            new IdField(),
            new FieldDefinition(
                name: 'job',
                class: FieldCronJobSelect::class,
                label: 'Задача',
                help: 'Задачи объявляются в provider/cron.php',
                width: '1',
                readonly: true,
            ),
            new FieldDefinition(
                name: 'timing',
                class: FieldString::class,
                label: 'Время',
                help: 'Как в *nix crontab. Например */10 * * * *',
                width: '160',
                required: true,
            ),
        ];
    }

    public function __construct()
    {
        parent::__construct();
        CronRegistry::sync();
        $this->canAdd = $this->canCopy = $this->canDelete = false;
    }

    public function save(): string|int|false
    {
        if (empty($_POST['group_ids']) && empty($_POST['id'])) {
            Alert::error('Задачи cron объявляются в коде: provider/cron.php', \Simflex\Admin\Core::path());
        }

        return parent::save();
    }
}
