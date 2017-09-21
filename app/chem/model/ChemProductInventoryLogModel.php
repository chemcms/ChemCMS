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

class ChemProductInventoryLogModel extends Model
{
    /**
     * 关联 product_pack表
     * @return $this
     */
    public function pack()
    {
        return $this->belongsTo('ChemProductPackModel', 'pack_id')->setEagerlyType(1);
    }

    /**
     * 关联 user表
     * @return $this
     */
    public function user()
    {
        return $this->belongsTo('UserModel', 'user_id')->setEagerlyType(1);
    }


}

