<div class="modal-account">
    <div class="modal-account__mask"></div>
    <div class="modal-account__inner">
        <div class="modal-account__header">
            <h5 class="modal-account__title"><?=\Simflex\Core\Container::getUser()->login?></h5>
            <button id="modal-account-btn-close" class="modal-account__close BtnIconPrimarySm">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                          fill="#ffffff" />
                </svg>
            </button>
        </div>
        <div class="modal-account__menu">
            <a class="list2__item LinkSecondary" href="/admin/account/">

                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2 13.3333C3.55719 11.6817 5.67134 10.6667 8 10.6667C10.3287 10.6667 12.4428 11.6817 14 13.3333M11 5C11 6.65685 9.65685 8 8 8C6.34315 8 5 6.65685 5 5C5 3.34315 6.34315 2 8 2C9.65685 2 11 3.34315 11 5Z"
                        stroke="#0D0D0D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                Настроить профиль
            </a>
        </div>
        <div class="modal-account__bottom">
            <a id="modal-account-btn-exit" class="BtnSecondarySm" href="/admin/logout/">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16 17L21 12M21 12L16 7M21 12H9M9 3H7.8C6.11984 3 5.27976 3 4.63803 3.32698C4.07354 3.6146 3.6146 4.07354 3.32698 4.63803C3 5.27976 3 6.11984 3 7.8V16.2C3 17.8802 3 18.7202 3.32698 19.362C3.6146 19.9265 4.07354 20.3854 4.63803 20.673C5.27976 21 6.11984 21 7.8 21H9"
                        stroke="#0D0D0D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Выйти из профиля
            </a>
        </div>
    </div>
</div>