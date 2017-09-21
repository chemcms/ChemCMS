<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use app\chem\model\ChemStoragePlaceModel;
use app\chem\model\ChemWarehouseModel;
use cmf\controller\AdminBaseController;

class AdminStoragePlaceController extends AdminBaseController
{

    /**
     * 货架列表
     * @adminMenu(
     *     'name'   => '货架管理',
     *     'parent' => 'chem/AdminIndex/defaultStock',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '货架管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $chemStoragePlaceModel = new ChemStoragePlaceModel();

        $storagePlaces = $chemStoragePlaceModel->select();

        if (!$storagePlaces->isEmpty()) {
            $storagePlaces->load('warehouse');
        }

        $this->assign('storage_places', $storagePlaces);

        return $this->fetch();
    }

    /**
     * 添加货架
     * @adminMenu(
     *     'name'   => '添加货架',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加货架',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $chemWarehouseModel = new ChemWarehouseModel();
        $warehouses         = $chemWarehouseModel->select();
        $this->assign('warehouses', $warehouses);
        return $this->fetch();
    }

    /**
     * 添加货架提交保存
     * @adminMenu(
     *     'name'   => '添加货架提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加货架提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'AdminStoragePlace');
        if ($result === false) {
            $this->error($result);
        }

        $chemStoragePlaceModel = new ChemStoragePlaceModel();
        $chemStoragePlaceModel->allowField(true)->save($data);

        $this->success("添加成功！", url("AdminStoragePlace/index"));
    }

    /**
     * 编辑货架
     * @adminMenu(
     *     'name'   => '编辑货架',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑货架',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id                    = $this->request->param('id', 0, 'intval');
        $chemStoragePlaceModel = ChemStoragePlaceModel::get($id);
        $chemWarehouseModel    = new ChemWarehouseModel();
        $warehouses            = $chemWarehouseModel->select();
        $this->assign('warehouses', $warehouses);
        $this->assign('data', $chemStoragePlaceModel);
        return $this->fetch();
    }

    /**
     * 编辑货架提交保存
     * @adminMenu(
     *     'name'   => '编辑货架提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑货架提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminStoragePlace');
        if ($result === false) {
            $this->error($result);
        }

        $chemStoragePlaceModel = new ChemStoragePlaceModel();
        $chemStoragePlaceModel->allowField(true)->isUpdate(true)->save($data);

        $this->success("保存成功！", url("AdminStoragePlace/index"));
    }

    /**
     * 删除货架
     * @adminMenu(
     *     'name'   => '删除货架',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除货架',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        ChemStoragePlaceModel::destroy($id);

        $this->success("删除成功！", url("AdminStoragePlace/index"));
    }


}