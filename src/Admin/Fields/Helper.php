<?php


namespace Simplex\Admin\Fields;


class Helper
{
    /**
     * @param $row
     * @return Field
     */
    public static function create($row)
    {
        $class = $row['class'];
        if (strpos($class, '\\') === false) {
            $class = "Simplex\Admin\Fields\\$class";
        }
        return new $class($row);
    }
}