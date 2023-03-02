<?php
namespace App\Admin\Fields;

use Simplex\Admin\Fields\Field;
use Simplex\Core\Container;

class FieldTable extends Field
{
    public function input($value)
    {
        // fallback value = []
        $value = !json_decode($value, true) ? '[]' : $value;
        if ($value == '[]' && $this->params['struct']) {
            $s = json_decode($this->params['struct'], true);
            $v = json_decode($value, true);

            $v['s'] = $s['v'];
            $v['v'] = [];
            $value = json_encode($v);
        }

        $tmpVal = json_decode($value, true);
        if (!str_contains(Container::getRequest()->getPath(), 'content_template_param') && $this->params['struct'] && $tmpVal['s'] != $this->params['struct']) {
            $s = json_decode($this->params['struct'], true);
            $v = json_decode($value, true);

            $v['s'] = $s['v'];
            foreach ($v['s'] as $s) {
                for ($i = 0; $i < count($v['v']); ++$i) {
                    if (!isset($v['v'][$i][$s['n']])) {
                        $v['v'][$i][$s['n']] = '';
                    }
                }
            }

            $value = json_encode($v);
        }

        // generate id for the js
        $jsId = (string)crc32($this->inputName());

        // output the anchor
        $html = '<input data-id="' . $jsId . '" type="hidden" name="' . $this->inputName() . '" value="' . htmlspecialchars($value) . '" />';

        // rendering will be performed on the frontend.
        // display base area and basic control buttons here.

        $html .= '<div data-id="' . $jsId . '" data-field="' . $this->name . '">';
        $html .= '<div>';
        $html .= '<div>
    <button class="btn btn-default btn-sm" onclick="TableEditor.onRowEdit(\''.$jsId.'\', null);return false;"><i class="fa fa-plus"></i> Добавить строку</button>
</div>';
        $html .= '</div>';
        $html .= '<div style="overflow: auto">';
        $html .= '<table class="table"><tbody><tr class="heading"></tr></tbody></table>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<script>TableEditor.init("' . $jsId . '");</script>';

        return $html;
    }
}