<?php

namespace Simplex\Admin\Plug;

use Simplex\Core\DB;

class Log
{

    public static function a($action, $data)
    {
//        $q = "DELETE FROM log WHERE ADDDATE(datetime,INTERVAL 6 MONTH) < NOW()";
//        DB::query($q);
        $action = DB::escape($action);
        $data = DB::escape($data);
        $browser = DB::escape($_SERVER['HTTP_USER_AGENT']);
        $set = array("action = '$action', ip = '{$_SERVER['REMOTE_ADDR']}', browser = '$browser'");
        $set[] = "data = '$data'";
        $q = "INSERT INTO log SET " . implode(', ', $set);
        DB::query($q);
    }

}
