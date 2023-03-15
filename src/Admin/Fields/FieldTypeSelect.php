<?php
namespace App\Admin\Fields;

use Simplex\Admin\Fields\Field;
use Simplex\Core\DB;

class FieldTypeSelect extends Field
{
    public function input($value)
    {
        $existing = DB::assoc('SELECT name FROM shared_type');

        $out = '<select name="' . $this->inputName() . '[]" class="form-control cat-select" multiple>';
        foreach ($existing as $cat) {
            $out .= '<option ' . (in_array($cat['name'], explode(',', $value)) ? ' selected' : '') . '>' . $cat['name'] . '</option>';
        }
        return $out .'</select>';
    }

    public function getPOST($simple = false, $group = null)
    {
        $data = $_POST[$this->inputName()] ?: [];
        foreach ($data as $val) {
            if (!DB::result('SELECT COUNT(*) as c FROM shared_type WHERE name = ?', 'c', [$val])) {
                DB::query('INSERT INTO shared_type (name) VALUES (?)', [$val]);
            }
        }

        return "'" . DB::escape(implode(',', $data)) . "'";
    }
}