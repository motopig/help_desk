<?php

/**
 * Class ComAdminApplyContronller
 *
 * 公司前台验证控制器 by no
 */
class ComAdminApplyContronller extends ComAdminController 
{
    /**
     * __construct 构造方法
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 应用设置
     *
     * @param $com
     *
     * @return mixed
     */
    public function getIndex($com)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');
        // 获取个人邮件配置
        $admin = AdminUser::where('company_id', $com->id)->where('user_id', Auth::user()->id)->first();

        // 加载模板
        if ($level == 4) {
            return View::make('com/admin/apply/staff')->with('com', $com)->with(compact('admin'));
        } else if ($level == 3) {
            return View::make('com/admin/apply/leader')->with('com', $com)->with(compact('admin'));
        } else if ($level == 2) {
            return View::make('com/admin/apply/manager')->with('com', $com)->with(compact('admin'));
        } else if ($level == 1) {
            return View::make('com/admin/apply/ceo')->with('com', $com)->with(compact('admin'));
        }
    }

    /**
     * 应用设置处理
     *
     * @return mixed
     */
    public function postIndex($com)
    {
        $level = Config::get('auth.com_admin_level_id'); // 判断管理员等级
        $admin_user = AdminUser::find(Input::get('aid')); // 加载用户对象类

        if ($level == 3 && Input::get('enable') == 'content') {
            $admin_user->enable = Input::get('enable');
            $admin_user->enable_count = '1';
        } else {
            $admin_user->enable = Input::get('enable');
            $admin_user->enable_count = '0';
        }

        // 组用户消息通知配置 - no
        if ($level > 2) {
            $admin_user->message = Input::get('message');
            $admin_user->audio = Input::get('audio');
        }

        if ($admin_user->save()) {
            return Redirect::to($com->path . '/admin/apply')->with('success', '应用设置成功！');
        } else {
            return Redirect::to($com->path . '/admin/apply')->with('error', '应用设置失败！');
        }
    }
}
