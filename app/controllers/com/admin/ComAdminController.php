<?php

/**
 * Class ComAdminController
 *
 * 公司后台主控制器，其他控制器继承 by cooper
 */
class ComAdminController extends BaseController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();

        $this->beforeFilter('com.admin.auth', array('except' => $this->whitelist));

        // 验证是否为后台用户登入 - no
        if (Auth::check()) {
            // 获取头像 - no
            if (Auth::user()->head > 0) {
                Auth::user()->enclosures_path = DB::table('enclosures')->where('id', Auth::user()->head)->pluck('path');
            } else {
                Auth::user()->enclosures_path = 0;
            }

            // 获取消息通知及提示音配置 － no
            $level = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('level');
            if ($level > 2) {
                Auth::user()->level = $level;
                Auth::user()->message = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('message');
                Auth::user()->audio = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('audio');
            } else {
                Auth::user()->level = 0;
            }
        }
    }

}