<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\service;

use app\chem\model\ChemProductModel;

class ProductService
{

    public function adminProductList($filter)
    {
        $where = [
            'a.create_time' => ['>=', 0],
            'a.delete_time' => ['eq', 0]
        ];

        $join = [
            ['__USER__ u', 'a.user_id = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            $where['b.category_id'] = ['eq', $category];
            array_push($join, [
                '__CHEM_CATEGORY_PRODUCT__ b', 'a.id = b.product_id'
            ]);
            $field = 'a.*,b.id AS product_category_id,b.list_order,b.category_id,u.user_login,u.user_nickname,u.user_email';
        }

        $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
        $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.create_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['a.create_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['a.create_time'] = ['<= time', $endTime];
            }
        }

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.cn_name'] = ['like', "%$keyword%"];
        }

        $cas = empty($filter['cas']) ? '' : $filter['cas'];
        if (!empty($cas)) {
            $where['a.cas'] = ['like', "%$cas%"];
        }

        $chemProductModel = new ChemProductModel();
        $products         = $chemProductModel->alias('a')->field($field)
            ->join($join)
            ->where($where)
            ->order('create_time', 'DESC')
            ->paginate(10);

        return $products;

    }

    public function adminPublishedProductList($filter)
    {
        $where = [
            'a.create_time' => ['>=', 0],
            'a.delete_time' => ['eq', 0]
        ];

        $join = [
            ['__USER__ u', 'a.user_id = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
        $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.create_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['a.create_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['a.create_time'] = ['<= time', $endTime];
            }
        }

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.cn_name'] = ['like', "%$keyword%"];
        }

        $cas = empty($filter['cas']) ? '' : $filter['cas'];
        if (!empty($cas)) {
            $where['a.cas'] = ['like', "%$cas%"];
        }

        $chemProductModel = new ChemProductModel();
        $products         = $chemProductModel->alias('a')->field($field)
            ->join($join)
            ->where($where)
            ->order('create_time', 'DESC')
            ->paginate(10);

        $products->appends($filter);

        if (!$products->isEmpty()) {
            $products->load('packs');
        }

        return $products;

    }

    public function publishedProduct($productId, $categoryId = 0)
    {
        $chemProductModel = new ChemProductModel();

        if (empty($categoryId)) {

            $where = [
                //'product.published_time' => [['< time', time()], ['> time', 0]],
                'product.status' => 1,
                'product.id'     => $productId
            ];

            $product = $chemProductModel->alias('product')->field('product.*')
                ->where($where)
                ->find();
        } else {
            $where = [
                //'product.published_time'     => [['< time', time()], ['> time', 0]],
                'product.status'       => 1,
                'relation.category_id' => $categoryId,
                'relation.product_id'  => $productId
            ];

            $join    = [
                ['__CHEM_CATEGORY_PRODUCT__ relation', 'product.id = relation.product_id']
            ];
            $product = $chemProductModel->alias('product')->field('product.*')
                ->join($join)
                ->where($where)
                ->find();
        }


        return $product;
    }

}