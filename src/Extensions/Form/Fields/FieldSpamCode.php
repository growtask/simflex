<?php

namespace Simflex\Extensions\Form\Fields;

class FieldSpamCode extends Field
{
    public array|int $vRequired = 1;

    public function html(): void
    {
        echo '<span class="plug-form-spamcode">';
        echo '</span>';
    }

    public function &check(): array
    {
        $formName = $this->form->getName();
        $err = [];
        $_POST[$formName][$this->name] = $_POST[$formName][$this->name] ?? '';
        if ($_POST[$formName][$this->name] === '' || $_POST[$formName][$this->name] !== ($_SESSION['spamcode'] ?? null)) {
            $err[$this->name][] = 'спам-код введен неверно';
        }
        return $err;
    }
}
