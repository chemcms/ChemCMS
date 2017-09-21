<?php
// +----------------------------------------------------------------------
// | ChemCMS [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.chemcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Sam <sam_zb@chemcms.com>
// +----------------------------------------------------------------------
namespace app\chem\model;

use think\Model;

class ChemProductPackModel extends Model
{
    /**
     * 关联 product表
     * @return $this
     */
    public function product()
    {
        return $this->belongsTo('ChemProductModel', 'product_id')->setEagerlyType(1);
    }

}

