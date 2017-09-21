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

class AdminProductValidate extends Validate
{
    protected $rule = [
        'cas'     => 'require|unique:chem_product',
        'cn_name' => 'require',
    ];
    protected $message = [
        'cas.require'     => 'CAS号不能为空',
        'cas.unique'      => 'CAS号已经存在',
        'cn_name.require' => '中文名不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];
}