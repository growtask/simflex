<?php

namespace Simflex\Core\Layout\Components\UI\Core\TextInput;

use Simflex\Core\Layout\LayoutBase;

/**
 * Поле ввода (набор Figma Inputs/main и заготовки phone, email).
 *
 * - kind: phone — type="tel", маска +7 (999) 123-45-67 и счётчик цифр 0/11 (скрипт компонента, в форму уходят цифры),
 *   email — type="email", проверка при потере фокуса;
 * - error: текст ошибки под полем (aria-invalid, aria-describedby), цвет текста и сообщения — error;
 * - value: введённое значение (состояние filled);
 * - counter и maxLength: счётчик «n/max» справа в поле, лимит — атрибутом maxlength.
 * Подпись снаружи — labelPos Standard, без подписи — Hidden (подпись остаётся для скринридера).
 */
class Layout extends LayoutBase
{
    /** Число цифр телефона с кодом страны (+7 и 10 цифр) */
    public const PHONE_DIGITS = 11;

    /**
     * @param string $id Идентификатор поля; пусто — вычисляется из названия и подписи
     * @param array<string,string> $attributes Атрибуты input (name, required, disabled, autocomplete и т. д.)
     * @param bool|null $counter Показать счётчик; null — для phone включён, для остальных выключен
     * @param int $maxLength Максимальная длина (для phone — число цифр, по умолчанию 11)
     * @param string $errorInvalid Сообщение о неверном формате (phone, email) при потере фокуса
     */
    public static function drawInput(
        string            $className = '',
        string            $placeholder = '',
        string            $labelText = '',
        TextInputLabelPos $labelPos = TextInputLabelPos::Hidden,
        TextInputStyle    $style = TextInputStyle::Filled,
        TextInputSize     $size = TextInputSize::Medium,
        TextInputTheme    $theme = TextInputTheme::Light,
        string            $icon = '',
        bool              $reset = true,
        string            $id = '',
        array             $attributes = [],
        TextInputKind     $kind = TextInputKind::Text,
        string            $value = '',
        string            $error = '',
        ?bool             $counter = null,
        int               $maxLength = 0,
        string            $errorInvalid = ''
    ): string {
        $attributes += match ($kind) {
            TextInputKind::Phone => [
                'type' => 'tel',
                'inputmode' => 'tel',
                'autocomplete' => 'tel',
            ],
            TextInputKind::Email => [
                'type' => 'email',
                'inputmode' => 'email',
                'autocomplete' => 'email',
                'autocapitalize' => 'off',
                'spellcheck' => 'false',
            ],
            default => [],
        };
        if ($kind === TextInputKind::Phone) {
            $attributes['data-mask'] = '+7 (###) ###-##-##';
            $maxLength = $maxLength ?: self::PHONE_DIGITS;
            $placeholder = $placeholder ?: '+7 (___) ___-__-__';
            $errorInvalid = $errorInvalid ?: 'Введите номер полностью';
        } elseif ($kind === TextInputKind::Email) {
            $errorInvalid = $errorInvalid ?: 'Введите корректный email';
        }
        $icon = $icon ?: match ($kind) {
            TextInputKind::Phone => 'phone',
            TextInputKind::Email => 'mail',
            default => '',
        };
        $counter ??= $kind === TextInputKind::Phone;
        if ($counter && $maxLength <= 0) {
            $counter = false;
        }
        // У телефона maxlength не задаём: маска длиннее числа цифр, лимит цифр держит скрипт
        if ($maxLength > 0 && $kind !== TextInputKind::Phone) {
            $attributes['maxlength'] = (string)$maxLength;
        }
        if ($id === '') {
            $id = 'text-input-' . substr(md5($kind->value . ($attributes['name'] ?? '') . $labelText . $placeholder), 0, 8);
        }
        $length = $kind === TextInputKind::Phone ? strlen(preg_replace('/\D/', '', $value)) : mb_strlen($value);

        return static::draw(
            compact(
                'className',
                'placeholder',
                'labelText',
                'icon',
                'reset',
                'id',
                'value',
                'error',
                'counter',
                'maxLength',
                'length',
                'errorInvalid',
                'attributes'
            ) + [
                'style' => $style->value,
                'labelPos' => $labelPos->value,
                'size' => $size->value,
                'theme' => $theme->value,
                'kind' => $kind->value,
            ]
        );
    }
}
