<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use cmf\controller\AdminBaseController;
use app\chem\model\ChemBrandModel;
use app\admin\model\ThemeModel;
use think\Db;

class AdminBrandController extends AdminBaseController
{
    /**
     * 品牌列表
     * @adminMenu(
     *     'name'   => '品牌管理',
     *     'parent' => 'chem/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $chemBrandModel = new ChemBrandModel();
        $brands         = $chemBrandModel->select();

        $this->assign('brands', $brands);
        return $this->fetch();
    }


    /**
     * 添加品牌
     * @adminMenu(
     *     'name'   => '添加品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加品牌',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加品牌提交保存
     * @adminMenu(
     *     'name'   => '添加品牌提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加品牌提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $chemBrandModel = new ChemBrandModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'AdminBrand');

        if ($result !== true) {
            $this->error($result);
        }

        $chemBrandModel->allowField(true)->save($data);

        $this->success('添加成功!', url('AdminBrand/index'));
    }

    /**
     * 编辑品牌
     * @adminMenu(
     *     'name'   => '编辑品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑品牌',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $category = ChemBrandModel::get($id)->toArray();
            $this->assign($category);

            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑品牌提交
     * @adminMenu(
     *     'name'   => '编辑品牌提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑品牌提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminBrand');

        if ($result !== true) {
            $this->error($result);
        }

        $chemBrandModel = new ChemBrandModel();

        $chemBrandModel->isUpdate(true)->allowField(true)->save($data);


        $this->success('保存成功!');
    }

    /** 品牌选择对话框
     * @adminMenu(
     *     'name'   => '品牌选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌选择对话框',
     *     'param'  => ''
     * )
     */
//    public function select()
//    {
//        $ids            = $this->request->param('ids');
//        $selectedIds    = explode(',', $ids);
//        $chemBrandModel = new ChemBrandModel();
//
//        $tpl = <<<tpl
//<tr class='data-item-tr'>
//    <td>
//        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
//                               value='\$id' data-name='\$name' \$checked>
//    </td>
//    <td>\$id</td>
//    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
//    <td>\$description</td>
//</tr>
//tpl;
//
//        $categoryTree = $chemBrandModel->adminCategoryTableTree($selectedIds, $tpl);
//
//        $where      = ['delete_time' => 0];
//        $categories = $chemBrandModel->where($where)->select();
//
//        $this->assign('categories', $categories);
//        $this->assign('selectedIds', $selectedIds);
//        $this->assign('categories_tree', $categoryTree);
//        return $this->fetch();
//    }

    /**
     * 品牌排序
     * @adminMenu(
     *     'name'   => '品牌排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $chemBrandModel = new  ChemBrandModel();
        parent::listOrders($chemBrandModel);
        $this->success("排序更新成功！");
    }

    /**
     * 删除品牌
     * @adminMenu(
     *     'name'   => '删除品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除品牌',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $chemBrandModel = new ChemBrandModel();
        $id             = $this->request->param('id');

        $count = $chemBrandModel->where(["parent_id" => $id, 'delete_time' => 0])->count();
        if ($count > 0) {
            $this->error("该分类下还有子类，无法删除！");
        }

        //获取删除的内容
        $res    = $chemBrandModel->where('id', $id)->find();
        $data   = [
            'object_id'   => $res['id'],
            'create_time' => time(),
            'table_name'  => 'chem_category',
            'name'        => $res['name']
        ];
        $result = $chemBrandModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }

}