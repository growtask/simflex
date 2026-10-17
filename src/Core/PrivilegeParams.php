<?php

namespace Simflex\Core;

/**
 * Privilege settings declared in code, see Privilege::init()
 */
readonly class PrivilegeParams
{
    /**
     * @param string $title Description in the admin panel
     * @param bool $active Enabled by default: the admin panel may switch it, the switched value is kept in user_privilege
     * @param bool $switchable Can be switched in the admin panel. The developer one can't: without it nobody
     *        could switch the others back
     */
    public function __construct(
        public string $title,
        public bool $active = true,
        public bool $switchable = true,
    ) {
    }
}
