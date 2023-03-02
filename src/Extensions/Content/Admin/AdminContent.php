<?php

namespace Simplex\Admin\Extensions\Content\Admin;

use Simplex\Admin\Base;
use Simplex\Core\Container;
use Simplex\Core\DB;
use Simplex\Core\Image;
use Simplex\Extensions\Content\Model\ModelContent;

class AdminContent extends Base
{
    public function content()
    {
        if (!Container::getRequest()->isAjax() || !isset($_FILES['file'])) {
            return parent::content();
        }
        $this->ajaxUpload();
    }

    public function ajaxUpload()
    {
        if (!Container::getRequest()->isAjax()) {
            die('');
        }

        $ext = '';
        switch ($_FILES['file']['type']) {
            case 'image/gif':
                $ext = 'gif';
                break;
            case 'image/jpeg':
                $ext = 'jpg';
                break;
            case 'image/pjpeg':
                $ext = 'jpg';
                break;
            case 'image/webp':
                $ext = 'webp';
                break;
            case 'image/png':
                $ext = 'png';
                break;
            case 'video/mp4':
                $ext = 'mp4';
                break;
            case 'video/webm':
                $ext = 'webm';
                break;
        }

        $name = md5(time()) . '.' . $ext;
        copy($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/uf/images/content/source/' . $name);

        die('/uf/images/content/source/' . $name);
    }

    protected function initTable()
    {
        parent::initTable();
        if ($_GET['action'] ?? '' == 'form') {
            return;
        }

        $this->fields['pid']->filterDataProvider = function () {
            $rows = ModelContent::findAdv()
                ->select(['content_id', 'title as label'])
                ->where(new DB\Expr('EXISTS (SELECT 1 FROM content c WHERE c.pid=content.content_id)'))
                ->orderBy('label')
                ->all('content_id');
            return [$rows];
        };
    }

    protected function tableParamsLoad()
    {
        $contentId = (int)($_REQUEST[$this->pk->name] ?? 0);
        $q = "
            SELECT param_id, param_pid, pos, '' as group_name, t1.name, t1.label, t1.params, t2.class, '$this->table' `table`, null default_value, 0 npp
            FROM struct_param t1
            LEFT JOIN struct_field t2 USING(field_id)
            WHERE table_id = $this->tableId
            UNION ALL
            SELECT ctp_id + 1000000 as param_id, param_pid, position as pos, t1.group_name as group_name,
                   t1.name, t1.label, t1.params, t2.class, '$this->table' `table`, default_value, npp
            FROM content_template_param t1
            JOIN content c USING(template_id)
            LEFT JOIN struct_field t2 USING(field_id)
            WHERE c.content_id = $contentId
            ORDER BY npp, label
        ";
        $params = DB::assoc($q, 'param_pid', 'param_id');
        return $params;
    }
}