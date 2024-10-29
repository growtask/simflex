<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $template_id
 * @property string $template_name
 * @property string $template_path
 */
class ContentTemplate extends ModelBase
{
    protected static $primaryKeyName = 'template_id';
    protected static $table = 'content_template';
}