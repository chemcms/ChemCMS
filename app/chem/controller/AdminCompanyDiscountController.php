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
use think\Db;

class AdminCompanyDiscountController extends AdminBaseController
{
    /**
     * 单位品牌折扣列表
     * @adminMenu(
     *     'name'   => '单位折扣管理',
     *     'parent' => 'chem/AdminIndex/default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '单位折扣管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $companyId = $this->request->param('id', 0, 'intval');

        $discounts      = Db::name('chem_company_discount')->where('company_id', $companyId)->column('*', 'brand_id');
        $chemBrandModel = new ChemBrandModel();
        $brands         = $chemBrandModel->select();

        $this->assign('brands', $brands);
        $this->assign('company_id', $companyId);
        $this->assign('discounts', $discounts);
        return $this->fetch();
    }

    /**
     * 设置单位品牌折扣
     * @adminMenu(
     *     'name'   => '设置单位品牌折扣',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '设置单位品牌折扣',
     *     'param'  => ''
     * )
     */
    public function setDiscount()
    {
        $companyId    = $this->request->param('company_id', 0, 'intval');
        $brandId      = $this->request->param('brand_id', 0, 'intval');
        $discount     = $this->request->param('discount', 0, 'intval');
        $findDiscount = Db::name('chem_company_discount')->where(['brand_id' => $brandId, 'company_id' => $companyId])->find();

        if (empty($findDiscount)) {
            Db::name('chem_company_discount')->where(['brand_id' => $brandId, 'company_id' => $companyId])->insert([
                'brand_id'   => $brandId,
                'company_id' => $companyId,
                'discount'   => $discount
            ]);
        } else {
            Db::name('chem_company_discount')->where('id', $findDiscount['id'])->update([
                'discount' => $discount
            ]);
        }

        $this->success('设置成功！');

    }


}