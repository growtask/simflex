<?php

namespace Simflex\Admin\Plugins\Pagecontrol;

use Simflex\Core\View;

class Pagecontrol
{
    private int $p = 0;
    private int $pOn = 0;
    private int $pCount = 0;
    private int $count = 0;
    private string $link = '?p={p}';
    private bool $pOnShow = false;
    public int $pCountMax = 7;

    public function __construct($p, $pOn, $count, $link = '?p={p}', $pOnShow = false)
    {
        $this->p = abs($p);
        $this->pOn = abs($pOn);
        $this->count = abs($count);
        $this->pCount = (int) ceil($count / $this->pOn);
        if ($this->p + 1 > $this->pCount) {
            $this->pCount++;
        }
        $this->link = (string)$link;
        $this->pOnShow = (bool)$pOnShow;
    }

    public function content(): string
    {
        if ($this->pCount <= 1) {
            return '';
        }
        if ($this->pCount > $this->pCountMax) {
            $pMaxCount = $this->pCountMax - 5;
            $min = $this->p - floor($pMaxCount / 2);
            $max = $this->p + ceil($pMaxCount / 2);
            $since = max([1, $min]);
            $till = min([$this->pCount - 2, $max]);
            $since = $since == 2 ? $since - 1 : $since;
            $till = $till == $this->pCount - 3 ? $till + 1 : $till;
            if ($min < 2) {
                $till = $this->pCountMax - 3;
            }
            if ($max > $this->pCount - 3) {
                $since = $this->pCount - $this->pCountMax + 2;
            }
        }

        return View::render(__DIR__ . '/tpl/control.tpl', get_defined_vars(), $this);
    }
}
