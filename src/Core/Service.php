<?php

namespace Simflex\Core;

use Simflex\Core\Helpers\Str;

/**
 * Class Service
 * @package Simflex\Core
 * @deprecated
 */
class Service
{
    private static array $list = [];

    private function __construct()
    {

    }

    public static function &tree2list(array &$tree, int|string $id_start = 0, bool $p_only = false): array
    {
        self::$list = [];
        if (isset($tree[$id_start])) {
            self::tree2listBuild($tree, $id_start, $p_only);
        }
        return self::$list;
    }

    private static function tree2listBuild(array &$tree, int|string $pid, bool $p_only, int $level = 0): void
    {
        if ($p_only) {
            foreach ($tree[$pid] as $id => $item) {
                if (isset($tree[$id])) {
                    $item['tree_level'] = $level;
                    $item['tree_nchild'] = isset($tree[$id]) ? count($tree[$id]) : 0;
                    self::$list[$id] = $item;
                    self::tree2listBuild($tree, $id, $p_only, $level + 1);
                }
            }
        } else {
            foreach ($tree[$pid] as $id => $item) {
                $item['tree_level'] = $level;
                $item['tree_nchild'] = isset($tree[$id]) ? count($tree[$id]) : 0;
                self::$list[$id] = $item;
                if (isset($tree[$id])) {
                    self::tree2listBuild($tree, $id, $p_only, $level + 1);
                }
            }
        }
    }

    public static function translite($string): string
    {
        return Str::translite($string);
    }
}
