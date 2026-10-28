<?php

namespace Simflex\Helpers;

class Tree
{
    /**
     * Разворачивает дерево [pid => [id => row]] в список [id => row] обходом в глубину.
     * Каждая запись получает tree_level (глубину от $rootId) и tree_nchild (число прямых потомков).
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
