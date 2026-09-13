<?php

namespace Simflex\Admin\Base;


use Simflex\Admin\Base\SFImage;
use Simflex\Admin\Base;
use Simflex\Admin\Fields\Helper;
use Simflex\Admin\Page;
use Simflex\Admin\Structure\Repository as StructureRepository;
use Simflex\Core\DB;

class Struct extends Base
{

    public function __construct()
    {
        parent::__construct();
        Page::coreJs('/Base/js/struct.js');

        $this->actionHandlers['field_param'] = array('method' => 'fieldParam');
    }

    /**
     * Выводим доп. параметры по типу поля
     */
    protected function fieldParam()
    {
        $fieldType = (string)@$_GET['field_type'];
        $fieldName = DB::escape(@$_GET['field_name']);
        $table = DB::escape(@$_GET['table']);
        $keyName = DB::escape(@$_GET['key_name']);
        $keyValue = (int)@$_GET['key_value'];

        $rows = StructureRepository::fieldParamsByType($fieldType);

        if (!count($rows)) {
            return;
        }

        $stored = DB::result("SELECT params FROM $table WHERE $keyName = ?", '', [$keyValue]);
        $storedParams = $stored ? unserialize($stored['params']) : [];

        $fields = array();
        foreach ($rows as $row) {
            $row['table'] = $this->table;
            $field = new $row['class']($row);
            $field->form = 'main';
            $field->value = isset($storedParams['main'][$row['name']]) ? $storedParams['main'][$row['name']] : $row['default_value'];
            $fields[] = $field;
        }

        $group = array('label' => 'Доп. параметры');
        $group['fields'] = $fields;
        $portletClass = "field-params-$fieldName";
        include 'tpl/form.portlet.tpl';
    }

    protected function getParams()
    {
        $params = parent::getParams();
        if (!empty($_POST['field_type'])) {
            $rows = StructureRepository::fieldParamsByType((string)$_POST['field_type']);
            foreach ($rows as $row) {
                $row['table'] = $this->table;
                $field = Helper::create($row);
                $field->form = 'main';
                $value = $field->getPost(true, 'main');
                $value = preg_replace("@^'(.+)'$@", '$1', $value);
                $params['main'][$field->name] = $value;
            }
        }
//        print_r($params);die;
        return $params;
    }

    public function save()
    {

        $id = (int)@$_POST[$this->pk->name];
        if ($id) {
            $field = StructureRepository::fieldTypeByClass((string)$_POST['field_type']);

            $sizesRebuild = array();
            if (($field['class'] ?? '') == \Simflex\Admin\Fields\FieldImage::class) {
                $q = "SELECT * FROM $this->table WHERE {$this->pk->name} = $id";
                $row = DB::result($q);
                $oldParams = unserialize($row['params']);
                $oldParams = $oldParams['main'];
                $newParams = $_POST['main'];
                $sizes = array('small', 'medium', 'large');

                foreach ($sizes as $size) {
                    if ($oldParams[$size] != $newParams[$size]) {
                        $sizesRebuild[$size] = $newParams[$size];
                    }
                }
            }

            $ret = parent::save();

            if ($ret && count($sizesRebuild)) {
                $this->imagesRebuild($_POST['table'] ?? '', $_POST['name'], $oldParams['path'], $sizesRebuild);
            }

            return $ret;
        } else {
            return parent::save();
        }
    }

    private function imagesRebuild($table, $fieldName, $dir, $sizesRebuild)
    {
        include_once "{$_SERVER['DOCUMENT_ROOT']}/core/sffile.class.php";
        $q = "SELECT $fieldName FROM $table";
        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            $dir = trim($dir, '/');
            $img = new SFImage("$dir/", $sizesRebuild);
            $img->load("{$_SERVER['DOCUMENT_ROOT']}/uf/images/{$dir}/source/{$row[$fieldName]}", true);
            $img->save();
        }
    }

}
