<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace plugins\guestbook\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginBaseController;
use plugins\guestbook\model\PluginGuestbookModel;
use think\Db;

class GuestbookController extends PluginBaseController
{

    public function add()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'Guestbook');

        if ($result !== true) {
            $this->error($result);
        }

        $pluginGuestbookModel = new PluginGuestbookModel();

        $data['create_time'] = time();
        $pluginGuestbookModel->allowField(true)->save($data);


        $this->success('留言成功！');
    }

}
