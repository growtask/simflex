<?php

namespace Simflex\Core\Helpers;

class Tree
{
    /**
     * Flattens a tree [pid => [id => row]] into a depth-first list [id => row].
     * Each row gets tree_level (depth from $rootId) and tree_nchild (number of direct children).
     */
    public static function toList(array $tree, int|string $rootId = 0): array
    {
        $list = [];
        if (isset($tree[$rootId])) {
            self::collect($tree, $rootId, 0, $list);
        }
        return $list;
    }

    private static function collect(array $tree, int|string $pid, int $level, array &$list): void
    {
        foreach ($tree[$pid] as $id => $item) {
            $item['tree_level'] = $level;
            $item['tree_nchild'] = isset($tree[$id]) ? count($tree[$id]) : 0;
            $list[$id] = $item;
            if (isset($tree[$id])) {
                self::collect($tree, $id, $level + 1, $list);
            }
        }
    }
}
