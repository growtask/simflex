<?php

namespace Simflex\Core\Layout\Components\Common\Form\Result;

/**
 * Результат отправки формы (Figma: Form/result, style)
 */
enum ResultStyle: string
{
    case Success = 'success';
    case Error = 'error';
}
