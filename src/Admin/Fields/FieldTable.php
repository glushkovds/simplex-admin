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
        if (!str_contains(Container::getRequest()->getPath(), 'content_template_param') &&
            $this->params['struct'] && $tmpVal['s'] != $this->params['struct']) {
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

        ob_start();
        include '../theme/tpl/fields/table.tpl';
        return ob_get_clean();
    }
}