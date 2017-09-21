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

/**
 * Class AdminIndexController
 * @package app\chem\controller
 * @adminMenuRoot(
 *     'name'   =>'产品管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'flask',
 *     'remark' =>'产品管理'
 * )
 * @adminMenuRoot(
 *     'name'   =>'仓储物流',
 *     'action' =>'defaultStock',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'truck',
 *     'remark' =>'仓储物流'
 * )
 */
class AdminIndexController extends AdminBaseController
{


}
