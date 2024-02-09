<?php

namespace Simflex\Admin\Fields;


use Simflex\Admin\Plugins\Editor\Editor;
use Simflex\Admin\Fields\Field;

class FieldText extends Field
{

    public function __construct($row)
    {
        parent::__construct($row);
        $this->editor = '';
        if (!empty($this->params['editor_mini'])) {
            $this->editor = 'mini';
        }
        if (!empty($this->params['editor_full'])) {
            $this->editor = 'full';
        }
    }

    public function loadUI($onForm = false)
    {
        if ($onForm) {
            if ($this->readonly) {
                Editor::tinymce('readonly', 'sf-editor-readonly');
            }
            if ('mini' == $this->editor) {
                Editor::tinymce('mini', 'sf-editor-mini');
            }
            if ('full' == $this->editor) {
                Editor::tinymce('full', 'sf-editor-full');
            }
        }
    }

    public function input($value)
    {
        return '<div class="form-control form-control--sm">
                                    <textarea class="form-control__textarea '.($this->readonly ? ' sf-editor-readonly' : ''). ($this->editor ? ' sf-editor-' . $this->editor : '').'" placeholder="' . $this->placeholder . '"
                                        name="' . $this->inputName() . '">' . htmlspecialchars($value) . '</textarea>
                                </div>';
    }

}