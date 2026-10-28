<?php

namespace Simflex\Layout\Components\UI\Core\BadgeStock;

use Simflex\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawBadge(
        string          $className = '',
        string          $text = '',
        BadgeStockStyle $style = BadgeStockStyle::Many,
        BadgeStockSize  $size = BadgeStockSize::ExtraSmall,
        array           $attributes = []
    ): string {
        return static::draw(
            compact(
                'className',
                'text',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
            ]
        );
    }
}
