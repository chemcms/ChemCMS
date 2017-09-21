<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\taglib;

use think\template\TagLib;

class Chem extends TagLib
{
    /**
     * 定义标签列表
     */
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'products'   => ['attr' => 'field,where,limit,order,page,relation,returnVarName,pageVarName,categoryIds', 'close' => 1],//非必须属性item
        'breadcrumb' => ['attr' => 'cid', 'close' => 1],//非必须属性self
    ];

    /**
     * 产品列表标签
     */
    public function tagProducts($tag, $content)
    {
        $item          = empty($tag['item']) ? 'vo' : $tag['item'];//循环变量名
        $field         = empty($tag['field']) ? '*' : $tag['field'];
        $limit         = empty($tag['limit']) ? '10' : $tag['limit'];
        $order         = empty($tag['order']) ? 'product.create_time DESC' : $tag['order'];
        $relation      = empty($tag['relation']) ? '' : $tag['relation'];
        $pageVarName   = empty($tag['pageVarName']) ? '__PAGE_VAR_NAME__' : $tag['pageVarName'];
        $returnVarName = empty($tag['returnVarName']) ? 'products_data' : $tag['returnVarName'];

        $where = '""';
        if (!empty($tag['where']) && strpos($tag['where'], '$') === 0) {
            $where = $tag['where'];
        }

        $page = "''";
        if (!empty($tag['page'])) {
            if (strpos($tag['page'], '$') === 0) {
                $page = $tag['page'];
            } else {
                $page = intval($tag['page']);
                $page = "'{$page}'";
            }
        }

        $categoryIds = "''";
        if (!empty($tag['categoryIds'])) {
            if (strpos($tag['categoryIds'], '$') === 0) {
                $categoryIds = $tag['categoryIds'];
                $this->autoBuildVar($categoryIds);
            } else {
                $categoryIds = "'{$tag['categoryIds']}'";
            }
        }

        $parse = <<<parse
<?php
\${$returnVarName} = \app\chem\service\ApiService::products([
    'field'   => '{$field}',
    'where'   => {$where},
    'limit'   => '{$limit}',
    'order'   => '{$order}',
    'page'    => $page,
    'relation'=> '{$relation}',
    'category_ids'=>{$categoryIds}
]);

\${$pageVarName} = isset(\${$returnVarName}['page'])?\${$returnVarName}['page']:'';

 ?>
<volist name="{$returnVarName}.products" id="{$item}">
{$content}
</volist>
parse;
        return $parse;
    }

    /**
     * 面包屑标签
     */
    public function tagBreadcrumb($tag, $content)
    {
        $cid = $tag['cid'] ? '0' : $tag['cid'];

        if (!empty($cid)) {
            $this->autoBuildVar($cid);
        }

        $self = isset($tag['self']) ? 'true' : 'false';

        $parse = <<<parse
<?php
if(!empty({$cid})){
    \$__BREADCRUMB_ITEMS__ = \app\chem\service\ApiService::breadcrumb({$cid},{$self});
}
?>
<volist name="__BREADCRUMB_ITEMS__" id="vo">
    {$content}
</volist>
parse;

        return $parse;

    }


}