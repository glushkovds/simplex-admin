<?php

namespace Simplex\Admin\Modules\Menu;

use Simplex\Core\Container;
use Simplex\Core\ModuleBase;

class Menu extends ModuleBase
{

    protected $name = 'menu';

    public function content()
    {
        $menu = Container::getCore()::menu();
        include dirname(__FILE__) . '/tpl/menu.tpl';
    }

}
