<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use app\chem\model\ChemProductPackModel;
use app\chem\model\ChemStockOutItemModel;
use app\chem\model\ChemStockOutModel;
use cmf\controller\AdminBaseController;
use think\Db;

class AdminStockOutController extends AdminBaseController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 产品出库
     * @adminMenu(
     *     'name'   => '产品出库',
     *     'parent' => 'chem/AdminIndex/defaultStock',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品出库',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $stockOutModel = new ChemStockOutModel();
        $filter        = $this->request->param();
        $where         = [];

        $stockOutId = empty($filter['stock_out_id']) ? '' : intval($filter['stock_out_id']);
        if (!empty($stockOutId)) {
            $where['id'] = $stockOutId;
        }

        $stockOutSn = empty($filter['stock_out_sn']) ? '' : $filter['stock_out_sn'];
        if (!empty($stockOutSn)) {
            $where['stock_out_sn'] = $stockOutSn;
        }

        $stockOuts = $stockOutModel->where($where)->order('create_time DESC')->paginate();
        $stockOuts->appends($filter);

        $this->assign('stock_outs', $stockOuts);
        $this->assign('page', $stockOuts->render());
        return $this->fetch();
    }

    /**
     * 产品出库记录
     * @adminMenu(
     *     'name'   => '产品出库记录',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品出库记录',
     *     'param'  => ''
     * )
     */
    public function logs()
    {
        $chemStockOutItemModel = new ChemStockOutItemModel();

        $where = ['stock_out.status' => 0];

        $filter = $this->request->param();

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['product.cn_name'] = ['like', "%$keyword%"];
        }

        $cas = empty($filter['cas']) ? '' : $filter['cas'];
        if (!empty($cas)) {
            $where['product.cas'] = ['like', "%$cas%"];
        }

        $stockOutId = empty($filter['stock_out_id']) ? '' : intval($filter['stock_out_id']);
        if (!empty($stockOutId)) {
            $where['stock_out_item.stock_out_id'] = $stockOutId;
        }

        $stockOutSn = empty($filter['stock_out_sn']) ? '' : $filter['stock_out_sn'];
        if (!empty($stockOutSn)) {
            $where['stock_out_item.stock_out_sn'] = $stockOutSn;
        }

        $stockOutItems = $chemStockOutItemModel->field('stock_out_item.*')->alias('stock_out_item')
            ->join('__CHEM_STOCK_OUT__ stock_out', 'stock_out.id = stock_out_item.stock_out_id')
            ->join('__CHEM_PRODUCT_PACK__ pack', 'stock_out_item.pack_id = pack.id')
            ->join('__CHEM_PRODUCT__ product', 'pack.product_id = product.id')
            ->where($where)
            ->order('stock_out_item.create_time DESC')
            ->paginate();
        if (!$stockOutItems->isEmpty()) {
            $stockOutItems->load('stockOut,pack,pack.product,stockOut.user');
        }
        $stockOutItems->appends($filter);
        $this->assign('stock_out_items', $stockOutItems);
        $this->assign('page', $stockOutItems->render());
        return $this->fetch();
    }

    /**
     * 添加产品出库
     * @adminMenu(
     *     'name'   => '添加产品出库',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品出库',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加产品出库提交
     * @adminMenu(
     *     'name'   => '添加产品出库提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品出库提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'AdminStockOut');
        if ($result !== true) {
            $this->error($result);
        }

        $data['create_time']  = time();
        $data['user_id']      = cmf_get_current_admin_id();
        $data['status']       = 1;
        $data['stock_out_sn'] = cmf_get_order_sn();
        $id                   = Db::name('chem_stock_out')->insertGetId($data);

        $this->success('创建成功', url('AdminStockOut/edit') . '?id=' . $id);

    }

    /**
     * 编辑产品出库
     * @adminMenu(
     *     'name'   => '编辑产品出库',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑产品出库',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id       = $this->request->param('id', 0, 'intval');
        $noTab    = $this->request->param('no_tab', 0, 'intval');
        $stockOut = Db::name('chem_stock_out')->where('id', $id)->find();
        $this->assign('stock_out', $stockOut);

        $stockOutItems = Db::name('chem_stock_out_item')->alias('a')
            ->join('__CHEM_PRODUCT_PACK__ pack', 'a.pack_id = pack.id')
            ->join('__CHEM_PRODUCT__ product', 'pack.product_id = product.id')
            ->where('a.stock_out_id', $id)->select();

        $this->assign('stock_out_items', $stockOutItems);
        $this->assign('no_tab', $noTab);

        return $this->fetch();
    }

    /**
     * 编辑产品出库提交
     * @adminMenu(
     *     'name'   => '编辑产品出库提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑产品出库提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'AdminStockOut');
        if ($result !== true) {
            $this->error($result);
        }

        $id = intval($data['id']);

        $stockOut = Db::name('chem_stock_out')->where('id', $id)->find();

        if (empty($stockOut)) {
            $this->error('出库单不存在！');
        }

        if ($stockOut['status'] == 0) {
            $this->error('出库已经完成，无法编辑！');
        }

        $data['create_time'] = time();
        $data['user_id']     = cmf_get_current_admin_id();
        $data['status']      = 1;
        unset($data['stock_out_sn']);
        Db::name('chem_stock_out')->where('id', $id)->update($data);

        $this->success('保存成功', url('AdminStockOut/edit') . '?id=' . $id);
    }

    /**
     * 打印产品出库
     * @adminMenu(
     *     'name'   => '打印产品出库',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '打印产品出库',
     *     'param'  => ''
     * )
     */
    public function printStockOut()
    {
        $id            = $this->request->param('id', 0, 'intval');
        $stockOutModel = new ChemStockOutModel();
        $stockOut      = $stockOutModel->where('id', $id)->find();
        $this->assign('stock_out', $stockOut);

        $stockOutItems = Db::name('chem_stock_out_item')->alias('a')
            ->join('__CHEM_PRODUCT_PACK__ pack', 'a.pack_id = pack.id')
            ->join('__CHEM_PRODUCT__ product', 'pack.product_id = product.id')
            ->where('a.stock_out_id', $id)->select();

        $this->assign('stock_out_items', $stockOutItems);

        return $this->fetch('print_stock_out');
    }

    /**
     * 产品出库确认
     * @adminMenu(
     *     'name'   => '产品出库确认',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品出库确认',
     *     'param'  => ''
     * )
     */
    public function confirm()
    {
        if ($this->request->isPost()) {
            $id            = $this->request->param('id', 0, 'intval');
            $stockOutModel = new ChemStockOutModel();
            $stockOutModel->save(['status' => 0], ['id' => $id]);

            $this->success('确认成功！');
        }
    }

    /**
     * 产品出库选择产品包装
     * @adminMenu(
     *     'name'   => '产品出库选择产品包装',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品出库选择产品包装',
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
     * 添加产品到出库
     * @adminMenu(
     *     'name'   => '添加产品到出库',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品到出库',
     *     'param'  => ''
     * )
     */
    public function addToStockOut()
    {
        $packId   = $this->request->param('pack_id', 0, 'intval');
        $quantity = $this->request->param('quantity', 0, 'intval');

        $chemProductPackModel = new ChemProductPackModel();

        $stockOutId = $this->request->param('stock_out_id', 0, 'intval');

        $stockOutModel = new ChemStockOutModel();

        $stockOut = $stockOutModel->where('id', $stockOutId)->find();

        if (empty($stockOut)) {
            $this->error('出库单不存在！');
        }

        if ($stockOut['status'] == 0) {
            $this->error('出库已经完成，无法添加产品！');
        }

        $pack = $chemProductPackModel->where('id', $packId)->find();

        if (empty($pack)) {
            $this->error('产品规格不存在！');
        }

        if ($pack['inventory'] < $quantity) {
            $this->error('库存不足！');
        }

        $findStockOutItem = Db::name('chem_stock_out_item')->where([
            'pack_id'      => $packId,
            'stock_out_id' => $stockOutId
        ])->find();


        if (empty($findStockOutItem)) {
            Db::name('chem_stock_out_item')->insert([
                'pack_id'      => $packId,
                'quantity'     => $quantity,
                'stock_out_id' => $stockOutId,
                'stock_out_sn' => $stockOut['stock_out_sn'],
                'create_time'  => time()
            ]);
        } else {
            Db::name('chem_stock_out_item')->where([
                'pack_id'      => $packId,
                'stock_out_id' => $stockOutId
            ])->update([
                'quantity' => $quantity,
            ]);
        }

        $this->success('添加成功！');

    }

}