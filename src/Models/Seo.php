<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * SEO страницы по её ссылке
 *
 * @property int $seo_id
 * @property int $seo_pid
 * @property string $link
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $metatags
 */
class Seo extends ModelBase
{
    protected static string $primaryKeyName = 'seo_id';
    protected static string $table = 'seo';
}
