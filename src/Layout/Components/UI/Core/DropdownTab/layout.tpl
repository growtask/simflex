<?php
/** @var array $data */

$dropdownTabClasses = [
    'dropdown-tab',
    "dropdown-tab-{$data['style']}",
    "dropdown-tab-size_{$data['size']}",
    "dropdown-tab-theme_{$data['theme']}",
    $data['className'] ?? ''
];

$menuTheme = $data['menuTheme'] === 'light' ? '' : 'dropdown-tab__menu_dark';
$menuTabTheme = $data['menuTheme'] === 'light' ? Simflex\Core\Layout\Components\UI\Core\Tab\TabTheme::Light : Simflex\Core\Layout\Components\UI\Core\Tab\TabTheme::Dark;
?>

<div class="<?= implode(' ', $dropdownTabClasses) ?>" data-options='' <?= buildAttrs($data['attributes'] ?? []) ?>>
    <?php if ($data['link']): ?>
        <a href="<?= $data['link'] ?>" class="dropdown-tab__trigger">
            <?php if ($data['icon']): ?>
                <?= renderIcon($data['icon'], 'dropdown-tab__icon') ?>
            <?php endif; ?>

            <span class="dropdown-tab__text"><?= $data['text'] ?></span>

            <?php if ($data['badge']): ?>
                <span class="tab__badge"><?= $data['badge'] ?></span>
            <?php endif; ?>

            <?= renderIcon('chevron-down-sm', 'dropdown-tab__chevron') ?>
        </a>
    <?php else: ?>
        <button class="dropdown-tab__trigger">
            <?php if ($data['icon']): ?>
                <?= renderIcon($data['icon'], 'dropdown-tab__icon') ?>
            <?php endif; ?>

            <span class="dropdown-tab__text"><?= $data['text'] ?></span>

            <?php if ($data['badge']): ?>
                <span class="tab__badge"><?= $data['badge'] ?></span>
            <?php endif; ?>

            <?= renderIcon('chevron-down-sm', 'dropdown-tab__chevron') ?>
        </button>
    <?php endif; ?>

    <ul class="dropdown-tab__menu <?= $menuTheme ?>" role="menu" data-pointer="center">
        <?php foreach ($data['options'] as $option): ?>
            <li class="dropdown-tab__option" role="menuitem" data-value="<?= $option['value'] ?>">
                <?= Simflex\Core\Layout\Components\UI\Core\Tab\Layout::drawTab(
                    className: 'dropdown-tab__link',
                    text: $option['text'],
                    link: $option['link'] ?? '',
                    iconLeft: $option['icon'] ?? '',
                    badge: $option['badge'] ?? '',
                    style: \Simflex\Core\Layout\Components\UI\Core\Tab\TabStyle::Secondary,
                    size: \Simflex\Core\Layout\Components\UI\Core\Tab\TabSize::Small,
                    theme: $menuTabTheme,
                ) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>