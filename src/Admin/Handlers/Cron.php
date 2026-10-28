<?php

namespace Simflex\Core\Admin\Handlers;

use Simflex\Core\Admin\Fields\FieldCronJobSelect;
use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Extensions\Alert\Alert;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Admin\Table\Presets\ActiveField;
use Simflex\Core\Admin\Table\Presets\IdField;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\Cron\CronRegistry;
use Simflex\Core\System\Privilege;
use Simflex\Core\Models\Cron as CronModel;

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
        if (empty($this->request->post('group_ids')) && empty($this->request->post('id'))) {
            Alert::error('Задачи cron объявляются в коде: provider/cron.php', \Simflex\Core\Admin\Page::get()->getPath());
        }

        return parent::save();
    }
}
