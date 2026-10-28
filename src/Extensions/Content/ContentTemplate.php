<?php

namespace Simflex\Core\Extensions\Content;

/**
 * Шаблон страницы, объявленный в provider/templates.php приложения, см. ContentTemplates
 */
readonly class ContentTemplate
{
    /**
     * @param string $name Название в админке
     * @param string $file Файл шаблона относительно Extensions/Content/tpl, например 'index.tpl' или 'news/item.tpl'
     * @param ContentTemplateBlock[] $params Дополнительные поля формы страницы. Их значения хранятся в content.params
     *        по имени поля, например $content->params['meta_title']
     */
    public function __construct(
        public string $name,
        public string $file,
        public array $params = [],
    ) {
    }
}
