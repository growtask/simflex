<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * Class UserRole
 * @package Simflex\Core\Models
 * @property string $name;
 */
class UserRole extends ModelBase
{
    protected static $table = 'user_role';
    protected static $primaryKeyName = 'role_id';

}