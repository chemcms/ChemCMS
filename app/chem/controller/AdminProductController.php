<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\controller;

use app\admin\model\ThemeModel;
use app\chem\logic\ChemProductLogic;
use app\chem\model\ChemCategoryModel;
use app\chem\model\ChemProductModel;
use app\chem\model\ChemStoragePlaceModel;
use app\chem\service\ProductService;
use cmf\controller\AdminBaseController;
use think\Db;

class AdminProductController extends AdminBaseController
{

    /**
     * 产品列表
     * @adminMenu(
     *     'name'   => '产品管理',
     *     'parent' => 'chem/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品管理',
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
     * 添加产品
     * @adminMenu(
     *     'name'   => '添加产品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $themeModel        = new ThemeModel();
        $productThemeFiles = $themeModel->getActionThemeFiles('chem/Product/index');
        $this->assign('product_theme_files', $productThemeFiles);
        return $this->fetch();
    }

    /**
     * 添加产品提交
     * @adminMenu(
     *     'name'   => '添加产品提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if ($this->request->isPost()) {
            $data    = $this->request->param();
            $product = $data['product'];
            $result  = $this->validate($product, 'AdminProduct');
            if ($result !== true) {
                $this->error($result);
            }

            $chemProductModel = new ChemProductModel();

            $chemProductModel->adminAddProduct($data['product'], $data['product']['categories']);

            $this->success('添加成功!', url('AdminProduct/edit', ['id' => $chemProductModel->id]));
        }

    }

    /**
     * 编辑产品
     * @adminMenu(
     *     'name'   => '编辑产品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑产品',
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

        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');

        $product['content'] = cmf_replace_content_file_url(htmlspecialchars_decode($product['content']));

        $chemStoragePlaceModel = new ChemStoragePlaceModel();
        $storagePlaces         = $chemStoragePlaceModel->select();
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('product', $product);
        $this->assign('packages', $packages);
        $this->assign('storage_places', $storagePlaces);
        $this->assign('product_categories', $productCategories);
        $this->assign('product_category_ids', $productCategoryIds);

        return $this->fetch();
    }

    /**
     * 产品包装列表
     * @adminMenu(
     *     'name'   => '产品包装列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品包装列表',
     *     'param'  => ''
     * )
     */
    public function packs()
    {
        $id = $this->request->param('id', 0, 'intval');

        $packages = Db::name('chem_product_pack')->where(['product_id' => $id])->order('id ASC')->select();

        $chemProductModel   = new ChemProductModel();
        $product            = $chemProductModel->where('id', $id)->find();
        $productCategories  = $product->categories()->alias('a')->column('a.name', 'a.id');
        $productCategoryIds = implode(',', array_keys($productCategories));

        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');

        $product['content'] = cmf_replace_content_file_url(htmlspecialchars_decode($product['content']));

        $chemStoragePlaceModel = new ChemStoragePlaceModel();
        $storagePlaces         = $chemStoragePlaceModel->column('*','id');
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('product', $product);
        $this->assign('packages', $packages);
        $this->assign('storage_places', $storagePlaces);
        $this->assign('product_categories', $productCategories);
        $this->assign('product_category_ids', $productCategoryIds);

        return $this->fetch();
    }


