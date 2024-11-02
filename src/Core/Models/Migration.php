<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * Class Migration
 * @package Simflex\Core\Models
 *
 * @property int id
 * @property string file
 */
class Migration extends ModelBase
{
    protected static $table = 'migration';
    protected static $primaryKeyName = 'id';
}