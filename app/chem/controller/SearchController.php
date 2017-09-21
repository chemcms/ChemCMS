<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class SearchController extends HomeBaseController
{

    public function index()
    {
        $keyword = $this->request->param('keyword');
        if (empty($keyword)) {

            $this->error('请输入关键字');

        } else {
            $where                                 = ["status" => 1];
            $where['cas|cn_name|mol_formula|name'] = ['like', "%$keyword%"];
            $products                              = Db::name('chem_product')
                ->where($where)
                ->paginate(20);

            $products->appends('keyword', $keyword);
        }

        $this->assign('page', $products->render());
        $this->assign("keyword", $keyword);
        $this->assign("count", $products->total());
        $this->assign("products", $products);
        return $this->fetch("/search");
    }
}