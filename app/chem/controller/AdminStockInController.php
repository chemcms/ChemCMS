<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use app\chem\model\ChemProductInventoryLogModel;
use app\chem\model\ChemProductPackModel;
use app\chem\model\ChemProductModel;
use app\chem\model\ChemCategoryModel;
use app\chem\service\ProductService;
use app\chem\model\ChemStoragePlaceModel;
use cmf\controller\AdminBaseController;
use think\Db;

class AdminStockInController extends AdminBaseController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 产品入库
     * @adminMenu(
     *     'name'   => '产品入库',
     *     'parent' => 'chem/AdminIndex/defaultStock',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品入库',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $param = $this->request->param();

        $categoryId = $this->request->param('category', 0, 'intval');

        $productService = new ProductService();
        $data           = $productService->adminProductList($param);

        $chemCategoryModel = new ChemCategoryModel();
        $categoryTree      = $chemCategoryModel->adminCategoryTree($categoryId);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('products', $data->items());
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());

        return $this->fetch();
    }

    /**
     * 产品入库记录
     * @adminMenu(
     *     'name'   => '产品入库记录',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品入库记录',
     *     'param'  => ''
     * )
     */
    public function logs(){
        $chemProductInventoryLogModel = new ChemProductInventoryLogModel();

        $logs = $chemProductInventoryLogModel->where(['is_in' => 1])->order('create_time DESC')->paginate();

        $this->assign('logs', $logs);
        $this->assign('page', $logs->render());
        return $this->fetch();
    }

    /**
     * 添加产品入库
     * @adminMenu(
     *     'name'   => '添加产品入库',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品入库',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 编辑产品入库
     * @adminMenu(
     *     'name'   => '编辑产品入库',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑产品入库',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $packages = Db::name('chem_product_pack')->where(['product_id' => $id])->order('id ASC')->select();

        $chemProductModel   = new ChemProductModel();
        $product            = $chemProductModel->where('id', $id)->find();
        $productCategories  = $product->categories()->alias('a')->column('a.name', 'a.id');
        $productCategoryIds = implode(',', array_keys($productCategories));

        $product['content'] = cmf_replace_content_file_url(htmlspecialchars_decode($product['content']));

        $chemStoragePlaceModel = new ChemStoragePlaceModel();
        $storagePlaces         = $chemStoragePlaceModel->select();
        $this->assign('product', $product);
        $this->assign('packages', $packages);
        $this->assign('storage_places', $storagePlaces);
        $this->assign('product_categories', $productCategories);
        $this->assign('product_category_ids', $productCategoryIds);

        return $this->fetch();
    }

    /**
     * 产品入库选择产品包装
     * @adminMenu(
     *     'name'   => '产品入库选择产品包装',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品入库选择产品包装',
     *     'param'  => ''
     * )
     */
    public function selectPack()
    {
        $stockOutId           = $this->request->param('stock_out_id', 0, 'intval');
        $filter               = $this->request->param();
        $chemProductPackModel = new ChemProductPackModel();

        $where = [
            'product.create_time' => ['>=', 0],
            'product.delete_time' => ['eq', 0]
        ];

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['product.name'] = ['like', "%$keyword%"];
        }

        $cas = empty($filter['cas']) ? '' : $filter['cas'];
        if (!empty($cas)) {
            $where['product.cas'] = ['like', "%$cas%"];
        }

        $packs = $chemProductPackModel->alias('pack')
            ->field('pack.*')
            ->join('__CHEM_PRODUCT__ product', 'pack.product_id = product.id')
            ->where($where)
            ->paginate();

        $packs->appends($filter);

        $this->assign('packs', $packs);
        $this->assign('page', $packs->render());
        $this->assign('stock_out_id', $stockOutId);
        return $this->fetch('select_pack');
    }

    /**
     * 产品入库产品库存更改
     * @adminMenu(
     *     'name'   => '产品入库产品库存更改',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品入库产品库存更改',
     *     'param'  => ''
     * )
     */
    public function inventoryChangePost()
    {

        if ($this->request->isPost()) {

            $isIn            = 1;
            $packId          = $this->request->param('pack_id', 0, 'intval');
            $inventoryChange = $this->request->param('inventory_change', 0, 'intval');

            $chemProductPackModel = new ChemProductPackModel();

            if ($isIn) {
                $isIn = 1;
                $chemProductPackModel->where('id', $packId)->setInc('inventory', $inventoryChange);
            }

            Db::name('chem_product_inventory_log')->insert([
                'pack_id'          => $packId,
                'is_in'            => $isIn,
                'inventory_change' => $inventoryChange,
                'user_id'          => cmf_get_current_admin_id(),
                'create_time'      => time(),
                'category_name'    => '',
                'remark'           => $this->request->param('remark', '')
            ]);

            $this->success('操作成功!');

        }

    }


}