<?php

namespace Simflex\Layout\Components\UI\Core\BadgeStock;

enum BadgeStockStyle: string
{
    case Many = 'many';
    case Normal = 'normal';
    case Few = 'few';
    case NoStock = 'no-stock';
}
