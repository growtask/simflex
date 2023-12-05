<div class="sidebar-modal">
    <div class="sidebar-modal__mask"></div>
    <div class="sidebar-modal__inner">
        <div class="sidebar-modal__head">
            <div class="sidebar-modal__title">Меню</div>
            <button class="sidebar-modal__close BtnIconPrimarySm">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                          fill="#ffffff" />
                </svg>
            </button>
        </div>
        <div class="sidebar-modal__menu menu">
            <?php
            $menu = \Simflex\Core\Container::getCore()::menu();

            foreach ($menu[0] as $id => $item): ?><?php
                if ($item['hidden']) {
                    continue;
                } ?>

                <div class="menu__item">
                    <?php
                    if (!isset($menu[$id])): ?>
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
                        <button class="menu__item-link">
                            <svg class="menu__item-icon menu__item-icon--main" viewBox="0 0 24 24">
                                <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#<?= $item['icon'] ?>"></use>
                            </svg>
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
                                            <svg class="menu__sub-icon menu__sub-icon--main" viewBox="0 0 16 16">
                                                <use xlink:href="<?= asset(
                                                    'img/icons/svg-defs.svg'
                                                ) ?>#<?= $item['icon'] ?>"></use>
                                            </svg>
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
                                            <?php
                                            if ($item['icon']): ?>
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
        <div class="sidebar-modal__bottom">
            <div class="sidebar-modal__bottom-text">Simflex CMS ver <?=SF_VERSION?> <?=SF_VERSION_DATE?></div>
            <button class="BtnSecondaryMonoSm modal-help-open">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14.0497 6C15.0264 6.19057 15.924 6.66826 16.6277 7.37194C17.3314 8.07561 17.8091 8.97326 17.9997 9.95M14.0497 2C16.0789 2.22544 17.9713 3.13417 19.4159 4.57701C20.8606 6.01984 21.7717 7.91101 21.9997 9.94M10.2266 13.8631C9.02506 12.6615 8.07627 11.3028 7.38028 9.85323C7.32041 9.72854 7.29048 9.66619 7.26748 9.5873C7.18576 9.30695 7.24446 8.96269 7.41447 8.72526C7.46231 8.65845 7.51947 8.60129 7.63378 8.48698C7.98338 8.13737 8.15819 7.96257 8.27247 7.78679C8.70347 7.1239 8.70347 6.26932 8.27247 5.60643C8.15819 5.43065 7.98338 5.25585 7.63378 4.90624L7.43891 4.71137C6.90747 4.17993 6.64174 3.91421 6.35636 3.76987C5.7888 3.4828 5.11854 3.4828 4.55098 3.76987C4.2656 3.91421 3.99987 4.17993 3.46843 4.71137L3.3108 4.86901C2.78117 5.39863 2.51636 5.66344 2.31411 6.02348C2.08969 6.42298 1.92833 7.04347 1.9297 7.5017C1.93092 7.91464 2.01103 8.19687 2.17124 8.76131C3.03221 11.7947 4.65668 14.6571 7.04466 17.045C9.43264 19.433 12.295 21.0575 15.3284 21.9185C15.8928 22.0787 16.1751 22.1588 16.588 22.16C17.0462 22.1614 17.6667 22 18.0662 21.7756C18.4263 21.5733 18.6911 21.3085 19.2207 20.7789L19.3783 20.6213C19.9098 20.0898 20.1755 19.8241 20.3198 19.5387C20.6069 18.9712 20.6069 18.3009 20.3198 17.7333C20.1755 17.448 19.9098 17.1822 19.3783 16.6508L19.1835 16.4559C18.8339 16.1063 18.6591 15.9315 18.4833 15.8172C17.8204 15.3862 16.9658 15.3862 16.3029 15.8172C16.1271 15.9315 15.9523 16.1063 15.6027 16.4559C15.4884 16.5702 15.4313 16.6274 15.3644 16.6752C15.127 16.8453 14.7828 16.904 14.5024 16.8222C14.4235 16.7992 14.3612 16.7693 14.2365 16.7094C12.7869 16.0134 11.4282 15.0646 10.2266 13.8631Z"
                        stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Поддержка</span>
            </button>
        </div>
    </div>
</div>