<div class="content">
    <div class="content__inner">
        <div class="content__head">
            <div class="content__head-wrap">
                <div class="content__head-bg"></div>
                <div class="content__head-left">
                    <h4 class="content__title">Главное меню</h4>
                </div>
                <div class="content__head-right">
                </div>
            </div>
        </div>
        <div class="content__body" data-simplebar>
            <div class="content__body-columns">
                <?php
                foreach ($menu[$cur_id] as $id => $item):
                if ($item['hidden']) {
                    continue;
                }
            ?>

                <div class="list-board">
                    <div class="list-board__title">
                        <svg class="list-board__title-icon" viewBox="0 0 24 24" stroke="#ffffff"
                             fill="none">
                            <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#<?=str_replace('-mini', '', $item['icon'])?>"></use>
                        </svg>
                        <div class="list-board__title-text"><?=$item['name']?></div>
                    </div>
                    <a class="BtnOutlineMonoSm" href="<?=$item['link']?>">Открыть</a>

                    <?php if (isset($menu[$id])):
                        foreach ($menu[$id] as $item):
                    if ($item['hidden']) {
                        continue;
                    }
                    ?>

                            <a class="list-board__link" href="<?=$item['link']?>">
                                <?php if ($item['icon']): ?>
                                    <svg class="list-board__title-icon" viewBox="0 0 16 16" stroke="#0D0D0D"
                                         fill="none">
                                        <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#<?=$item['icon']?>"></use>
                                    </svg>
                                <?php endif; ?>
                                <?=$item['name']?></a>

                    <?php endforeach; endif; ?>
                </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>