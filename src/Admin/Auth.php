<?php


namespace Simplex\Admin;


use Simplex\Admin\Plugins\Log\Log;

;

use Simplex\Auth\Bootstrap;
use Simplex\Auth\CookieTokenBag;
use Simplex\Auth\Models\UserAuth;
use Simplex\Auth\SessionStorage;
use Simplex\Core\DB;
use Simplex\Core\Models\User;

class Auth
{

    const REMEMBER_ME_INTERVAL = '1 WEEK';
    const LOGIN_PRIV = 'simplex_admin';

    public static function login($login, $password, $isRemember, $redirect)
    {
        if (empty($login) || empty($password)) {
            return;
        }
        $successLogin = false;
        if (strpos($redirect, '//') !== false) {
            $redirect = '/';
        }
        if (preg_match('@^[0-9a-z\@\-\.]+$@i', $login)) {
            $q = "SELECT u.*
                    FROM user u
                    JOIN user_role r ON r.role_id=u.role_id
                    WHERE login=:login
                      AND u.active=1
                      AND r.active=1";
            $row = DB::result($q, '', ['login' => strtolower($login)]);
            if ($row && md5($password) === $row['password']) {
                $user = (new User())->fill($row);
                if ($user->ican(static::LOGIN_PRIV)) {
                    SessionStorage::set($row['user_id']);
                    Bootstrap::authByUser($user);
                    if ($isRemember) {
                        $auth = UserAuth::create($row['user_id'], static::REMEMBER_ME_INTERVAL);
                        $cookies = new CookieTokenBag(CookieTokenBag::defaultPrefix());
                        $cookies->set($auth->token, new \DateTime(static::REMEMBER_ME_INTERVAL));
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
        header("Location: $redirect");
        exit;
    }

    public static function logout()
    {
        $redirect = $_REQUEST['r'] ?? '/admin/';
        if (strpos($redirect, '//') !== false) {
            $redirect = '/';
        }
        Bootstrap::signOut();
        header("Location: $redirect");
        exit;
    }
}