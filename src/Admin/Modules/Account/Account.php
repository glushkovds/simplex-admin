<?php

namespace Simplex\Admin\Modules\Account;

use Simplex\Admin\Plugins\Alert\Alert;;
use Simplex\Core\DB;
use Simplex\Core\ModuleBase;
use Simplex\Core\User;

class Account extends ModuleBase
{

    public function content()
    {

        if (count($data = DB::escape($_POST))) {
            $set = array("email = '{$data['email']}', name = '{$data['name']}'");
            if ($data['password']) {
                $password = md5($data['password']);
                $set[] = "password = '$password'";
            }
            $q = "UPDATE user SET " . implode(', ', $set) . " WHERE user_id = " . User::$id;
            $success = DB::query($q);
            if ($success) {
                Alert::success('Данные успешно обновлены', './');
            } else {
                Alert::error('Данные не обновлены', './');
            }
        }

        $data = User::info();
        include 'tpl/index.tpl';
    }

}
