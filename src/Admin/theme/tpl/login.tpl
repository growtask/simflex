<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="<?=asset('css/styles.min.css');?>" />

        <title>Simflex Admin</title>
    </head>
    <body>
        <div class="container">
            <div class="login">
                <div class="login__logo">
                    <img class="login__logo-img" src="<?=asset('img/logo.svg')?>" alt="" />
                </div>
                <form class="form login-form" action="/admin/login/?r=<?php echo urlencode($back) ?>">
                    <div class="login-form__text">
                        <h4 class="login-form__title">Вход в панель управления</h4>
                    </div>
                    <div class="login-form__fields">
                        <div class="form-control form-control--sm form-control--login">
                            <input placeholder="Логин" name="login[login]" type="text" class="form-control__input" />
                        </div>
                        <div class="form-control form-control--sm form-control--pass">
                            <input placeholder="Пароль" name="login[password]" type="password" class="form-control__input" />
                            <a href="#" class="form-control-show-pass"></a>
                        </div>
                    </div>
                    <label class="login-form__bid">
                        <div class="form-control">
                            <input type="checkbox" name="login[remember]" checked class="form-control__checkbox" />
                        </div>
                        <div class="login-form__bid-text">Запомнить меня</div>
                    </label>
                    <div class="login-form__btns">
                        <button class="BtnPrimarySm" type="submit">Войти</button>
                        <button class="BtnOutlineMonoSm modal-restore-pass" type="button">Не помню пароль</button>
                    </div>
                </form>
                <div class="login__text">Simflex CMS ver <?=SF_VERSION?> <?=SF_VERSION_DATE?></div>
            </div>
        </div>

        <?php include 'modals/context.tpl'; ?>

        <iframe id="iframe-login" src="https://growtask.ru/remoteform.php?tpl=pass&ws=https://<?= $_SERVER['HTTP_HOST']?>" frameborder="0"></iframe>

        <script>

            document.querySelectorAll('.modal-restore-pass').forEach(btn => {
                btn.addEventListener('click', () => {
                    let frameToRemove = document.getElementById("iframe-login");
                    frameToRemove.style = 'position: absolute; top: 50%; left: 50%; width: 100%; transform: translate(-50%, -50%); height: 100%; z-index: 1000;';
                })
            })

            window.addEventListener("message", function (event) {
                let frameToRemove = document.getElementById("iframe-login");

                if (event.data === 'close-iframe') {
                    if (frameToRemove) {
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

        <script src="<?=asset('js/app.min.js')?>"></script>
    </body>
</html>