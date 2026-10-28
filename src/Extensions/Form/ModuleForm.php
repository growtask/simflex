<?php

namespace Simflex\Core\Extensions\Form;

use Simflex\Core\Extensions\Form\Fields\Field;
use Simflex\Core\Extensions\Form\Fields\FieldCheckbox;
use Simflex\Core\Extensions\Form\Fields\FieldEmail;
use Simflex\Core\Extensions\Form\Fields\FieldPassword;
use Simflex\Core\Extensions\Form\Fields\FieldSpamCode;
use Simflex\Core\Extensions\Form\Fields\FieldString;
use Simflex\Core\Extensions\Form\Fields\FieldText;
use Simflex\Core\Models\Settings;
use Simflex\Core\Extensions\Alert\Alert;
use Simflex\Core\System\Container;
use Simflex\Core\Helpers\Str;
use Simflex\Core\Logger\Log;
use Simflex\Core\Mail\Mailer;
use Simflex\Core\System\ModuleBase;

class ModuleForm extends ModuleBase
{
    protected string $name;
    protected string $submitButtonText;
    /** @var Field[] */
    protected array $fields = [];
    protected static string $formTplDefault = 'form.tpl';
    protected string $formTpl;
    protected array $errors = [];
    protected string $action = '';
    protected string $method = 'post';

    protected const TYPE_MAP = [
        'string' => FieldString::class,
        'checkbox' => FieldCheckbox::class,
        'email' => FieldEmail::class,
        'password' => FieldPassword::class,
        'spamCode' => FieldSpamCode::class,
        'text' => FieldText::class,
    ];

    public function __construct($module)
    {
        parent::__construct($module);
        $this->name = Str::translite($this->title);
        $this->submitButtonText = $this->params['submit_button_text'] ?? 'Отправить';
        $this->fillFields(explode("\n", $this->params['fields'] ?? ''));
        $this->formTpl = $this->params['form_tpl'] ?? self::$formTplDefault;
        $this->maybeSubmit();
    }

    protected function maybeSubmit(): void
    {
        if ($this->submitted()) {
            if ($this->validate()) {
                $to = Settings::get('form_email');
                if (empty($to)) {
                    Alert::warning('Сообщение не может быть отправлено по техническим причинам, попробуйте позднее [0xfe]');
                    return;
                }
                $subject = 'Заполненная форма ' . $this->title;

                $rows = [];
                $rows[] = 'Отправлено со страницы ' . Container::getRequest()->getFullUrl();
                $rows[] = '';
                foreach ($this->fields as $field) {
                    $rows[] = $field->getLabel() . ': ' . $field->getPOST();
                }
                $rows[] = '';
                $rows[] = '---';
                $rows[] = 'Simflex CMS';
                try {
                    $sent = Mailer::get()->sendPlain($subject, $to, implode("\n", $rows));
                } catch (\Throwable $e) {
                    Log::error('Form mail: {error}', ['error' => $e->getMessage()]);
                    $sent = false;
                }
                if ($sent) {
                    Alert::success('Ваше сообщение отправлено, Мы ответим Вам в ближайшее время');
                } else {
                    Alert::warning('Сообщение не может быть отправлено по техническим причинам, попробуйте позднее');
                }
            } else {
                Alert::warning(
                    'При заполнении формы возникли ошибки<span style="display:none">' . $this->errorsStr() . '</span>'
                );
            }
        }
    }

    protected function errorsStr(): string
    {
        $ar = [];
        foreach ($this->errors as $field => $errors) {
            $ar[] = "$field: " . implode(', ', $errors);
        }
        return implode('; ', $ar);
    }

    protected function submitted(): bool
    {
        return isset(Container::getRequest()->post($this->name)['submit']);
    }

    public function validate(): bool
    {
        $errors = [];
        foreach ($this->fields as $field) {
            $errors = array_merge($errors, $field->check());
        }
        $this->errors = $errors;
        return !count($this->errors);
    }

    protected function fillFields(array $fields): void
    {
        $this->fields = [];
        foreach ($fields as $fieldRaw) {
            $ar = explode(':', $fieldRaw);
            $title = $ar[0];
            $name = Str::translite($title);
            $type = $ar[1] ?? 'string';
            $required = $ar[2] ?? false;
            $fieldClass = self::TYPE_MAP[$type] ?? FieldString::class;
            /** @var Field $field */
            $field = new $fieldClass($this, $name, $title, '', 0, '', $required);
            $this->fields[$name] = $field;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function content(): string
    {
        return $this->render(static::findTemplateFile($this->formTpl));
    }

    protected static function findTemplateFile(string $name): string
    {
        if (is_file($path = Container::getPaths()->getRoot('Extensions/Form/tpl/' . $name))) {
            return $path;
        }
        if (is_file($path = __DIR__ . '/tpl/' . $name)) {
            return $path;
        }
        return __DIR__ . '/tpl/' . self::$formTplDefault;
    }
}
