<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use app\chem\model\ChemWarehouseModel;
use cmf\controller\AdminBaseController;

class AdminWarehouseController extends AdminBaseController
{

    /**
     * 库房列表
     * @adminMenu(
     *     'name'   => '库房管理',
     *     'parent' => 'chem/AdminIndex/defaultStock',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '库房管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $chemWarehouseModel = new ChemWarehouseModel();

        $warehouses = $chemWarehouseModel->select();

        $this->assign('warehouses', $warehouses);

        return $this->fetch();
    }

    /**
     * 添加库房
     * @adminMenu(
     *     'name'   => '添加库房',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加库房',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加库房提交保存
     * @adminMenu(
     *     'name'   => '添加库房提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加库房提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'AdminWarehouse');
        if ($result === false) {
            $this->error($result);
        }

        $chemWarehouseModel = new ChemWarehouseModel();
        $chemWarehouseModel->allowField(true)->save($data);

        $this->success("添加成功！", url("AdminWarehouse/index"));
    }

    /**
     * 编辑库房
     * @adminMenu(
     *     'name'   => '编辑库房',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑库房',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id                 = $this->request->param('id', 0, 'intval');
        $chemWarehouseModel = ChemWarehouseModel::get($id);
        $this->assign('data', $chemWarehouseModel);
        return $this->fetch();
    }

    /**
     * 编辑库房提交保存
     * @adminMenu(
     *     'name'   => '编辑库房提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑库房提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminWarehouse');
        if ($result === false) {
            $this->error($result);
        }

        $chemWarehouseModel = new ChemWarehouseModel();
        $chemWarehouseModel->allowField(true)->isUpdate(true)->save($data);

        $this->success("保存成功！", url("AdminWarehouse/index"));
    }

    /**
     * 删除库房
     * @adminMenu(
     *     'name'   => '删除库房',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除库房',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        ChemWarehouseModel::destroy($id);

        $this->success("删除成功！", url("AdminWarehouse/index"));
    }


}