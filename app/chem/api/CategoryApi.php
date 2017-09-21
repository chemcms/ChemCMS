<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\api;

use app\chem\model\ChemCategoryModel;

class CategoryApi
{
    /**
     * 分类列表 用于模板设计
     * @param array $param
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function index($param = [])
    {
        $chemCategoryModel = new ChemCategoryModel();

        $where = ['delete_time' => 0];

        if (!empty($param['keyword'])) {
            $where['name'] = ['like', "%{$param['keyword']}%"];
        }

        //返回的数据必须是数据集或数组,item里必须包括id,name,如果想表示层级关系请加上 parent_id
        return $chemCategoryModel->where($where)->select();
    }

    /**
     * 分类列表 用于导航选择
     * @return array
     */
    public function nav()
    {
        $chemCategoryModel = new ChemCategoryModel();

        $where = ['delete_time' => 0];

        $categories = $chemCategoryModel->where($where)->select();

        $return = [
            //'name'  => '文章分类',
            'rule'  => [
                'action' => 'chem/List/index',
                'param'  => [
                    'id' => 'id'
                ]
            ],//url规则
            'items' => $categories //每个子项item里必须包括id,name,如果想表示层级关系请加上 parent_id
        ];

        return $return;
    }

}