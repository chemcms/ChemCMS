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
use app\chem\model\ChemCategoryModel;
use app\admin\model\ThemeModel;
use think\Db;

class AdminCategoryController extends AdminBaseController
{
    /**
     * 产品分类列表
     * @adminMenu(
     *     'name'   => '分类管理',
     *     'parent' => 'chem/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品分类列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $chemCategoryModel = new ChemCategoryModel();
        $categoryTree      = $chemCategoryModel->adminCategoryTableTree();

        $this->assign('category_tree', $categoryTree);
        return $this->fetch();
    }


    /**
     * 添加产品分类
     * @adminMenu(
     *     'name'   => '添加产品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品分类',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $parentId          = $this->request->param('parent', 0, 'intval');
        $chemCategoryModel = new ChemCategoryModel();
        $categoriesTree    = $chemCategoryModel->adminCategoryTree($parentId);

        $themeModel        = new ThemeModel();
        $listThemeFiles    = $themeModel->getActionThemeFiles('chem/List/index');
        $productThemeFiles = $themeModel->getActionThemeFiles('chem/Product/index');

        $this->assign('list_theme_files', $listThemeFiles);
        $this->assign('product_theme_files', $productThemeFiles);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }

    /**
     * 添加产品分类提交保存
     * @adminMenu(
     *     'name'   => '添加产品分类提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品分类提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $chemCategoryModel = new ChemCategoryModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'AdminCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $chemCategoryModel->addCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('AdminCategory/index'));
    }

    /**
     * 编辑产品分类
     * @adminMenu(
     *     'name'   => '编辑产品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑产品分类',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $category = ChemCategoryModel::get($id)->toArray();
            $this->assign($category);

            $chemCategoryModel = new ChemCategoryModel();
            $categoriesTree    = $chemCategoryModel->adminCategoryTree($category['parent_id'], $id);

            $themeModel        = new ThemeModel();
            $listThemeFiles    = $themeModel->getActionThemeFiles('chem/List/index');
            $productThemeFiles = $themeModel->getActionThemeFiles('chem/Product/index');

            $this->assign('list_theme_files', $listThemeFiles);
            $this->assign('product_theme_files', $productThemeFiles);
            $this->assign('categories_tree', $categoriesTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑产品分类提交
     * @adminMenu(
     *     'name'   => '编辑产品分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑产品分类提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $chemCategoryModel = new ChemCategoryModel();

        $result = $chemCategoryModel->editCategory($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /** 文章分类选择对话框
     * @adminMenu(
     *     'name'   => '文章分类选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章分类选择对话框',
     *     'param'  => ''
     * )
     */
    public function select()
    {
        $ids               = $this->request->param('ids');
        $selectedIds       = explode(',', $ids);
        $chemCategoryModel = new ChemCategoryModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
                               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
    <td>\$description</td>
</tr>
tpl;

        $categoryTree = $chemCategoryModel->adminCategoryTableTree($selectedIds, $tpl);

        $where      = ['delete_time' => 0];
        $categories = $chemCategoryModel->where($where)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 产品分类排序
     * @adminMenu(
     *     'name'   => '产品分类排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品分类排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $chemCategoryModel = new  ChemCategoryModel();
        parent::listOrders($chemCategoryModel);
        $this->success("排序更新成功！");
    }

    /**
     * 删除产品分类
     * @adminMenu(
     *     'name'   => '删除产品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除产品分类',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $chemCategoryModel = new ChemCategoryModel();
        $id                = $this->request->param('id');

        $count = $chemCategoryModel->where(["parent_id" => $id, 'delete_time' => 0])->count();
        if ($count > 0) {
            $this->error("该分类下还有子类，无法删除！");
        }

        //获取删除的内容
        $res    = $chemCategoryModel->where('id', $id)->find();
        $data   = [
            'object_id'   => $res['id'],
            'create_time' => time(),
            'table_name'  => 'chem_category',
            'name'        => $res['name']
        ];
        $result = $chemCategoryModel
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