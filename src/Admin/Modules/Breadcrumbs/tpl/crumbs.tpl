<li class="breadcrumbs__item">
    <a href="/admin/" class="breadcrumbs__link">
        <span class="breadcrumbs__link-text">Главная</span>
        <?php if ($crumbs): ?>
            <svg class="breadcrumbs__link-icon" viewBox="0 0 24 24">
                <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#chevron"></use>
            </svg>
        <?php endif; ?>
    </a>
</li>

<?php foreach ($crumbs as $i => $c): ?>
    <li class="breadcrumbs__item">
        <a href="<?=$c['link']?>" class="breadcrumbs__link">
            <span class="breadcrumbs__link-text"><?=$c['name']?></span>
            <?php if ($i != count($crumbs) - 1): ?>
                <svg class="breadcrumbs__link-icon" viewBox="0 0 24 24">
                    <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#chevron"></use>
                </svg>
            <?php endif; ?>
        </a>
    </li>
<?php endforeach; ?>
