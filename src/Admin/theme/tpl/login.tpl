<?php

use \Simplex\Admin\Page;

//PlugJQuery::jquery();
\App\Plugins\Jquery\Jquery::fancybox();
\Simplex\Admin\Plug\Alert::init();

// GLOBAL MANDATORY STYLES
Page::coreCss('/theme/css/global/font-awesome.min.css');
Page::coreCss('/theme/css/global/simple-line-icons.min.css');
Page::coreCss('/theme/css/global/bootstrap.min.css');
Page::coreCss('/theme/css/global/uniform.default.css');

// THEME STYLES
Page::coreCss('/theme/css/conquer/style-conquer.css');
Page::coreCss('/theme/css/conquer/style.css');
Page::coreCss('/theme/css/conquer/style-responsive.css');
Page::coreCss('/theme/css/conquer/plugins.css');
Page::coreCss('/theme/css/conquer/default.css');
Page::coreCss('/theme/css/conquer/login.css');

Page::coreCss('/theme/css/default.css');
Page::coreJs('/theme/js/jquery-1.6.1.min.js');
Page::coreJs('/theme/js/default.js');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Simplex Admin 2.0</title>

        <?php \Simplex\Admin\Page::meta() ?>

    </head>


    <body class="login">

        <div class="logo">
            <a href="/admin/">
                <span>Simplex</span> Admin
            </a>
        </div>

        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form method="post" action="/admin/login/?r=<?php echo urlencode($back) ?>" class="login-form" style="display: block;">
                <h3 class="form-title">Вход в панель управления</h3>
                <div class="alert alert-danger display-hide">
                    <button data-close="alert" class="close"></button>
                    <span>
                        Enter any username and password. </span>
                </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Логин</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input type="text" name="login[login]" placeholder="Логин" autocomplete="off" class="form-control placeholder-no-fix" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Пароль</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input type="password" name="login[password]" placeholder="Пароль" autocomplete="off" class="form-control placeholder-no-fix" />
                    </div>
                </div>
                <div class="form-actions">
                    <label class="checkbox">
                        <input type="checkbox" value="1" name="login[remember]" /> Запомнить меня
                    </label>
                    <button class="btn btn-info pull-right" type="submit" name="login[submit]"> Вход </button>
                </div>
                <div class="forget-password hidden">
                    <h4>Забыли пароль?</h4>
                    <p>
                        Не беспокойтесть, нажмите <a id="forget-password" href="javascript:;">здесь</a>
                        чтобы восстановить свой пароль.
                    </p>
                </div>
            </form>
            <!-- END LOGIN FORM -->
            
            <!-- BEGIN FORGOT PASSWORD FORM -->
            <form method="post" action="index.html" class="forget-form" novalidate="novalidate" style="display: none;">
                <h3>Forget Password ?</h3>
                <p>
                    Enter your e-mail address below to reset your password.
                </p>
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input type="text" name="email" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-default" id="back-btn" type="button">
                        <i class="m-icon-swapleft"></i> Back </button>
                    <button class="btn btn-info pull-right" type="submit">
                        Submit </button>
                </div>
            </form>
            <!-- END FORGOT PASSWORD FORM -->
        </div>

        <div class="copyright">
            2015 - <?php echo date('Y') ?> &copy; Simplex Admin 2.0
        </div>

    </body>
</html>