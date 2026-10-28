<?php

namespace Simflex\Core\Admin\Fields;

use Simflex\Core\Cron\CronRegistry;

/**
 * Ключ задачи cron из provider/cron.php, показывается по названию задачи
 */
class FieldCronJobSelect extends FieldExtensionSelect
{
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
