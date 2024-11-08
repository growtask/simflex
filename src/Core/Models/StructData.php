<?php

namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;

/**
 * Class StructData
 *
 * @property int id
 * @property int npp
 * @property int table_id
 * @property int field_id
 * @property string name
 * @property string label
 * @property string help
 * @property string placeholder
 * @property string params
 */
class StructData extends ModelBase
{
    protected static $table = 'struct_data';
    protected static $primaryKeyName = 'id';

    public function getParams()
    {
        return unserialize($this->params);
    }

    public function setParams(array $value): void
    {
        $this->params = serialize($value);
    }
}