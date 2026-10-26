<?php

namespace Simflex\Admin;

use Simflex\Core\Privilege;

/**
 * Admin panel menu item declared in code, see Menu::init()
 */
readonly class MenuItem
{
    /**
     * @param string $name Title in the menu
     * @param string $link Address relative to the admin panel root (/site/menu => /admin/site/menu), the item's key.
     *        A link without the leading slash is used as is
     * @param class-string<\Simflex\Admin\Structure\Handler>|'' $handler Section handler: a table (Structure\Table
     *        registered in Admin/Structure/handlers.php) or a section without a table (Structure\Handler naming its class).
     *        Empty — a group listing its subitems
     * @param string $icon Symbol id from theme/img/icons/svg-defs.svg
     * @param string $privilege Privilege needed to see the item, see \Simflex\Core\Privilege
     * @param bool $hidden Not shown in the menu, but the section is available by its link
     * @param MenuItem[] $children Subitems
     */
    public function __construct(
        public string $name,
        public string $link,
        public string $handler = '',
        public string $icon = '',
        public string $privilege = Privilege::ADMIN,
        public bool $hidden = false,
        public array $children = [],
    ) {
    }
}
