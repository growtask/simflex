<?php

namespace Simflex\Admin\Plugins\Pagecontrol;

class Pagecontrol
{

    private int $p = 0;
    private int $p_on = 0;
    private int $p_count = 0;
    private int $count = 0;
    private string $link = '?p={p}';
    private bool $p_on_show = false;
    public int $p_count_max = 7;

    public function __construct($p, $p_on, $count, $link = '?p={p}', $p_on_show = false)
    {
        $this->p = abs($p);
        $this->p_on = abs($p_on);
        $this->count = abs($count);
        $this->p_count = (int) ceil($count / $this->p_on);
        if ($this->p + 1 > $this->p_count) {
            $this->p_count++;
        }
        $this->link = (string)$link;
        $this->p_on_show = (bool)$p_on_show;
    }

    public function content(): void
    {
        if ($this->p_count > 1) {
            if ($this->p_count > $this->p_count_max) {
                $p_max_count = $this->p_count_max - 5;
                $min = $this->p - floor($p_max_count / 2);
                $max = $this->p + ceil($p_max_count / 2);
                $since = max([1, $min]);
                $till = min([$this->p_count - 2, $max]);
                $since = $since == 2 ? $since - 1 : $since;
                $till = $till == $this->p_count - 3 ? $till + 1 : $till;
                if ($min < 2) {
                    $till = $this->p_count_max - 3;
                }
                if ($max > $this->p_count - 3) {
                    $since = $this->p_count - $this->p_count_max + 2;
                }
            }

            include dirname(__FILE__) . '/tpl/control.tpl';
        }
    }

}
