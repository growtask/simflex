<?php

namespace Simflex\Core\Controller;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Action
{
    /**
     * Controller action
     *
     * @param string $action Action. This will be used as a part of the URI path. Use :name within to extract strings from paths
     * @param string $method Target HTTP method. Set to "all" if you want to allow any method
     */
    public function __construct(
        public string $action,
        public string $method = 'all',
    )
    {
    }
}