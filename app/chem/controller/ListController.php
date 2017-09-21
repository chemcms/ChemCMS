<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use app\chem\model\ChemCategoryModel;
use cmf\controller\HomeBaseController;

class ListController extends HomeBaseController
{
    public function index()
    {
        $id                = $this->request->param('id', 0, 'intval');
        $chemCategoryModel = new ChemCategoryModel();

        $category = $chemCategoryModel->where('id', $id)->where('status', 1)->find();

        $this->assign('category', $category);

        $listTpl = empty($category['list_tpl']) ? 'list' : $category['list_tpl'];

        return $this->fetch('/' . $listTpl);
    }

}
