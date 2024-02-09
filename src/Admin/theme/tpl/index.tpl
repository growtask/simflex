<?php

use Simflex\Admin\Page;

\Simflex\Admin\Plugins\Alert\Alert::init();

Page::js('/theme/default/js/cookie.js');
Page::coreJs('/theme/js/table.js');
Page::coreJs('/theme/js/vtable.js');

?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="<?= asset('css/styles.min.css') ?>" />
        <link rel="shortcut icon" href="<?= asset('img/favicon.ico') ?>" type="image/x-icon">

        <script   src="https://code.jquery.com/jquery-3.7.0.min.js"   integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="   crossorigin="anonymous"></script>
        <script src="https://cdn.tiny.cloud/1/1xg2o2utn8wcz706ywlb0eyva4j4yg31zqzm9po4no1dc2jl/tinymce/6/tinymce.min.js"
                referrerpolicy="origin"></script>

        <noscript>
            <style>
                /**
                  * Reinstate scrolling for non-JS clients
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
            echo \Simflex\Admin\Core::menuCurItem('name') ? \Simflex\Admin\Core::menuCurItem('name') . ' |' : '' ?>
            <?php
            echo \Simflex\Admin\Core::siteParam('site_name') ?> |
                                                                Simflex Admin </title>

        <?php
        Page::meta() ?>
    </head>

    <body>
        <div class="container">
            <?php include 'partial/header.tpl'; ?>
            <div class="layout">
                <?php include 'partial/sidebar.tpl'; ?>
                <div class="layout__content">
                    <?php Page::content(); ?>
                </div>
            </div>
            <?php include 'modals/mobilebar.tpl'; ?>
        </div>

        <?php include 'modals/sidebar.tpl'; ?>
        <?php include 'modals/delete.tpl'; ?>
        <?php include 'modals/account.tpl'; ?>
        <?php include 'modals/help.tpl'; ?>
        <?php include 'modals/info.tpl'; ?>
        <?php include 'modals/context.tpl'; ?>
        <?php include 'modals/point.tpl'; ?>

        <iframe id="iframe-help" src="https://growtask.ru/remoteform.php?tpl=help&ws=https://<?= $_SERVER['HTTP_HOST']?>" frameborder="0"></iframe>

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
                }
                else if (event.data === 'success') {
                    frameToRemove.style = 'display: none';
                    const modalSuccess = document.querySelector('.modal-context');
                    modalSuccess.classList.add('modal-context--active');
                    const modalSuccessTitle = modalSuccess.querySelector('.modal-context__title');
                    modalSuccessTitle.innerHTML = 'Заявка успешно отправлена';
                }

            });
        </script>

        <script src="<?= asset('js/app.min.js'); ?>"></script>
        <script src="<?= asset('js/extra.js'); ?>"></script>
    </body>
</html>