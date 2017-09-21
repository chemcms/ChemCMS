<?php 
/**
 * @ 处理标签函数
 * @ $tag以字符串方式传入,通过sp_param_lable函数解析为以下变量。例："cid:1,2;order:post_date desc,listorder desc;"
 * ids:调用指定id的一个或多个数据,如 1,2,3
 * cid:数据所在分类,可调出一个或多个分类数据,如 1,2,3 默认值为全部,在当前分类为:'.$cid.'
 * field:调用post指定字段,如(id,post_title...) 默认全部
 * limit:数据条数,默认值为10,可以指定从第几条开始,如3,8(表示共调用8条,从第3条开始)
 * order:推荐方式(post_date) (desc/asc/rand())
 */

function sp_chem_products($tag,$where=array(),$pagesize=20,$pagesetting='',$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}'){
	$where=array();
	$tag=sp_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';
	$limit = !empty($tag['limit']) ? $tag['limit'] : '';
	$order = !empty($tag['order']) ? $tag['order'] : 'update_date desc';


	//根据参数生成查询条件
	$where['status'] = array('eq',1);
	$where['product_status'] = array('eq',1);

	if (isset($tag['cid'])) {
		$where['cat_id'] = array('in',$tag['cid']);
	}

	if (isset($tag['ids'])) {
		$where['cat_product_id'] = array('in',$tag['ids']);
	}

	$join = "".C('DB_PREFIX').'chem_product as b on a.product_id =b.id';
	$join2= "".C('DB_PREFIX').'users as c on b.author = c.id';
	$rs= M("ChemCatProduct");
	$totalsize=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->count();

	$content=array();
	$limit_firstrow=0;
	if(!empty($limit)){
		if ($pagesize == 0) {
			$pagesize = 20;
		}
		$PageParam = C("VAR_PAGE");
		$page = new \Page($totalsize,$pagesize);
		$page->setLinkWraper("li");
		$page->__set("PageParam", $PageParam);
		$page->SetPager('default', $pagetpl, array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		$limit_firstrow=$page->firstRow;
		$limit=$page->listRows;
		$content['page']=$page->show('default');
	}
	$products=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->order($order)->limit($limit_firstrow . ',' . $limit)->select();

	$content['products']=$products;
	
	return $content;
}

/**
 * 获取某个产品的内容
 * @param int $cat_product_id
 * @param string $tag
 * @return array
 */
function sp_chem_product($cat_product_id,$tag=''){
	$tag=sp_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';


	//根据参数生成查询条件
	$where['status'] = array('eq',1);
	$where['product_status'] = array('eq',1);

	$join = "".C('DB_PREFIX').'chem_product as b on a.product_id =b.id';
	$join2= "".C('DB_PREFIX').'users as c on b.author = c.id';
	$rs= M("ChemCatProduct");
	$product=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->find();

	return $product;
}

/**
 * 
 * @param string $formula 分子式
 */
function sp_beautify_molformula($formula){
    //$formula=strtoupper($formula);
    while(preg_match('/([a-zA-Z])(\d+)/i',$formula,$mat)){
        $formula=str_replace($mat[0],$mat[1].'<sub>'.$mat[2].'</sub>',$formula);
    }
    return $formula;
}

