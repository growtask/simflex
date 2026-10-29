<?php

namespace Simflex\Core\Layout\Components\UI\Core\TextInput;

/**
 * Заготовка поля (примеры phone и email набора Figma Inputs)
 */
enum TextInputKind: string
{
    case Text = 'text';
    case Phone = 'phone';
    case Email = 'email';
}
