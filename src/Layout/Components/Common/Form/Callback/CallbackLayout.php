<?php

namespace Simflex\Core\Layout\Components\Common\Form\Callback;

/**
 * Раскладка формы (Figma: Form/callback, layout)
 */
enum CallbackLayout: string
{
    /** Для страницы: имя и телефон в одной строке */
    case Grid = 'grid';
    /** Для модального окна и mobile: одна колонка */
    case Stack = 'stack';
}
