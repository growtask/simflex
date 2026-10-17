<?php

namespace Simflex\Extensions\Content;

/**
 * Page template declared in the app's provider/templates.php, see ContentTemplates
 */
readonly class ContentTemplate
{
    /**
     * @param string $name Title in the admin panel
     * @param string $file Template file, relative to Extensions/Content/tpl, e.g. 'index.tpl' or 'news/item.tpl'
     * @param ContentTemplateBlock[] $params Additional fields of the page form. Their values are kept in content.params
     *        by the field name, e.g. $content->params['meta_title']
     */
    public function __construct(
        public string $name,
        public string $file,
        public array $params = [],
    ) {
    }
}
