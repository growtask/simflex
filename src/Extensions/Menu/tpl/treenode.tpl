<?php
/**
 * @var \Simflex\Core\Extensions\Menu\Menu $this
 * @var int $pid
 * @var int $lvl
 */
?>
<ul class="level-<?= $lvl ?>">
    <?php foreach ($this->menu[$pid] as $id => $item): ?>
        <?php if (empty($item['hidden'])): ?>
            <li>
                <a<?= $item['class-a'] ? ' class="' . implode(' ', $item['class-a']) . '"' : '' ?> href="<?= $item['link'] ?>"><?= $item['name'] ?></a>
                <?php if ($lvl < $this->params['max_level']): ?>
                    <?= $this->tree($id, $lvl + 1) ?>
                <?php endif ?>
            </li>
        <?php endif ?>
    <?php endforeach ?>
</ul>
