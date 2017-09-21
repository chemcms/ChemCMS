<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace plugins\guestbook\controller; //Demo插件英文名，改成你的插件英文就行了

use think\Db;
use cmf\controller\PluginBaseController;
use plugins\guestbook\model\PluginGuestbookModel;

class AdminIndexController extends PluginBaseController
{

    public function _initialize()
    {
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        } else {
            //TODO no login
            $this->error('未登录');
        }
    }

    public function index()
    {
        $pluginGuestbookModel = new PluginGuestbookModel();
        // print_r($demos);

        $messages = $pluginGuestbookModel->order('create_time DESC')->paginate();

        $this->assign("messages", $messages);


        $this->assign("page", $messages->render());

        return $this->fetch('/admin_index');
    }

}
