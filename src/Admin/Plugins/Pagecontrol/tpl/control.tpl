<div class="table__pagination">
    <div class="table__pagination-text">Показаны записи: с    <?=$this->p*$this->p_on+1?> по <?=min(array($this->count, $this->p*$this->p_on+$this->p_on))?> из <?=$this->count?></div>
    <div class="table__pagination-text-mobile">с <?=$this->p*$this->p_on+1?> по <?=min(array($this->count, $this->p*$this->p_on+$this->p_on))?> из <?=$this->count?></div>
    <div class="table__pagination-btns">
        <?php if($this->p!=0): ?>
        <a href="<?=str_replace('{p}', $this->p - 1, $this->link)?>" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-prev">
            <svg viewBox="0 0 24 24">
                <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#chevron-mini"></use>
            </svg>
        </a>
        <?php endif; ?>
        <?php if($this->p_count > $this->p_count_max): ?>
        <?php $i=0; ?>
            <?php if (!$this->p): ?>
            <button class="BtnSecondaryMonoXs">1</button>
        <?php else: ?>
            <a href="<?=str_replace('{p}', 0, $this->link)?>" class="BtnOutlineMonoXs">1</a>
        <?php endif; ?>

                    <?php if ($since > 1): ?>
                <button class="BtnIconOutlineNoneXs table__pagination-btns-mobile-next">
                    ...
                </button>
            <?php endif; ?>

        <?php for($i=$since;$i<=$till;$i++): ?>
                    <?php if ($i == $this->p): ?>
                <button class="BtnSecondaryMonoXs"><?=$i+1?></button>
                    <?php else: ?>
                <a href="<?=str_replace('{p}', $i, $this->link)?>" class="BtnOutlineMonoXs"><?=$i+1?></a>
                    <?php endif; ?>
        <?php endfor; ?>

            <?php if($till<$this->p_count-2): ?>
                <button class="BtnIconOutlineNoneXs table__pagination-btns-mobile-next">
                    ...
                </button>
            <?php endif; ?>

        <?php $i=$this->p_count-1;?>
        <?php if ($i == $this->p): ?>
                <button class="BtnSecondaryMonoXs"><?=$i+1?></button>
        <?php else: ?>
            <a href="<?=str_replace('{p}', $i, $this->link)?>" class="BtnOutlineMonoXs"><?=$i+1?></a>
        <?php endif; ?>
        <?php else: ?>
            <?php for($i=0;$i<$this->p_count;$i++): ?>
                <?php if ($i == $this->p): ?>
                    <button class="BtnSecondaryMonoXs"><?=$i+1?></button>
                <?php else: ?>
                    <a href="<?=str_replace('{p}', $i, $this->link)?>" class="BtnOutlineMonoXs"><?=$i+1?></a>
                <?php endif; ?>
            <?php endfor; ?>
        <?php endif; ?>
        <?php if($this->p < $this->p_count-1) :?>
        <a href="<?=str_replace('{p}', $this->p + 1, $this->link)?>" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-next">
            <svg viewBox="0 0 24 24">
                <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#chevron-mini"></use>
            </svg>
        </a>
        <?php endif; ?>

    </div>
    <div class="table__pagination-btns-mobile">
        <a href="<?=str_replace('{p}', $this->p - 1, $this->link)?>" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-prev">
            <svg viewBox="0 0 24 24">
                <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#chevron-mini"></use>
            </svg>
        </a>
        <span class="table__pagination-btns-mobile-text"><?=$this->p+1?></span>
        <a href="<?=str_replace('{p}', $this->p + 1, $this->link)?>" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-next">
            <svg viewBox="0 0 24 24">
                <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#chevron-mini"></use>
            </svg>
        </a>
    </div>
</div>