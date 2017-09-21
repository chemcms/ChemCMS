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

class ChemStockOutItemModel extends Model
{
    /**
     * 关联 stock_out表
     * @return $this
     */
    public function stockOut()
    {
        return $this->belongsTo('ChemStockOutModel', 'stock_out_id')->setEagerlyType(1);
    }

    /**
     * 关联 product_pack表
     * @return $this
     */
    public function pack()
    {
        return $this->belongsTo('ChemProductPackModel', 'pack_id')->setEagerlyType(1);
    }


}

