<?php

class AdminModAccount extends SFModBase {

    public function content() {

        if (count($data = DB::escape($_POST))) {
            $set = array("email = '{$data['email']}', name = '{$data['name']}'");
            if ($data['password']) {
                $password = md5($data['password']);
                $set[] = "password = '$password'";
            }
            $q = "UPDATE user SET " . implode(', ', $set) . " WHERE user_id = " . SFUser::$id;
            $success = DB::query($q);
            if ($success) {
                AdminPlugAlert::success('Данные успешно обновлены', './');
            } else {
                AdminPlugAlert::error('Данные не обновлены', './');
            }
        }

        $data = SFUser::info();
        include 'tpl/index.tpl';
    }

}
