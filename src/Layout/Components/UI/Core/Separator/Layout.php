<?php

namespace Simflex\Layout\Components\UI\Core\Separator;

use Simflex\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSeparator(
        string               $className = '',
        SeparatorOrientation $orientation = SeparatorOrientation::Horizontal,
        SeparatorTheme       $theme = SeparatorTheme::Light,
        array                $attributes = []
    ): string {
        return static::draw(
            compact(
                'className',
                'attributes'
            ) + [
                'orientation' => $orientation->value,
                'theme' => $theme->value,
            ]
        );
    }
}
