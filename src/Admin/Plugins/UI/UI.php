<?php

namespace Simplex\Admin\Plugins\UI;


use Simplex\Admin\Core;
use Simplex\Core\Container;

class UI
{

    public static function datePicker()
    {
        static::js('/theme/ui/datepicker/bootstrap-datepicker.js', 5);
        static::js('/theme/ui/datepicker/bootstrap-datepicker.ru.js', 5);
        static::css('/theme/ui/datepicker/datepicker.css', 5);
    }

    public static function timePicker()
    {
        static::js('/theme/ui/timepicker/bootstrap-timepicker.min.js', 5);
        static::css('/theme/ui/timepicker/bootstrap-timepicker.min.css', 5);
    }

    public static function dateTimePicker()
    {
        static::js('/theme/ui/datetimepicker/bootstrap-datetimepicker.js', 5);
        static::js('/theme/ui/datetimepicker/bootstrap-datetimepicker.ru.js', 5);
        static::css('/theme/ui/datetimepicker/datetimepicker.css', 5);
    }

    public static function fileInput()
    {
        static::js('/theme/ui/fileinput/bootstrap-fileinput.js', 5);
        static::css('/theme/ui/fileinput/bootstrap-fileinput.css', 5);
    }

    public static function treeView()
    {
        static::js('/theme/ui/treeview/jstree.min.js', 5);
        static::js('/theme/ui/treeview/ui-tree.js', 5);
        static::css('/theme/ui/treeview/style.min.css', 5);
    }

    protected static function js($file, $idx)
    {
        Container::getPage()::js(Core::webVendorPath() . $file, $idx);
    }

    protected static function css($file, $idx)
    {
        Container::getPage()::css(Core::webVendorPath() . $file, $idx);
    }

}