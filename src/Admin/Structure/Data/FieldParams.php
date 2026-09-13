<?php

namespace Simflex\Admin\Structure\Data;

readonly class FieldParams
{
    public function __construct(
        public bool $pk = false,
        public bool $e2n = false,
        public bool $hidden = false,
        public int|string $width = 0,
        public string $defaultValue = '',
        public bool $required = false,
        public bool $filter = false,
        public string $onchange = '',
        public bool $readonly = false,
        public string $styleCell = '',
        public int|string $screenWidth = 0,
        public int|string $widthMob = 0,
        public bool $isFk = false,
        public string $fkTable = '',
        public string $fkKey = '',
        public string $fkLabel = '',
        public bool $fkIsPid = false,
        public string $pos = '',
        public string $posGroup = '',
        public string $source = '',
        public string $path = '',
        public string $small = '',
        public string $medium = '',
        public string $large = '',
        public bool $editorMini = false,
        public bool $editorFull = false,
        public string $fk = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'pk' => $this->pk,
            'e2n' => $this->e2n,
            'hidden' => $this->hidden,
            'width' => $this->width,
            'defaultValue' => $this->defaultValue,
            'required' => $this->required,
            'filter' => $this->filter,
            'onchange' => $this->onchange,
            'readonly' => $this->readonly,
            'style_cell' => $this->styleCell,
            'screen_width' => $this->screenWidth,
            'width_mob' => $this->widthMob,
            'is_fk' => $this->isFk,
            'fk_table' => $this->fkTable,
            'fk_key' => $this->fkKey,
            'fk_label' => $this->fkLabel,
            'fk_is_pid' => $this->fkIsPid,
            'pos' => $this->pos,
            'pos_group' => $this->posGroup,
            'source' => $this->source,
            'path' => $this->path,
            'small' => $this->small,
            'medium' => $this->medium,
            'large' => $this->large,
            'editor_mini' => $this->editorMini,
            'editor_full' => $this->editorFull,
            'fk' => $this->fk,
        ];
    }
}