    /**
     * 编辑产品提交
     * @adminMenu(
     *     'name'   => '编辑产品提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑产品提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data    = $this->request->param();
            $product = $data['product'];
            $result  = $this->validate($product, 'AdminProduct');
            if ($result !== true) {
                $this->error($result);
            }

            $chemProductModel = new ChemProductModel();

            $chemProductModel->adminEditProduct($data['product'], $data['product']['categories']);

            $this->success('保存成功!');

        }
    }

    public function delete()
    {
        $data = $this->request->param();
        if (isset($data['id'])) {
            $id = $this->request->param("id", 0, 'intval');

            Db::name('ChemProduct')->delete($id);

            Db::name('ChemProductPack')->where('product_id', $id)->delete();

            $this->success("删除成功！");
        }

        if (isset($data['ids'])) {
            $ids = $this->request->param('ids/a');

            Db::name('ChemProduct')->where('id', 'in', $ids)->delete();

            Db::name('ChemProductPack')->where('product_id', 'in', $ids)->delete();

            $this->success("删除成功！");

        }
    }

    /**
     * 产品发布/取消发布
     * @adminMenu(
     *     'name'   => '产品发布/取消发布',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品发布/取消发布',
     *     'param'  => ''
     * )
     */
    public function publish()
    {
        $param            = $this->request->param();
        $chemProductModel = new ChemProductModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $chemProductModel->where(['id' => ['in', $ids]])->update(['status' => 1]);

            $this->success("上架成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $chemProductModel->where(['id' => ['in', $ids]])->update(['status' => 0]);

            $this->success("下架成功！", '');
        }

    }

    /**
     * 产品置顶
     * @adminMenu(
     *     'name'   => '产品置顶',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品置顶',
     *     'param'  => ''
     * )
     */
    public function top()
    {
        $param            = $this->request->param();
        $chemProductModel = new ChemProductModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $chemProductModel->where(['id' => ['in', $ids]])->update(['is_top' => 1]);

            $this->success("置顶成功！", '');

        }

