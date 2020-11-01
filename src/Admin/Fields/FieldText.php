<?php

namespace Simplex\Admin\Fields;


use Simplex\Admin\Plugins\Editor\Editor;

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
        if ($this->readonly) {
            return '<textarea class="form-control sf-editor-readonly" rows="4" cols="20"'
                . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"')
                . ' readonly>' . htmlspecialchars($value) . '</textarea>';
        }
        return '<textarea class="form-control' . ($this->editor ? ' sf-editor-' . $this->editor : '')
            . '" name="' . $this->inputName() . '" rows="4" cols="20"'
            . (empty($this->placeholder) ? '' : ' placeholder="' . $this->placeholder . '"')
            . '>' . htmlspecialchars($value) . '</textarea>';
    }

}