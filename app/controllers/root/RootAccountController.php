<?php

/**
 * Class RootAccountController
 *
 * 超级管理员验证控制器 by cooper
 */
class RootAccountController extends RootController {

    // 不检查权限方法
    //
    protected $whitelist = array(
        'getLogin',
        'postLogin'
    );

    /**
     * __construct 构造方法
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 个人资料
     *
     * @param $com
     *
     * @return mixed
     */
    public function getIndex()
    {
        // 加载模板
        //
        return View::make('root/account/index')->with('user', Auth::user());
    }

    /**
     * 个人资料处理
     *
     * @return mixed
     */
    public function postIndex()
    {
        // 验证规则
        //
        $rules = array(
            'name'   => 'Required',
            'mobile' => 'Required',
        );

        // 如果提交更改密码
        //
        if (Input::get('password'))
        {
            // Update the validation rules.
            //
            $rules['password']              = 'Required|Confirmed|min:6';
            $rules['password_confirmation'] = 'Required';
        }

        // 验证
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证通过
        //
        if ($validator->passes())
        {
            // 初始化用户模型、数据
            //
            $user         = User::find(Auth::user()->id);
            $user->name   = Input::get('name');
            $user->mobile = Input::get('mobile');

            // 如果密码不为空
            //
            if (Input::get('password') !== '')
            {
                $user->password = Hash::make(Input::get('password'));
            }

            // 保存数据
            //
            $user->save();

            // 更改密码退出重新登录
            //
            if (Input::get('password') !== '')
            {
                // 注销
                //
                Auth::logout();

                // 转跳登录页面
                //
                return Redirect::to('root/login')->with('success', '密码修改、请重新登录！');
            }

            // 提示修改信息
            //
            return Redirect::to('root/account')->with('success', '资料更新成功!');
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());

    }

    /**
     * 登录显示
     *
     * @return mixed
     */
    public function getLogin()
    {
        // 清除登录信息
        //
        if (Auth::check())
        {
            Auth::logout();
        }

        // 加载模板
        //
        return View::make('root/account/login');
    }

    /**
     * 登录处理
     */
    public function postLogin()
    {

        // 表单验证规则
        //
        $rules = array(
            'email'    => 'Required|Email',
            'password' => 'Required'
        );

        // 获取表单信息
        //
        $email    = Input::get('email');
        $password = Input::get('password');

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证成功处理
        //
        if ($validator->passes())
        {
            // 验证用户名，密码
            //
            if (Auth::attempt(array(
                'user_name' => $email,
                'password'  => $password
            ))
            )
            {
                // Redirect to the users page.
                //
                return Redirect::to('root')->with('success', '登陆成功');
            }
            else
            {
                // Redirect to the login page.
                //
                return Redirect::to('root/login')->with('error', '邮箱 / 密码 无效');
            }
        }

        // 返回登录界面，并提示错误信息
        //
        return Redirect::to('root/login')->withInput(Input::all())->withErrors($validator->getMessageBag());

    }

    /**
     * 退出处理
     */
    public function getLogout()
    {

        // Log the user out.
        //
        Auth::logout();

        // Redirect to the users page.
        //
        return Redirect::to('root/login')->with('success', '退出成功');
    }

}
