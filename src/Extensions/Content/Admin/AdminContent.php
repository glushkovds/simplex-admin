<?php

namespace Simplex\Admin\Extensions\Content\Admin;

use Simplex\Admin\Base;
use Simplex\Core\DB;
use Simplex\Extensions\Content\Model\ModelContent;

class AdminContent extends Base
{
    protected function tableParamsLoad()
    {
        $contentId = (int)($_REQUEST[$this->pk->name] ?? 0);
        $q = "
            SELECT param_id, param_pid, pos, t1.name, t1.label, t1.params, t2.class, '$this->table' `table`, null default_value, 0 npp
            FROM struct_param t1
            LEFT JOIN struct_field t2 USING(field_id)
            WHERE table_id = $this->tableId
            UNION ALL
            SELECT ctp_id + 1000000 as param_id, param_pid, position as pos,
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

    protected function initTable()
    {
        parent::initTable();
        $this->fields['pid']->filterDataProvider = function () {
            $rows = ModelContent::findAdv()
                ->select(['content_id', 'title as label'])
                ->where(new DB\Expr('EXISTS (SELECT 1 FROM content c WHERE c.pid=content.content_id)'))
                ->orderBy('label')
                ->all('content_id');
            return [$rows];
        };
    }
}