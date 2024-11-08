<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int id
 * @property string file
 */
class Seeder extends ModelBase
{
    protected static $table = 'seeder';
    protected static $primaryKeyName = 'id';
}