<?php

namespace Simplex\Admin;


use Simplex\Core\Container;
use Simplex\Core\DB;
use Simplex\Core\Html\HtmlRequest;
use Simplex\Core\Html\HtmlResponse;

class Core
{

    public static $ajax = '';
    public static $isAjax = false;
    private static $uri = array();
    private static $path = '';
    private static $menu_tree = array();
    private static $menu_by_id = array();
    private static $menu_by_link = array();
    private static $menu_cur = false;
    private static $crumbs = array();
    private static $site_params = false;

    private function __construct()
    {

    }

    /**
     * @return string like /vendor/glushkovds/simplex-admin/src/Admin
     */
    public static function webVendorPath()
    {
        return str_replace(SF_ROOT_PATH, '', __DIR__);
    }

    public static function vendorPath()
    {
        return __DIR__;
    }

    public static function init()
    {

        if (!Container::isSet('request')) {
            Container::set('request', new HtmlRequest());
        }

        if (!Container::isSet('response')) {
            Container::set('response', new HtmlResponse());
        }

        $url_info = parse_url($_SERVER['REQUEST_URI']);
        self::$path = $url_info['path'];
        self::$uri = array_slice(explode('/', self::$path), 1);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            self::$ajax = true;
            self::$isAjax = true;
        }

        if (static::uri(1) == 'login') {
            Auth::login(
                $_REQUEST['login']['login'] ?? null,
                $_REQUEST['login']['password'] ?? null,
                isset($_POST['login']['remember']),
                $_REQUEST['r'] ?? '/admin/'
            );
        } elseif (static::uri(1) == 'logout') {
            Auth::logout();
        }

        if (!Container::getUser()) {
            return;
        }

        $privIds = Container::getUser()->getPrivileges()['ids'] ?? [];
        $q = "
            SELECT menu_id, menu_pid, link, name, model, icon, hidden
            FROM admin_menu
            WHERE priv_id IN(" . join(',', $privIds) . ")
            ORDER BY npp, menu_id
        ";
        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            self::$menu_tree[(int)$row['menu_pid']][(int)$row['menu_id']] = $row;
            self::$menu_by_id[(int)$row['menu_id']] = $row;
            self::$menu_by_link[md5($row['link'])] = $row;
            if ($row['link'] == self::$path) {
                self::$menu_cur = $row;
            }
        }

        if (!self::$isAjax) {
            if (self::$menu_cur) {
                self::crumbsSet(self::$menu_cur);
            }
        }

        $q = "SELECT alias, value FROM settings";
        $rows = DB::assoc($q);
        foreach ($rows as $row) {
            self::$site_params[$row['alias']] = $row['value'];
        }


        DB::bind(array('SITE_PATH' => self::$path));
    }

    public static function ajax()
    {
        return self::$ajax;
    }

    public static function uri($i = 0)
    {
        return isset(self::$uri[$i]) ? self::$uri[$i] : '';
    }

    public static function menu()
    {
        return self::$menu_tree;
    }

    public static function menuCurItem($field = false)
    {
        return $field ? @self::$menu_cur[$field] : self::$menu_cur;
    }

    private static function crumbsSet($item = array())
    {
        if (isset(self::$menu_by_id[(int)$item['menu_pid']])) {
            self::crumbsSet(self::$menu_by_id[(int)$item['menu_pid']]);
        }
        self::$crumbs[] = array('link' => $item['link'], 'name' => $item['name']);
    }

    public static function crumbs()
    {
        return self::$crumbs;
    }

    public static function path()
    {
        return self::$path;
    }

    public static function execute()
    {
        if (Container::getUser()) {
            if (self::ajax()) {
                Page::content();
            } else {
                include 'theme/tpl/index.tpl';
            }
        } else {
            $back = $_SERVER['REQUEST_URI'];
            if (strpos($back, 'logout') !== false) {
                $back = '/admin/';
            }
            include 'theme/tpl/login.tpl';
        }
    }

    public static function siteParam($key)
    {
        if (!isset(self::$site_params[$key])) {
            self::$site_params[$key] = '';
            $q = "INSERT INTO settings(name, alias, value) VALUES('Новый параметр')";
        }
        return isset(self::$site_params[$key]) ? self::$site_params[$key] : '';
    }

}
