<?php

namespace Simflex\Core\Extensions\Content;

use Simflex\Core\Extensions\Content\Models\Content;
use Simflex\Core\System\ModuleBase;

/**
 * Выводит список материалов (меню) из раздела Материалы в админке (таблица content)
 */
class ModuleContent extends ModuleBase
{
    protected function content(): string
    {
        $content_id = empty($this->params['content_id']) ? 0 : (int)$this->params['content_id'];
        $cnt_limit = empty($this->params['cnt_limit']) ? 0 : abs($this->params['cnt_limit']);

        $query = Content::findAdv()
            ->asArray()
            ->select(['content_id', 'date', 'title', 'path', 'short', 'text', 'photo'])
            ->where(['active' => 1])
            ->orderBy('date DESC, content_id');
        if ($content_id) {
            $query->andWhere(['pid' => $content_id]);
        }
        if ($cnt_limit) {
            $query->limit((int)$cnt_limit);
        }
        $rows = $query->all();

        if (!count($rows)) {
            return '';
        }
        $tpl = __DIR__ . '/tpl/mod_list.tpl';
        if (!empty($this->params['tpl']) && is_file(__DIR__ . '/tpl/' . $this->params['tpl'] . '.tpl')) {
            $tpl = __DIR__ . '/tpl/' . $this->params['tpl'] . '.tpl';
        }
        return $this->render($tpl, ['rows' => $rows]);
    }
}
