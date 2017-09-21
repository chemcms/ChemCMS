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
use cmf\controller\AdminBaseController;
use think\Db;

class AdminPackController extends AdminBaseController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 产品检索
     * @adminMenu(
     *     'name'   => '产品检索',
     *     'parent' => 'chem/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品检索',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $filter = $this->request->param();

        $where   = [];
        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['product.cn_name'] = ['like', "%$keyword%"];
        }

        $cas = empty($filter['cas']) ? '' : $filter['cas'];
        if (!empty($cas)) {
            $where['product.cas'] = ['like', "%$cas%"];
        }

        $productNo = empty($filter['product_no']) ? '' : $filter['product_no'];
        if (!empty($productNo)) {
            $where['pack.product_no'] = ['like', "%$productNo%"];
        }

        if (empty($where)) {
            $packs = [];
        } else {
            $chemProductPackModel = new ChemProductPackModel();
            $packs                = $chemProductPackModel->alias('pack')
                ->field('pack.*,product.cas,product.thumbnail,product.mol_formula')
                ->join('__CHEM_PRODUCT__ product', 'pack.product_id = product.id')
                ->where($where)
                ->paginate();
            $packs->appends($filter);
            $this->assign('page', $packs->render());
        }

        $this->assign('packs', $packs);

        return $this->fetch();
    }

    /**
     * 产品包装提交保存
     * @adminMenu(
     *     'name'   => '产品包装提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品包装提交保存',
     *     'param'  => ''
     * )
     */
    public function savePost()
    {
        if ($this->request->isPost()) {

            $data   = $this->request->param();
            $result = $this->validate($data, 'AdminPack');
            if ($result !== true) {
                $this->error($result);
            }

            if (empty($data['id'])) {
                $chemProductPackModel = new ChemProductPackModel();
                $chemProductPackModel->allowField(true)->save($data);
                $this->success('保存成功！', '', ['id' => $chemProductPackModel->id]);
            } else {
                $id                   = intval($data['id']);
                $chemProductPackModel = new ChemProductPackModel();
                $findProductPack      = $chemProductPackModel->where('id', $id)->find();

                if (empty($findProductPack)) {
                    $this->error('产品规格不存在！');
                }

                $inventoryChange = $data['inventory'] - $findProductPack['inventory'];

                if ($inventoryChange < 0) {
                    $this->error('只能增加产品包装,不能减小库存！');
                }

                if ($inventoryChange > 0) {
                    Db::name('chem_product_inventory_log')->insert([
                        'pack_id'          => $data['id'],
                        'is_in'            => 1,
                        'inventory_change' => $inventoryChange,
                        'user_id'          => cmf_get_current_admin_id(),
                        'create_time'      => time(),
                        'category_name'    => '入库',
                        'remark'           => '产品入库'
                    ]);
                }

                $chemProductPackModel->allowField(true)->save($data, ['id' => $id]);
                $this->success('保存成功！', '', ['id' => $id]);
            }
        }
    }

    /**
     * 产品包装删除
     * @adminMenu(
     *     'name'   => '产品包装删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品包装删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param('id', 0, 'intval');

            $chemProductPackModel = new ChemProductPackModel();

            $chemProductPackModel->where('id', $id)->delete();

            $this->success("删除成功！");
        }
    }

    /**
     * 产品包装库存更新
     * @adminMenu(
     *     'name'   => '产品包装库存更新',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品包装库存更新',
     *     'param'  => ''
     * )
     */
    public function inventoryChange()
    {

        $packId = $this->request->param('pack_id', 0, 'intval');
        $this->assign('pack_id', $packId);
        return $this->fetch('inventory_change');

    }

    /**
     * 产品包装库存更新提交保存
     * @adminMenu(
     *     'name'   => '产品包装库存更新提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品包装库存更新提交保存',
     *     'param'  => ''
     * )
     */
    public function inventoryChangePost()
    {

        if ($this->request->isPost()) {

            $isIn            = $this->request->param('is_in', 0, 'intval');
            $packId          = $this->request->param('pack_id', 0, 'intval');
            $inventoryChange = $this->request->param('inventory_change', 0, 'intval');

            $chemProductPackModel = new ChemProductPackModel();

            if ($isIn) {
                $isIn = 1;
                $chemProductPackModel->where('id', $packId)->setInc('inventory', $inventoryChange);
            } else {
                $oldInventory = $chemProductPackModel->where('id', $packId)->value('inventory');
                $isIn         = 0;
                if ($oldInventory >= $inventoryChange) {
                    $chemProductPackModel->where('id', $packId)->setDec('inventory', $inventoryChange);
                } else {
                    $this->error('库存不足！');
                }
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