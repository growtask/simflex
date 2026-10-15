<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="<?= asset('css/styles.min.css') ?>" />
        <link rel="stylesheet" href="<?= asset('../fonts/inter.css') ?>"/>
        <link rel="shortcut icon" href="<?= asset('img/favicon-admin.ico') ?>" type="image/x-icon">

        <title>Simflex Admin</title>
    </head>
    <body>
        <div class="container">
            <div class="login">
                <div class="login__logo">
                    <img class="login__logo-img" src="<?= asset('img/logo.svg') ?>" alt="" />
                </div>
                <div class="form login-form">
                    <div class="login-form__text">
                        <h4 class="login-form__title">Недостаточно прав для входа в панель управления</h4>
                    </div>
                    <div class="login-form__btns">
                        <a class="BtnPrimarySm" href="/admin/logout/">Выйти</a>
                        <a class="BtnOutlineMonoSm" href="/">На сайт</a>
                    </div>
                </div>
                <div class="login__text">Simflex CMS v<?= getSimflexVersion() ?></div>
            </div>
        </div>
    </body>
</html>
