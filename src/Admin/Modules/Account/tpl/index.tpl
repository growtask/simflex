<div class="content">
<form class="content__inner" action="" method="post">
    <div class="content__head">
        <div class="content__head-bg"></div>
        <div class="content__head-wrap">
            <div class="content__head-left">
                <h4 class="content__title">Данные пользователя</h4>
                <button class="content__head-toggle BtnIconPrimarySm" type="button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M10.7071 5.29289C11.0976 5.68342 11.0976 6.31658 10.7071 6.70711L6.41421 11H20C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H6.41421L10.7071 17.2929C11.0976 17.6834 11.0976 18.3166 10.7071 18.7071C10.3166 19.0976 9.68342 19.0976 9.29289 18.7071L3.29289 12.7071C2.90237 12.3166 2.90237 11.6834 3.29289 11.2929L9.29289 5.29289C9.68342 4.90237 10.3166 4.90237 10.7071 5.29289Z"
                              fill="white" />
                    </svg>
                </button>
            </div>
            <div class="content__head-right">
                <div class="content__head-btns content__head-btns--hidden-desktop">
                    <button type="submit" class="content__btn-save BtnPrimarySm BtnIconLeft">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12L10 17L19 8"
                                  stroke="#ffffff"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round" />
                        </svg>
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="content__body" data-simplebar>
        <?php \Simflex\Admin\Plugins\Alert\Alert::output(); ?>
        <label class="data-point">
            Роль
            <div class="form-control form-control--sm">
                <input value="<?= $data['role_name'] ?>"
                       disabled
                       placeholder="Роль"
                       type="text"
                       class="form-control__input">
            </div>
        </label>
        <label class="data-point">
            Логин
            <div class="form-control form-control--sm">
                <input value="<?= $data['login'] ?>"
                       disabled
                       placeholder="Логин"
                       type="text"
                       class="form-control__input">
            </div>
        </label>
        <label class="data-point">
            Имя
            <div class="form-control form-control--sm">
                <input name="name"
                       value="<?= $data['name'] ?>"
                       placeholder="Имя"
                       type="text"
                       class="form-control__input">
            </div>
        </label>
        <label class="data-point">
            E-mail
            <div class="form-control form-control--sm">
                <input name="email"
                       value="<?= $data['email'] ?>"
                       placeholder="E-mail"
                       type="email"
                       class="form-control__input">
            </div>
        </label>
        <label class="data-point">
            Пароль
            <div class="form-control form-control--pass form-control--sm">
                <input name="password"
                       value=""
                       placeholder="Оставьте пустым, если не требуется изменить"
                       type="password"
                       class="form-control__input">
                <a href="#" class="form-control-show-pass"></a>
            </div>
        </label>
    </div>

    <div class="content__btns">
        <button type="submit" class="content__btn-save BtnPrimarySm BtnIconLeft">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12L10 17L19 8"
                      stroke="#ffffff"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round" />
            </svg>
            Сохранить
        </button>
    </div>
</form>
</div>