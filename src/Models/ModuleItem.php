<?php

namespace Simflex\Core\Models;

use Simflex\Core\System\ModelBase;

/**
 * @property int item_id
 * @property string module Класс модуля, см. \Simflex\Core\System\ExtensionRegistry
 * @property int menu_id
 * @property string posname
 * @property boolean active
 * @property int npp
 * @property string name
 * @property string params
 */
class ModuleItem extends ModelBase
{
    protected static string $table = 'module_item';
    protected static string $primaryKeyName = 'item_id';

    public function getParams(): array
    {
        $params = $this->params ? @unserialize($this->params) : [];
        return is_array($params) ? $params : [];
    }

    public function setParams(array $params): void
    {
        $this->params = serialize($params);
    }
}
