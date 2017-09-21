<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
return [
    'List/index'    => [
        'name'   => '化学应用-产品列表',
        'vars'   => [
            'id' => [
                'pattern' => '\d+',
                'require' => true
            ]
        ],
        'simple' => true
    ],
    'Product/index' => [
        'name'   => '化学应用-产品页',
        'vars'   => [
            'id'  => [
                'pattern' => '\d+',
                'require' => true
            ],
            'cid' => [
                'pattern' => '\d+',
                'require' => false
            ]
        ],
        'simple' => true
    ],
    'Search/index'  => [
        'name'   => '化学应用-搜索页',
        'vars'   => [

        ],
        'simple' => false
    ],
];