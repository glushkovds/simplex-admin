<?php

namespace Simplex\Admin\Modules\Install;

use Simplex\Admin\InstallBase;
use Simplex\Admin\Plug\Alert;
use Simplex\Core\ModuleBase;

/**
 * Class Install
 * @package Simplex\Admin\Modules\Install
 *
 * WIP. requires not implemented plugins
 *
 */
class Install extends ModuleBase {

    private $installDir = '';

    protected function content() {
        Alert::output();
        if (!empty($_GET['installdir'])) {
            $this->installDir = urldecode($_GET['installdir']);
        }
        if (is_uploaded_file(@$_FILES['file']['tmp_name'])) {
            $this->installDir = $this->fromZip($_FILES['file']);
        }
        if ($this->installDir) {
            return $this->install();
        }
        include 'tpl/load.form.tpl';
    }

    protected function install() {
        $installer = $this->getInstaller();
        if ($installer) {
            if ($installer->destDir) {
                if (isset($_GET['confirm'])) {
                    $success = $installer->install();
                    if ($success) {
                        $text = $installer->type == InstallBase::TYPE_EXT ? "Расширение $installer->destDir успешно установлено" : "Плагин $installer->destDir успешно установлен";
                        PlugFS::rmDir($this->installDir);
                        Alert::success($text, './');
                    } else {
                        Alert::error('Ошибка! Установка не удалась в процессе установки', './');
                    }
                }
                include 'tpl/confirm.tpl';
                return;
            } else {
                Alert::error('Ошибка! В инсталляторе не указан адрес, куда устанавливать ($destDir)');
            }
        } else {
            Alert::error('Ошибка! Не удалось найти инсталлятор');
        }
        PlugFS::rmDir($this->installDir);
        header("location: ./");
        exit;
    }

    /**
     * 
     * @return \SFAdminInstallBase|boolean
     */
    protected function getInstaller() {
        $file = 'sfinstall.php';
        $path = '';
        if (is_file($ret = "$this->installDir/$file")) {
            $path = $ret;
        } else {
            foreach (scandir($this->installDir) as $dir) {
                if (is_dir($dir0 = "$this->installDir/$dir")) {
                    if (is_file($ret = "$dir0/$file")) {
                        $path = $ret;
                        break;
                    } else {
                        foreach (scandir($dir0) as $dir1) {
                            if (is_dir($dir0 = "$this->installDir/$dir/$dir1")) {
                                if (is_file($ret = "$dir0/$file")) {
                                    $path = $ret;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($path) {
            include $path;
            return new SFInstall(dirname($path));
        }

        return false;
    }

    protected function getTmpDir() {
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/uf/install';
        $success = true;
        if (!is_dir($dir)) {
            $success = mkdir($dir);
        }
        if (!$success) {
            Alert::error('Ошибка! Невозможно создать root-папку ' . $dir, './');
        }
        return $success ? $dir : false;
    }

    protected function fromZip($uploadedFile) {
        $root = $this->getTmpDir();
        $dir = $root . '/' . time();
        if (!mkdir($dir)) {
            Alert::error('Ошибка! Невозможно создать папку установщика ' . $dir, './');
        }
        $zipFile = $dir . '/' . $uploadedFile['name'];
        if (move_uploaded_file($uploadedFile['tmp_name'], $zipFile)) {
            if (PlugZip::unzip($zipFile)) {
                return $dir;
            } else {
                Alert::error('Ошибка! Невозможно распаковать архив ' . $zipFile, './');
            }
        } else {
            Alert::error('Ошибка! Невозможно записать архив установщика в папку ' . $dir, './');
        }
    }

}
