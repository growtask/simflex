<?php

namespace Simflex\Core\Layout\Components\Common\Form\Callback;

/**
 * Состояние формы (Figma: Form/callback, state)
 */
enum CallbackState: string
{
    case Default = 'default';
    /** Поля с ошибками из $errors помечены */
    case Error = 'error';
    /** Кнопка отправки в состоянии load, поля недоступны */
    case Sending = 'sending';
}
