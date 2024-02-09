<div class="sidebar">
    <div class="sidebar__mask"></div>
    <div class="sidebar__inner">
        <div class="sidebar__top">
            <h5 class="sidebar__title">Меню</h5>
            <button class="sidebar__btn BtnIconPrimarySm">
                <svg class="menu__item-icon menu__item-icon--arrow" fill="white" viewBox="0 0 24 24">
                    <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#arrow"></use>
                </svg>
            </button>
        </div>
        <?php \Simflex\Admin\Page::position('menu'); ?>
        <div class="sidebar__bottom">
            <p class="sidebar__bottom-text">Simflex CMS ver <?=SF_VERSION?> <?=SF_VERSION_DATE?></p>
            <button class="sidebar__bottom-btn modal-help-open BtnSecondaryMonoSm">
                <svg viewBox="0 0 24 24">
                    <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#phone"></use>
                </svg>
                <span>Поддержка</span>
            </button>
        </div>
    </div>
</div>