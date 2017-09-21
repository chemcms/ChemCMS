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

class ChemStoragePlaceModel extends Model
{


    /**
     * 关联 chem_warehouse表
     * @return $this
     */
    public function warehouse()
    {
        return $this->belongsTo('ChemWarehouseModel', 'warehouse_id')->setEagerlyType(1);
    }
}