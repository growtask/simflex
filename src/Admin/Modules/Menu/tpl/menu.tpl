<div class="sidebar__menu menu" data-simplebar>
    <?php
    foreach ($menu[0] as $id => $item): ?><?php
        if ($item['hidden']) {
            continue;
        } ?>

        <div class="menu__item">
            <?php
            if (!isset($menu[$id])): ?>
                <a class="menu__item-path" href=<?= $item['link']?>></a>
                <a href="<?= $item['link'] ?>" class="menu__list-item menu__item-link">
                    <?php if ($item['icon']): ?>
                        <svg class="menu__item-icon menu__item-icon--main" viewBox="0 0 24 24">
                            <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#<?= $item['icon'] ?>"></use>
                        </svg>
                    <?php endif; ?>
                    <span class="menu__item-text"><?= $item['name'] ?></span>
                </a>
            <?php
            else: ?>
                <a class="menu__item-path" href=<?= $item['link']?>></a>
                <button class="menu__item-link">
                    <?php if ($item['icon']): ?>
                        <svg class="menu__item-icon menu__item-icon--main" viewBox="0 0 24 24">
                            <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#<?= $item['icon'] ?>"></use>
                        </svg>
                    <?php endif; ?>
                    <span class="menu__item-text"><?= $item['name'] ?></span>
                    <svg class="menu__item-icon menu__item-icon--arrow" viewBox="0 0 24 24">
                        <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#chevron"></use>
                    </svg>
                </button>
                <ul class="menu__list" data-menu-level="3">
                    <?php
                    foreach ($menu[$id] as $id2 => $item): ?><?php
                        if ($item['hidden']) {
                            continue;
                        } ?>

                        <?php
                        if (isset($menu[$id2])): ?>
                            <li class="menu__list-sub menu__sub">
                                <button class="menu__sub-link">
                                    <?php if ($item['icon']): ?>
                                        <svg class="menu__sub-icon menu__sub-icon--main" viewBox="0 0 16 16">
                                            <use xlink:href="<?= asset(
                                                'img/icons/svg-defs.svg'
                                            ) ?>#<?= $item['icon'] ?>"></use>
                                        </svg>
                                    <?php endif; ?>
                                    <span class="menu__sub-text"><?= $item['name'] ?></span>
                                    <svg class="menu__sub-icon menu__sub-icon--arrow" viewBox="0 0 24 24">
                                        <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#chevron"></use>
                                    </svg>

                                </button>

                                <ul class="menu__list" data-menu-level="4">
                                    <?php
                                    foreach ($menu[$id2] as $id3 => $item): ?><?php
                                        if ($item['hidden']) {
                                            continue;
                                        } ?>
                                        <li class="menu__list-item">
                                            <a href="<?= $item['link'] ?>"
                                               class="menu__list-link LinkSecondary2"><?= $item['name'] ?></a>
                                        </li>
                                    <?php
                                    endforeach; ?>
                                </ul>
                            </li>
                        <?php
                        else: ?>
                            <li class="menu__list-item">
                                <a href="<?= $item['link'] ?>" class="menu__list-link">
                                    <?php if ($item['icon']): ?>
                                        <svg viewBox="0 0 16 16">
                                            <use xlink:href="<?= asset(
                                                'img/icons/svg-defs.svg'
                                            ) ?>#<?= $item['icon'] ?>"></use>
                                        </svg>
                                    <?php endif; ?>
                                    <?= $item['name'] ?>
                                </a>
                            </li>
                        <?php
                        endif; ?><?php
                    endforeach; ?>
                </ul>
            <?php
            endif; ?>
        </div>
    <?php
    endforeach; ?>
</div>