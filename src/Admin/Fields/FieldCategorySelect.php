<?php
namespace App\Admin\Fields;

use Simplex\Admin\Fields\Field;
use Simplex\Core\DB;

class FieldCategorySelect extends Field
{
    public function input($value)
    {
        $this->syncCategories();

        $existing = DB::assoc('SELECT name FROM shared_category');

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
            if (!DB::result('SELECT COUNT(*) as c FROM shared_category WHERE name = ?', 'c', [$val])) {
                DB::query('INSERT INTO shared_category (name) VALUES (?)', [$val]);
            }
        }

        return "'" . DB::escape(implode(',', $data)) . "'";
    }

    protected function syncCategories()
    {
        $realCats = DB::assoc('SELECT title as name FROM content WHERE template_id = 6');
        $existing = DB::assoc('SELECT name FROM shared_category');

        $toAdd = array_diff($realCats, $existing);
        foreach ($toAdd as $cat) {
            DB::query('INSERT INTO shared_category (name, linked_to) VALUES (?, ?)', [$cat['name'], DB::result('SELECT content_id FROM content WHERE title = ? AND template_id = 6', 'content_id', [$cat['name']])]);
        }
    }
}