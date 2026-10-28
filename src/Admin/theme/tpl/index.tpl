<?php

use Simflex\Core\System\Container;

\Simflex\Core\Extensions\Alert\Alert::init();

$page->coreJs('/theme/js/table.js');
$page->coreJs('/theme/js/vtable.js');

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>"/>
    <link rel="stylesheet" href="<?= asset('fonts/inter.css') ?>"/>
    <link rel="shortcut icon" href="<?= asset('img/favicon-admin.ico') ?>" type="image/x-icon">

    <?php $fmAssets = \Simflex\Core\Http\Assets::publish(\Simflex\Core\System\Container::getPaths()->getRoot('vendor/growtask/simflex-file-manager/src/assets')); ?>
    <link rel="stylesheet" href="<?= $fmAssets ?>/css/styles.css"/>
    <script src="https://cdn.sn9.ru/js/jquery/3.7.1/jquery.min.js"></script>
    <script>window.sfAdminUrl = '<?= \Simflex\Core\Admin\Page::webVendorPath() ?>';</script>

    <noscript>
        <style>
            /**
              * Возвращает прокрутку для клиентов без JS
              */
            .simplebar-content-wrapper {
                scrollbar-width: auto;
                -ms-overflow-style: auto;
            }

            .simplebar-content-wrapper::-webkit-scrollbar,
            .simplebar-hide-scrollbar::-webkit-scrollbar {
                display: initial;
                width: initial;
                height: initial;
            }
        </style>
    </noscript>

    <title>
        <?php
        echo \Simflex\Core\Admin\Page::get()->getCurrentMenuItem('name') ? \Simflex\Core\Admin\Page::get()->getCurrentMenuItem('name') . ' |' : '' ?>
        Simflex Admin </title>

    <?= $page->getMeta() ?>
</head>

<body>
<div class="container">
    <?php
    include 'partial/header.tpl'; ?>
    <div class="layout">
        <?php
        include 'partial/sidebar.tpl'; ?>
        <div class="layout__content">
            <?= $page->getContent() ?>
        </div>
    </div>
    <?php
    include 'modals/mobilebar.tpl'; ?>
</div>

<?php
include 'modals/sidebar.tpl'; ?>
<?php
include 'modals/delete.tpl'; ?>
<?php
include 'modals/account.tpl'; ?>
<?php
include 'modals/help.tpl'; ?>
<?php
include 'modals/info.tpl'; ?>
<?php
include 'modals/context.tpl'; ?>
<?php
include 'modals/point.tpl'; ?>

<div class="f-manager-mgr">
    <?php
    include \Simflex\Core\System\Container::getPaths()->getRoot('vendor/growtask/simflex-file-manager/src/tpl/index.tpl'); ?>
</div>

<iframe id="iframe-help" src="https://growtask.ru/remoteform.php?tpl=help&ws=https://<?= \Simflex\Core\System\Container::getRequest()->getHost() ?>"
        frameborder="0"></iframe>

<script>
    document.querySelectorAll('.modal-help-open').forEach(btn => {
        btn.addEventListener('click', () => {
            let frameToRemove = document.getElementById("iframe-help");
            frameToRemove.style = 'position: absolute; top: 50%; left: 50%; width: 100%; transform: translate(-50%, -50%); height: 100%; z-index: 1000;';
        })
    })

    window.addEventListener("message", function (event) {
        let frameToRemove = document.getElementById("iframe-help");

        if (event.data === 'close-iframe') {
            if (frameToRemove) {
                // frameToRemove.parentNode.removeChild(frameToRemove);
                frameToRemove.style = 'display: none';
                document.body.style.overflow = "inherit";
            }
        } else if (event.data === 'success') {
            frameToRemove.style = 'display: none';
            const modalSuccess = document.querySelector('.modal-context');
            modalSuccess.classList.add('modal-context--active');
            const modalSuccessTitle = modalSuccess.querySelector('.modal-context__title');
            modalSuccessTitle.innerHTML = 'Заявка успешно отправлена';
        }

    });
</script>

<script src="<?= asset('js/app.js'); ?>"></script>
<script src="<?= asset('js/extra.js'); ?>"></script>

<script src="<?= $fmAssets ?>/js/app.js"></script>
<script>
    fileManager.assetsDir = '<?= $fmAssets ?>';
    fileManager.rootDir = '/uf';
    fileManager.apiBase = '<?= \Simflex\Core\Admin\Page::url('fm') ?>';
</script>

<?= Container::getConfig()->devMode ? Container::get('debugbar')->render() : '' ?>
</body>
</html>