<?php

namespace Simflex\Models;

use Simflex\Core\ModelBase;

/**
 * Класс Migration
 * @package Simflex\Core\Models
 *
 * @property int id
 * @property string file
 */
class Migration extends ModelBase
{
    protected static string $table = 'migration';
    protected static string $primaryKeyName = 'id';
}
