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
use app\chem\model\ChemProductPackModel;
use app\chem\service\ProductService;
use cmf\controller\HomeBaseController;
use think\Db;

class ProductController extends HomeBaseController
{

    public function index()
    {
        $chemCategoryModel = new ChemCategoryModel();
        $productService    = new ProductService();

        $productId  = $this->request->param('id', 0, 'intval');
        $categoryId = $this->request->param('cid', 0, 'intval');
        $product    = $productService->publishedProduct($productId, $categoryId);

        if (empty($productId)) {
            abort(404, '产品不存在!');
        }

        //TODO 上一篇,下一篇

        $tplName = 'product';

        if (!empty($categoryId)) {

            $category = $chemCategoryModel->where('id', $categoryId)->where('status', 1)->find();

            if (empty($category)) {
                abort(404, '产品不存在!');
            }

            $this->assign('category', $category);

            $tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
        }

        Db::name('chem_product')->where(['id' => $productId])->setInc('hits');

        $chemProductPackModel = new ChemProductPackModel();

        $packs = $chemProductPackModel->where(['product_id' => $productId, 'status' => 1])->select();

        $product['content'] = cmf_replace_content_file_url(htmlspecialchars_decode($product['content']));

        $this->assign('product', $product);

        $this->assign('packs', $packs);

        $tplName = empty($product['more']['template']) ? $tplName : $product['more']['template'];

        return $this->fetch("/$tplName");
    }



}
