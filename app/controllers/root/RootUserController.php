<?php

/**
 * Class RootUserController
 *
 * 超级管理员用户管理控制器 by cooper
 */
class RootUserController extends RootController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 首页
     *
     * @return mixed
     */
    public function index()
    {
        // 分页 默认15条
        //
        $users = User::orderBy('created_at', 'desc')->paginate();

        // 加载模板
        //
        return View::make('root/user/index')->with(compact('users'));
    }

    /**
     * 禁用用户
     *
     * @param null $userId
     *
     * @return mixed
     */
    public function getDisabled($userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($user = User::find($userId)))
        {
            return Redirect::to('root/user')->with('error', '用户不存在');
        }

        // 禁用
        //
        $user->disabled = 'true';

        // 保存
        //
        if ($user->save())
        {
            // 成功后转跳
            //
            return Redirect::to('root/user')->with('success', '禁用用户成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 启用用户
     *
     * @param null $userId
     *
     * @return mixed
     */
    public function getEnable($userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($user = User::find($userId)))
        {
            return Redirect::to('root/user')->with('error', '用户不存在');
        }

        // 启用
        //
        $user->disabled = 'false';

        // 保存
        //
        if ($user->save())
        {
            // 成功后转跳
            //
            return Redirect::to('root/user')->with('success', '启用用户成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 查看用户信息
     *
     * @param null $userId
     *
     * @return mixed
     */
    public function getView($userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($user = User::find($userId)))
        {
            return Redirect::to('root/user')->with('error', '用户不存在');
        }

        // 加载模板
        //
        return View::make('root/user/view')->with(compact('user'));

    }

    /**
     * 更改用户密码
     *
     * @param null $userId
     *
     * @return mixed
     */
    public function getChangePassword($userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($user = User::find($userId)))
        {
            return Redirect::to('root/user')->with('error', '用户不存在');
        }

        // 加载模板
        //
        return View::make('root/user/changepassword')->with(compact('user'));
    }

    /**
     * 更改用户处理
     *
     * @param null $userId
     *
     * @return mixed
     */
    public function postChangePassword($userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($user = User::find($userId)))
        {
            return Redirect::to('root/user')->with('error', '用户不存在');
        }

        // 验证规则
        //
        $rules = array(
            'password'              => 'Required|Confirmed|min:6',
            'password_confirmation' => 'Required',
        );

        // 验证
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证通过
        //
        if ($validator->passes())
        {
            // 新密码
            //
            $user->password = Hash::make(Input::get('password'));

            // 保存
            //
            if ($user->save())
            {
                // 提示修改信息
                //
                return Redirect::to('root/user')->with('success', '资料更新成功!');
            }

        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());

    }


}
