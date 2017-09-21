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

class AdminWarehouseValidate extends Validate
{
    protected $rule = [
        'name' => 'require',
    ];

    protected $message = [
        'name.require' => '名称不能为空',
    ];

}