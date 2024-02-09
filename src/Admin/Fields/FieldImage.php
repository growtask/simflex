<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Fields\PlugJQuery;
use Simflex\Core\Image;
use Simflex\Admin\Fields\FieldFile;

class FieldImage extends FieldFile
{

    public $sizes = array();

    public function __construct($row)
    {
        parent::__construct($row);
        if (!empty($this->params['small'])) {
            $this->sizes['small'] = $this->params['small'];
        }
        if (!empty($this->params['medium'])) {
            $this->sizes['medium'] = $this->params['medium'];
        }
        if (!empty($this->params['large'])) {
            $this->sizes['large'] = $this->params['large'];
        }
    }

    public function loadUI($onForm = false)
    {
        parent::loadUI($onForm);
        if (!$onForm && class_exists('PlugJQuery')) {
            PlugJQuery::plugFancybox();
        }
    }

    public function input($value)
    {
        $imgPath = '/uf/images/' . $this->path . 'preview/' . $value;
        $defaultImg = asset('img/default-img.png');

        $imgSize =  filesize($_SERVER['DOCUMENT_ROOT'] . $imgPath);
        $sizeDenom = 'б';
        if ($imgSize > 1024) {
            $imgSize /= 1024;
            $sizeDenom = 'кб';
        }
        if ($imgSize > 1024) {
            $imgSize /= 1024;
            $sizeDenom = 'мб';
        }
        if ($imgSize > 1024) {
            $imgSize /= 1024;
            $sizeDenom = 'гб';
        }
        $imgSize = round($imgSize);

        $activeDisplay = $value ? 'display:block;' : '';
        $activeClass = $value ? 'form-control__file--active' : '';
        $activeText = $value ?: 'Перетащите или загрузите файл';

        $s = <<<DATA
<label class="form-control">
                                    <div class="form-control__file $activeClass">
                                        <img src="$imgPath" onerror="this.src = '$defaultImg'" alt="" class="form-control__file-img">
                                        <div class="form-control__file-area-wrapper drop-area">
                                            <div class="form-control__file-area">
                                                <input type="file" name="" accept="image/*"
                                                    class="form-control__file-input">
                                                    <input type="hidden" data-path="$this->path" name="$this->name" value="$value" />
                                                <div class="form-control__file-progressbar progressbar">
                                                    <div class="progressbar__bg">
                                                        <div class="progressbar__progress"></div>
                                                    </div>
                                                </div>
                                                <div class="form-control__file-head">
                                                    <div class="form-control__file-title">$activeText
                                                    </div>
                                                    <div class="form-control__file-size" style="$activeDisplay">$imgSize $sizeDenom</div>
                                                </div>
                                                <div class="form-control__file-btns">
                                                    <div
                                                        class="form-control__file-btn btn-file-upload BtnPrimarySm BtnIconLeft">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M21 15V16.2C21 17.8802 21 18.7202 20.673 19.362C20.3854 19.9265 19.9265 20.3854 19.362 20.673C18.7202 21 17.8802 21 16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V15M17 8L12 3M12 3L7 8M12 3V15"
                                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        Загрузить
                                                    </div>
                                                    <button type="button"
                                                        class="form-control__file-btn btn-file-reset BtnSecondarySm BtnIconLeft">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path style="fill: #ffffff !important" fill-rule="evenodd"
                                                                clip-rule="evenodd"
                                                                d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                                                fill="#ffffff" />
                                                        </svg>
                                                        Удалить
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
DATA;

        return $s;

        // http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image
        $thumb = $thumbNoImage = "/vendor/glushkovds/simplex-admin/src/Admin/theme/img/noimage.90x90.gif";
        $source = '';
        $imgPath = 'uf/images/' . $this->path . 'preview/' . $value;
        if (is_file("../$imgPath")) {
            $thumb = "/$imgPath";
            $source = '/uf/images/' . $this->path . 'source/' . $value;
        }

        $s = '<div class="fileinput fileinput-new" data-provides="fileinput" style="float: left">';
        $s .= '
            <div class="fileinput-new thumbnail" style="max-width: 90px; max-height: 90px; float: left">
                ' . ($source ? '<a href="' . $source . '" class="fancybox"><img src="' . $thumb . '" alt="" /></a>' : '<img src="' . $thumb . '" alt="" />') . '
                <input type="hidden" class="thumb-noimage" value="' . $thumbNoImage . '" />
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 90px; max-height: 90px; float: left"></div>
        ';
        if (!$this->readonly) {
            $s .= '
                <div style="padding-left: 15px; float: left">
                    <span class="btn btn-default btn-file">
                        <span class="fileinput-new"> Выбрать изображение </span>
                        <span class="fileinput-exists"> Выбрать изображение </span>
                        <input type="file" name="' . $this->name . '">
                    </span>
                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput"> Отмена </a>
                </div>
            ';
            $s .= '<input type="hidden" name="' . $this->name . '_old" value="' . $value . '" />';
        }
        $s .= '</div>';
        if (is_file("../uf/images/{$this->path}source/$value")) {
            $s .= '<p class="form-control-static" style="display: block; float: left; padding-left: 20px">';
            $s .= '<span class="s11">' . round(filesize("../uf/images/{$this->path}source/$value") / 1024) . ' кБ</span>';
            if (!$this->required && !$this->readonly) {
                $s .= ' &nbsp; <a href="javascript:;" onclick="deleteField(this)">удалить</a>';
            }
            $s .= '</p>';
        }
        $s .= '<div class="clearfix"></div>';
        return $s;
    }

    public function getPOST($simple = false, $group = null)
    {
        $path = explode('/', $_POST[$this->name]);
        return end($path);
    }

    public function show($row)
    {
        echo $this->showDetail($row);
    }

    public function showDetail($row)
    {
        $value = $row[$this->name];

        return '  <div class="table__row-photo">
                            <img src="/uf/images/'.$this->path.'preview/' . $value . '" onerror="this.src=`'.asset('img/default-img.png').'`" alt="" />
                        </div>';
    }

    public function delete($value)
    {
        if ($this->required || $this->readonly) {
            return false;
        }
        $img = new Image($this->path);
        $img->delete($value);
        return true;
    }

}