        if (isset($_POST['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $chemProductModel->where(['id' => ['in', $ids]])->update(['is_top' => 0]);

            $this->success("取消置顶成功！", '');
        }
    }

    /**
     * 产品推荐
     * @adminMenu(
     *     'name'   => '产品推荐',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品推荐',
     *     'param'  => ''
     * )
     */
    public function recommend()
    {
        $param            = $this->request->param();
        $chemProductModel = new ChemProductModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $chemProductModel->where(['id' => ['in', $ids]])->update(['recommended' => 1]);

            $this->success("推荐成功！", '');

        }
        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $chemProductModel->where(['id' => ['in', $ids]])->update(['recommended' => 0]);

            $this->success("取消推荐成功！", '');

        }
    }

    /**
     * 产品选择
     * @adminMenu(
     *     'name'   => '产品选择',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品选择',
     *     'param'  => ''
     * )
     */
    public function select()
    {
        $param = $this->request->param();

        $categoryId   = $this->request->param('category', 0, 'intval');
        $orderDraftId = $this->request->param('order_draft_id', 0, 'intval');

        $productService = new ProductService();
        $data           = $productService->adminPublishedProductList($param);

        $chemCategoryModel = new ChemCategoryModel();
        $categoryTree      = $chemCategoryModel->adminCategoryTree($categoryId);

        $orderDraft = Db::name('order_draft')->where([
            'admin_id' => cmf_get_current_admin_id(),
            'id'       => $orderDraftId
        ])->find();

        if (empty($orderDraft)) {
            $this->error('订单草稿不存在！');
        }

        $orderDraftItems = json_decode($orderDraft['items'], true);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('products', $data->items());
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('order_draft_id', $orderDraftId);
        $this->assign('order_draft_items', $orderDraftItems);
        $this->assign('page', $data->render());

        return $this->fetch();
    }

    /**
     * 添加产品到订单草稿
     * @adminMenu(
     *     'name'   => '添加产品到订单草稿',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加产品到订单草稿',
     *     'param'  => ''
     * )
     */
    public function addToOrderDraft()
    {
        $orderDraftId = $this->request->param('order_draft_id', 0, 'intval');
        $quantity     = $this->request->param('quantity', 0, 'intval');
        $packId       = $this->request->param('pack_id', 0, 'intval');

        $orderDraft = Db::name('order_draft')->where([
            'admin_id' => cmf_get_current_admin_id(),
            'id'       => $orderDraftId
        ])->find();

        if (empty($orderDraft)) {
            $this->error('记录不存在！');
        }

        $items = json_decode($orderDraft['items'], true);

        $findProductPack = Db::name('chem_product_pack')->where('id', $packId)->find();
        if (empty($findProductPack)) {
            $this->error('产品库存不存在！');
        }

        $product = Db::name('chem_product')->where('id', $findProductPack['product_id'])->find();
        if (empty($product)) {
            $this->error('产品不存在！');
        }

        $goodsSpec = "包装:{$findProductPack['pack']}{$findProductPack['unit']}";

        if (!empty($findProductPack['packing_material'])) {
            $goodsSpec .= "({$findProductPack['packing_material']})";
        }

        if (!empty($findProductPack['purity'])) {
            $goodsSpec .= " 纯度:{$findProductPack['purity']}";
        }

        if (!empty($findProductPack['brand'])) {
            $goodsSpec .= " 品牌:{$findProductPack['brand']}";
        }

        $items['chem_product_pack' . $packId] = [
            'goods_id'         => $findProductPack['product_id'],
            'goods_spec_id'    => $packId,
            'market_price'     => $findProductPack['price'],
            'goods_price'      => $findProductPack['price'],
            'vip_price'        => $findProductPack['price'],
            'goods_quantity'   => $quantity,
            'table_name'       => 'chem_product',
            'goods_spec_table' => 'chem_product_pack',
            'goods_name'       => "CAS:{$product['cas']} " . $product['name'],
            'goods_spec'       => $goodsSpec,
            'more'             => json_encode(['goods_spec' => $findProductPack])
        ];

        $totalAmount = 0;

        foreach ($items as $item) {
            $totalAmount += $item['goods_price'] * $item['goods_quantity'];
        }

        Db::name('order_draft')->where([
            'admin_id' => cmf_get_current_admin_id(),
            'id'       => $orderDraftId
        ])->update(['items' => json_encode($items), 'total_amount' => round($totalAmount, 2)]);

        $this->success("添加成功！");

    }

    /**
     * 产品导入
     * @adminMenu(
     *     'name'   => '产品导入',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品导入',
     *     'param'  => ''
     * )
     */
    public function import()
    {
        if ($this->request->isPost()) {

        } else {
            return $this->fetch();
        }
    }

    /**
     * 产品导入提交
     * @adminMenu(
     *     'name'   => '产品导入提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '产品导入提交',
     *     'param'  => ''
     * )
     */
    public function importPost()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (isset($post['file'])) {
                $file = './upload/' . $post['file'];

                if (!file_exists_case($file)) {
                    $this->error('上传的文件不存在！');
                }

                session('chem_admin_product_import_file', $file);

                $chemProductLogic = new ChemProductLogic();
                $rowsCount        = $chemProductLogic->getImportFileRowsCount($file);
                $rows             = $chemProductLogic->getImportFileRows($file, 1, 20);

                $this->assign('file_rows_count', $rowsCount);
                $this->assign('file_rows', $rows);
                $this->assign('file_uploaded', true);

                return $this->fetch();
            } else {
                $file             = session('chem_admin_product_import_file');
                $currentPage      = $this->request->param('page', 1, 'intval');
                $chemProductLogic = new ChemProductLogic();
                $rowsCount        = $chemProductLogic->getImportFileRowsCount($file);

                $pageSize   = 100;
                $firstRow   = ($currentPage - 1) * $pageSize;
                $totalPages = ceil($rowsCount / $pageSize);

                $rowsMessage = $chemProductLogic->importFileRows($file, $firstRow, $pageSize);
                $nextPage    = $currentPage + 1;
                $message     = "正在导入,已经导入！" . intval(($pageSize * $currentPage) / $rowsCount * 100) . '%';

                if ($currentPage == $totalPages) {
                    $nextPage = 0;
                    $message  = '导入完成！';
                }

                $this->success($message, '', [
                    'log'       => $rowsMessage['logs'],
                    'next_page' => $nextPage
                ]);
            }
        }
    }


}