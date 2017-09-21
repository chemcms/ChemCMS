<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\validate;

use think\Validate;

class AdminPackValidate extends Validate
{
    protected $rule = [
        'product_id' => 'require',
        'price'      => 'require',
        'pack'       => 'require',
        'unit'       => 'require',

    ];
    protected $message = [
        'product_id.require' => '未指定产品',
        'price.require'      => '价格不能为空',
        'pack.require'       => '包装大小不能为空',
        'unit.require'       => '单位不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];
}