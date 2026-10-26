<?php

namespace Simflex\Core\Controller;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Action
{
    /**
     * Действие контроллера
     *
     * @param string $path Действие. Используется как часть пути URI. :name внутри извлекает часть пути
     *        в переменную
     * @param string $httpMethod HTTP-метод. "all" - любой метод
     */
    public function __construct(
        public string $path,
        public string $httpMethod = 'all',
    ) {
    }
}
