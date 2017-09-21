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
use app\chem\model\ChemCategoryModel;
use think\Db;

class ApiService
{
    /**
     * 功能:查询产品列表,支持分页;<br>
     * 注:此方法查询时关联三个表chem_category_product(category_product),chem_product(product),user;在指定排序(order),指定查询条件(where)最好指定一下表别名
     * @param array $param 查询参数<pre>
     * array(
     *  'category_ids'=>'',
     *  'where'=>'',
     *  'limit'=>'',
     *  'order'=>'',
     *  'page'=>'',
     *  'relation'=>''
     * )
     * 字段说明:
     * category_ids:产品所在分类,可指定一个或多个分类id,以英文逗号分隔,如1或1,2,3 默认值为全部
     * field:调用指定的字段@todo
     *   如只调用products表里的id和cn_name字段可以是product.id,product.cn_name; 默认全部,
     *   此方法查询时关联三个表chem_category_product(category_product),chem_product(product),user;
     *   所以最好指定一下表名,以防字段冲突
     * limit:数据条数,默认值为10,可以指定从第几条开始,如3,8(表示共调用8条,从第3条开始)
     * order:排序方式,如按chem_product表里的published_time字段倒序排列：product.published_time desc
     * where:查询条件,字符串形式,和sql语句一样,请在事先做好安全过滤,最好使用第二个参数$where的数组形式进行过滤,此方法查询时关联多个表,所以最好指定一下表名,以防字段冲突,查询条件(只支持数组),格式和thinkPHP的where方法一样,此方法查询时关联多个表,所以最好指定一下表名,以防字段冲突;
     * </pre>
     * @return array 包括分页的产品列表<pre>
     * 格式:
     * array(
     *     "products"=>array(),//产品列表,array
     *     "page"=>"",//生成的分页html,不分页则没有此项
     *     "total"=>100, //符合条件的产品总数,不分页则没有此项
     *     "total_pages"=>5 // 总页数,不分页则没有此项
     * )</pre>
     */
    public static function products($param)
    {
        $chemProductModel = new ChemProductModel();

        $where = [
            //'product.published_time' => [['> time', 0], ['<', time()]],
            'product.status'      => 1,
            'product.delete_time' => 0
        ];

        $paramWhere = empty($param['where']) ? '' : $param['where'];

        $limit       = empty($param['limit']) ? 10 : $param['limit'];
        $order       = empty($param['order']) ? '' : $param['order'];
        $page        = isset($param['page']) ? $param['page'] : false;
        $relation    = empty($param['relation']) ? '' : $param['relation'];
        $categoryIds = empty($param['category_ids']) ? '' : $param['category_ids'];

        $join = [
            ['__USER__ user', 'product.user_id = user.id'],
            ['__CHEM_CATEGORY_PRODUCT__ category_product', 'product.id = category_product.product_id']
        ];

        if (!empty($categoryIds)) {

            if (!is_array($categoryIds)) {
                $categoryIds = explode(',', $categoryIds);
            }

            if (count($categoryIds) == 1) {
                $where['category_product.category_id'] = ['eq', $categoryIds[0]];
            } else {
                $where['category_product.category_id'] = ['in', $categoryIds];
            }
        }

        $products = $chemProductModel->alias('product')->field('product.*,user.user_login,user.user_nickname,user.user_email,category_product.category_id')
            ->join($join)
            ->where($where)
            ->where($paramWhere)
            ->order($order);

        $return = [];

        if (empty($page)) {
            $products = $products->limit($limit)->select();

            if (!empty($relation)) {
                $products->load($relation);
            }

            $return['products'] = $products;
        } else {

            if (is_array($page)) {
                if (empty($page['list_rows'])) {
                    $page['list_rows'] = 10;
                }

                $products = $products->paginate($page);
            } else {
                $products = $products->paginate(intval($page));
            }

            if (!empty($relation)) {
                $products->load($relation);
            }

            $return['products']    = $products->items();
            $return['page']        = $products->render();
            $return['total']       = $products->total();
            $return['total_pages'] = $products->lastPage();
        }


        return $return;

    }

    /**
     * 获取指定id的产品
     * @param int $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function product($id)
    {
        $chemProductModel = new ChemProductModel();

        $where = [
            //'published_time' => [['> time', 0], ['<', time()]],
            'status'      => 1,
            'id'          => $id,
            'delete_time' => 0
        ];

        return $chemProductModel->where($where)->find();
    }

    /**
     * 返回指定分类
     * @param int $id 分类id
     * @return array 返回符合条件的分类
     */
    public static function category($id)
    {
        $chemCategoryModel = new ChemCategoryModel();

        $where = [
            'status'      => 1,
            'delete_time' => 0,
            'id'          => $id
        ];

        return $chemCategoryModel->where($where)->find();
    }

    /**
     * 返回指定分类下的子分类
     * @param int $categoryId 分类id
     * @return false|\PDOStatement|string|\think\Collection 返回指定分类下的子分类
     */
    public static function subCategories($categoryId)
    {
        $chemCategoryModel = new ChemCategoryModel();

        $where = [
            'status'      => 1,
            'delete_time' => 0,
            'parent_id'   => $categoryId
        ];

        return $chemCategoryModel->where($where)->select();
    }

    /**
     * @todo
     * 返回指定分类下的所有子分类
     * @param int $categoryId 分类id
     * @return array 返回指定分类下的所有子分类
     */
    public static function allSubCategories($categoryId)
    {
    }

    /**
     * 返回符合条件的所有分类
     * @param array $param 查询参数<pre>
     * array(
     *  'where'=>'',
     *  'order'=>'',
     * )</pre>
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function categories($param)
    {
        $paramWhere = empty($param['where']) ? '' : $param['where'];

        $order = empty($param['order']) ? '' : $param['order'];

        $chemCategoryModel = new ChemCategoryModel();

        $where = [
            'status'      => 1,
            'delete_time' => 0,
        ];

        return $chemCategoryModel
            ->where($where)
            ->where($paramWhere)
            ->order($order)
            ->select();
    }

    /**
     * 获取面包屑数据
     * @param int $categoryId 当前产品所在分类,或者当前分类的id
     * @param boolean $withCurrent 是否获取当前分类
     * @return array 面包屑数据
     */
    public static function breadcrumb($categoryId, $withCurrent = false)
    {
        $data              = [];
        $chemCategoryModel = new ChemCategoryModel();

        $path = $chemCategoryModel->where(['id' => $categoryId])->value('path');

        if (!empty($path)) {
            $parents = explode('-', $path);
            if (!$withCurrent) {
                array_pop($parents);
            }

            if (!empty($parents)) {
                $data = $chemCategoryModel->where(['id' => ['in', $parents]])->order('path ASC')->select();
            }
        }

        return $data;
    }

}