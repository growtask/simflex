<?php

namespace Simflex\Layout\Components\UI\Core\DropdownTab;

use Simflex\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawDropdownTab(
        string               $className = '',
        string               $text = '',
        string               $link = '',
        string               $icon = '',
        string               $badge = '',
        DropdownTabMenuTheme $menuTheme = DropdownTabMenuTheme::Light,
        DropdownTabStyle     $style = DropdownTabStyle::Primary,
        DropdownTabSize      $size = DropdownTabSize::Medium,
        DropdownTabTheme     $theme = DropdownTabTheme::Light,
        array                $options = [],
        array                $attributes = []
    ): string {
        return static::draw(
            compact(
                'className',
                'text',
                'link',
                'icon',
                'badge',
                'options',
                'attributes'
            ) + [
                'menuTheme' => $menuTheme->value,
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}
