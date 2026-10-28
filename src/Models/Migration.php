<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * Класс Migration
 * @package Simflex\Core\System\Models
 *
 * @property int id
 * @property string file
 */
class Migration extends ModelBase
{
    protected static string $table = 'migration';
    protected static string $primaryKeyName = 'id';
}
