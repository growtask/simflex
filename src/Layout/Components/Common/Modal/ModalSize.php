<?php

namespace Simflex\Core\Layout\Components\Common\Modal;

/**
 * Размер окна (Figma: Modal/main, size): ширина карточки на desktop
 */
enum ModalSize: string
{
    case ExtraSmall = 'xs';
    case Small = 's';
    case Medium = 'm';
    case Large = 'l';
}
