<?php


namespace Simplex\Admin;


use Simplex\Admin\Plug\Log;
use Simplex\Core\DB;

class Auth extends \Simplex\Core\Auth
{
    public static function login($login, $password)
    {
        if (empty($login) || empty($password)) {
            return;
        }
        if (preg_match('@^[0-9a-z\@\-\.]+$@i', $login)) {
            DB::bind(array('USER_LOGIN' => strtolower($login)));
            $q = "SELECT u.user_id, u.role_id, u.login, u.password
        FROM user u
        JOIN user_role r ON r.role_id=u.role_id
        WHERE login=@USER_LOGIN
          AND u.active=1
          AND r.active=1";
            if ($row = DB::result($q)) {
                if (md5($password) === $row['password']) {
                    $hash = md5(rand(0, 999) . microtime());
                    $_SESSION['admin_user_id'] = $row['user_id'];
                    $_SESSION['admin_user_hash'] = $hash;

                    DB::bind(array('USER_ID' => $row['user_id'], 'USER_HASH' => $hash));
                    $q = "UPDATE user SET hash_admin = @USER_HASH WHERE user_id=@USER_ID";
                    DB::query($q);

                    if (isset($_POST['login']['remember']) && $row['role_id'] != 5) {
                        setcookie("cha", md5($row['user_id']), time() + 60 * 60 * 24 * 3, "/");
                        setcookie("csa", $hash, time() + 60 * 60 * 24 * 3, "/");
                    }
                    $successLogin = true;
                    $logLogin = $login;
                    Log::a('login_success', "Логин: $logLogin");
                }
            }
        }
        if (!$successLogin) {
            Log::a('login_attempt', "Логин: {$login}");
        }
        header('location: ' . (empty($_REQUEST['r']) ? '/' : $_REQUEST['r']));
    }

    public static function logout()
    {

    }
}