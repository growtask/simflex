<?php
namespace Simflex\Core\Models;

use Simflex\Core\ModelBase;
use Simflex\Core\Models\Attrib\FieldOne;

/**
 * @property int item_id
 * @property int module_id
 * @property int menu_id
 * @property string posname
 * @property boolean active
 * @property int npp
 * @property string name
 * @property string params
 *
 * @property ?Module module
 */
#[FieldOne('module', Module::class)]
class ModuleItem extends ModelBase
{
    protected static $table = 'module_item';
    protected static $primaryKeyName = 'item_id';

    public function getParams(): array
    {
        return unserialize($this->params);
    }

    public function setParams(array $params): void
    {
        $this->params = serialize($params);
    }
}