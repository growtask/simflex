<?php

namespace Simflex\Core;

/**
 * Настройки привилегии, объявленной в коде, см. Privilege::init()
 */
readonly class PrivilegeParams
{
    /**
     * @param string $title Описание в админке
     * @param bool $active Включена по умолчанию: в админке ее можно переключить, переключенное значение хранится в
     *        user_privilege
     * @param bool $switchable Можно переключать в админке. Привилегию разработчика нельзя: без нее никто
     *        не смог бы включить остальные обратно
     */
    public function __construct(
        public string $title,
        public bool $active = true,
        public bool $switchable = true,
    ) {
    }
}
