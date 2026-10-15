<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * @property int id
 * @property string file
 */
class Seeder extends ModelBase
{
    protected static string $table = 'seeder';
    protected static string $primaryKeyName = 'id';
}
