<?php

namespace Simflex\Admin\Handlers;

use Simflex\Admin\Fields\FieldAlias;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldContentTemplateSelect;
use Simflex\Admin\Fields\FieldDate;
use Simflex\Admin\Fields\FieldImage;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldPath;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\ParamDefinition;
use Simflex\Admin\Table\Presets\ActiveField;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Presets\NppField;
use Simflex\Admin\Table\Presets\ParentField;
use Simflex\Admin\Table\Repository;
use Simflex\Admin\Table\Table;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Extensions\Content\Model\ModelContent;

/**
 * Страницы сайта. В форме — поля шаблона страницы (provider/templates.php), файлы загружаются через ajax
 */
class Content extends Table
{
    protected static string $model = ModelContent::class;

    public static function getFields(): array
    {
        return [
            new ActiveField(),
            new IdField(name: 'content_id', filter: true),
            new FieldDefinition(
                name: 'template',
                class: FieldContentTemplateSelect::class,
                label: 'Шаблон',
                help: 'Шаблоны страниц объявляются в provider/templates.php',
                e2n: true,
                width: '176',
                filter: true,
            ),
            new NppField(),
            new ParentField(
                name: 'pid',
                table: 'content',
                key: 'content_id',
                display: 'title',
            ),
            new FieldDefinition(
                name: 'date',
                class: FieldDate::class,
                label: 'Дата',
            ),
            new FieldDefinition(
                name: 'title',
                class: FieldString::class,
                label: 'Заголовок',
                width: '1',
                required: true,
                filter: true,
                widthMob: '1',
            ),
            new FieldDefinition(
                name: 'alias',
                class: FieldAlias::class,
                label: 'Алиас',
                source: 'title',
            ),
            new FieldDefinition(
                name: 'path',
                class: FieldPath::class,
                label: 'Путь',
                readonly: true,
            ),
            new FieldDefinition(
                name: 'short',
                class: FieldText::class,
                label: 'Коротко',
            ),
            new FieldDefinition(
                name: 'text',
                class: FieldText::class,
                label: 'Текст',
                editorFull: true,
            ),
            new FieldDefinition(
                name: 'params',
                class: FieldString::class,
                label: 'Параметры',
                hidden: true,
            ),
            new FieldDefinition(
                name: 'photo',
                class: FieldImage::class,
                label: 'Фото',
                e2n: true,
                width: '88',
                widthMob: '50',
                path: 'content',
                small: '200x150',
                large: '800x600',
            ),
        ];
    }

    public static function getParams(): array
    {
        return [
            new ParamDefinition(
                name: 'content_main',
                label: 'Вывод данных',
                paramId: 18,
                paramPid: '',
                pos: 'right',
            ),
            new ParamDefinition(
                name: 'tpl',
                label: 'Шаблон вывода',
                class: FieldString::class,
                paramId: 19,
                paramPid: 18,
                defaultValue: 'mod_list.tpl',
            ),
            new ParamDefinition(
                name: 'cnt_limit',
                label: 'Количество',
                class: FieldInt::class,
                paramId: 20,
                paramPid: 18,
                defaultValue: '3',
            ),
            new ParamDefinition(
                name: 'content_view',
                label: 'Внешний вид',
                paramId: 21,
                paramPid: '',
                pos: 'right',
            ),
            new ParamDefinition(
                name: 'date',
                label: 'Дата',
                class: FieldBool::class,
                paramId: 22,
                paramPid: 21,
            ),
            new ParamDefinition(
                name: 'short',
                label: 'Анонс',
                class: FieldBool::class,
                paramId: 23,
                paramPid: 21,
                defaultValue: '0',
            ),
            new ParamDefinition(
                name: 'more',
                label: 'Кнопка далее',
                class: FieldBool::class,
                paramId: 24,
                paramPid: 21,
                defaultValue: '0',
            ),
            new ParamDefinition(
                name: 'more_text',
                label: 'Текст на кнопке далее',
                class: FieldString::class,
                paramId: 25,
                paramPid: 21,
                defaultValue: 'Читать далее',
            ),
            new ParamDefinition(
                name: 'hide_title',
                label: 'Не показывать заголовок',
                class: FieldBool::class,
                paramId: 26,
                paramPid: 21,
                defaultValue: '0',
            ),
        ];
    }

    public function content(): string
    {
        if (!Container::getRequest()->isAjax() || !isset($_FILES['file'])) {
            return parent::content();
        }
        return $this->ajaxUpload();
    }

    /**
     * Сохраняет загруженный файл, возвращает его url
     */
    public function ajaxUpload(): string
    {
        if (!Container::getRequest()->isAjax()) {
            return '';
        }

        if (!str_starts_with($_FILES['file']['type'], 'image') && !str_starts_with($_FILES['file']['type'], 'video')) {
            $name = $_FILES['file']['name'];
            copy($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/uf/files/content/' . $name);
            return '/uf/files/content/' . $name;
        }

        $ext = '';
        switch ($_FILES['file']['type']) {
            case 'image/gif':
                $ext = 'gif';
                break;
            case 'image/jpeg':
                $ext = 'jpg';
                break;
            case 'image/pjpeg':
                $ext = 'jpg';
                break;
            case 'image/webp':
                $ext = 'webp';
                break;
            case 'image/png':
                $ext = 'png';
                break;
            case 'video/mp4':
                $ext = 'mp4';
                break;
            case 'video/webm':
                $ext = 'webm';
                break;
        }

        $name = md5(time()) . '.' . $ext;
        copy($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/uf/images/content/source/' . $name);

        return '/uf/images/content/source/' . $name;
    }

    protected function initTable(): void
    {
        parent::initTable();
        if (($_GET['action'] ?? '') == 'form') {
            return;
        }

        $this->fields['pid']->filterDataProvider = function () {
            $rows = ModelContent::findAdv()
                ->select(['content_id', 'title as label'])
                ->where(new \Simflex\Core\DB\Expr('EXISTS (SELECT 1 FROM content c WHERE c.pid=content.content_id)'))
                ->orderBy('label')
                ->all('content_id');
            return [$rows];
        };
    }

    protected function tableParamsLoad(): array
    {
        $contentId = (int)($_REQUEST[$this->pk->name] ?? 0);
        $params = parent::tableParamsLoad();

        // поля шаблона страницы, объявленные в коде (provider/templates.php)
        $template = $contentId ? DB::result('SELECT template FROM content WHERE content_id = ?', 'template', [$contentId]) : null;
        foreach (Repository::getTemplateParams((string)$template, $this->table) as $pid => $rows) {
            foreach ($rows as $id => $row) {
                $params[(string)$pid][(string)$id] = $row;
            }
        }

        return $params;
    }
}
