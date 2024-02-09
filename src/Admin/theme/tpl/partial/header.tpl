<header class="header">
    <div class="header__inner">
        <div class="header__left">
            <div class="header__logo-wrapper">
                <img src="<?=asset('img/logo.svg')?>" alt="Simflex CMS" class="header__logo" />
                <a href="/admin/" class="header__logo-link"></a>
            </div>
            <div class="header__breadcrumbs">
                <div class="breadcrumbs">
                    <ul class="breadcrumbs__list">
                        <?php \Simflex\Admin\Page::position('breadcrumbs'); ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="header__right">
            <div class="header__user btn-user">
                <img class="btn-user__img" src="/theme/default/img/favicon.ico" />
                <div class="btn-user__name">
                    <div class="btn-user__title"><?=\Simflex\Core\Container::getUser()->login?></div>
                    <svg class="btn-user__icon" viewBox="0 0 24 24">
                        <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#chevron"></use>
                    </svg>
                </div>
                <div class="btn-user__modal list2">
                    <div class="list2__wrapper">
                        <div class="list2__title">Управление</div>
                        <div class="list2__items">
                            <a class="list2__item LinkSecondary" href="/admin/account/">
                                <svg fill="none" viewBox="0 0 16 16" stroke="#262626">
                                    <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#user-mini"></use>
                                </svg>
                                Настроить профиль
                            </a>
                            <a class="BtnSecondarySm BtnIconLeft" href="/admin/logout/">
                                <svg fill="none" viewBox="0 0 25 25" stroke="#ffffff">
                                    <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#exit"></use>
                                </svg>
                                Выйти из профиля
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="header__link BtnSecondaryMonoSm" href="/" target="_blank">
                <svg fill="none" viewBox="0 0 25 25" stroke="#ffffff">
                    <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#planet"></use>
                </svg>
                На сайт</a>
        </div>
    </div>
</header>