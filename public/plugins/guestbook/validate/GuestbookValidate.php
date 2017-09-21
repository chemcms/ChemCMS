<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace plugins\guestbook\validate;

use think\Validate;

class GuestbookValidate extends Validate
{
    protected $rule = [
        'company'   => 'require',
        'full_name' => 'require',
        'mobile'    => 'require',
        'content'   => 'require',
        'captcha'   => 'require',
    ];

    protected $message = [
        'company.require'   => '公司或单位名称不能为空',
        'captcha.require'   => '验证码不能为空',
        'full_name.require' => '姓名不能为空',
        'mobile.require'    => '手机不能为空',
        'content.require'   => '留言内容不能为空',
    ];

}