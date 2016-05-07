<?php

/**
 * Class ComSiteApplyContronller
 *
 * 公司前台验证控制器 by no
 */
class ComSiteApplyContronller extends ComSiteContronller 
{
    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
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
        $user = SiteUser::where('company_id', $com->id)->where('user_id', Auth::user()->id)->first();

        // 加载模板
        return View::make('com/site/apply/index')->with('com', $com)->with(compact('user'));
    }

    /**
     * 应用设置处理
     *
     * @return mixed
     */
    public function postIndex($com)
    {
        $site_user = SiteUser::find(Input::get('uid')); // 加载用户对象类
        $site_user->enable = Input::get('enable');
        $site_user->message = Input::get('message');
        $site_user->audio = Input::get('audio');
        
        if ($site_user->save()) {
            return Redirect::to($com->path . '/apply')->with('success', '应用设置成功！');
        } else {
            return Redirect::to($com->path . '/apply')->with('error', '应用设置失败！');
        }
    }
}
