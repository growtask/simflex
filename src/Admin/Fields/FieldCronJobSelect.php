<?php

namespace Simflex\Admin\Fields;

use Simflex\Core\Cron\CronRegistry;

/**
 * Cron job key from provider/cron.php, shown by the job name
 */
class FieldCronJobSelect extends FieldExtensionSelect
{
    public static function typeLabel(): string
    {
        return 'Выбор задачи cron';
    }

    protected function options(): array
    {
        return array_map(fn($job) => $job->name, CronRegistry::jobs());
    }

    public function input($value): string
    {
        if (!$this->readonly) {
            return parent::input($value);
        }

        $value = (string)($this->value ?: $value);
        return $this->render('input', ['value' => $this->options()[$value] ?? $value] + $this->inputVars());
    }
}
