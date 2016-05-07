<?php

/**
 * Class ComAdminAccoutController
 *
 * 公司后台验证控制器 by cooper
 */
class ComAdminAccountController extends ComAdminController {

    // 不检查权限方法
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
    public function getIndex($com)
    {
        // 获取头像 - no
        if (Auth::user()->head > 0) {
            Auth::user()->enclosures_path = DB::table('enclosures')->where('id', Auth::user()->head)->pluck('path');
        } else {
            Auth::user()->enclosures_path = 0;
        }

        // 加载模板
        //
        return View::make('com/admin/account/index')->with('com', $com)->with('user', Auth::user());
    }

    /**
     * 个人资料处理
     *
     * @return mixed
     */
    public function postIndex($com)
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

            // 头像处理 - no
            if (Input::hasFile('file')) {
                $files = Input::file('file');
                $file = $files[0];
                $str = explode("/", $file->getMimeType());
                if ($str[0] != 'image') {
                    return Redirect::to($com->path . '/account')->with('error', '请上传图片，格式为png、jpg、jpeg、gif或bmp！');
                } else if ($file->getSize() > (1024 * 1024)) {
                    return Redirect::to($com->path . '/account')->with('error', '上传图片过大，请控制在1M以内！');
                }

                $destinationPath = 'uploads/' . $com->id . '/' . date('Y/m/d');
                $extension = $file->getClientOriginalExtension();
                $filename = str_random(8) . '.' . $extension;
                //重置头像图片100*100
                if(!is_dir($destinationPath)){//先判断最终路径是否创建
                    mkdir($destinationPath,0775,true);//递归创建 － no
                }
                $upload_success = Image::make($file->getRealPath())->resize(100, 100)->save(public_path().'/'.$destinationPath.'/'.$filename);

                if ($upload_success) {
                    $istr = explode(".", $filename);

                    $encl = new Enclosure();
                    $encl->enclosure_name = $file->getClientOriginalName();
                    $encl->type = 'image';
                    $encl->suffix = $file->getClientOriginalExtension();
                    $encl->path = $destinationPath . '/' . $filename;
                    if ($encl->save()) {
                        $user->head = $encl->id;
                    }
                } else {
                    return Redirect::to($com->path . '/account')->with('error', '上传图片失败！');
                }
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
                return Redirect::to($com->path . '/admin/login')->with('success', '密码修改、请重新登录！');
            }

            // 提示修改信息
            //
            return Redirect::to($com->path . '/admin/account')->with('success', '资料更新成功!');
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());

    }

    /**
     * 登录显示
     *
     * @param $com
     *
     * @return mixed
     */
    public function getLogin($com)
    {
        // 加载模板
        //
        return View::make('com/admin/account/login')->with('com', $com);
    }

    /**
     * 登录处理
     *
     * @param $com
     */
    public function postLogin($com)
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
                return Redirect::to($com->path . '/admin')->with('success', '登陆成功');
            }
            else
            {
                // Redirect to the login page.
                //
                return Redirect::to($com->path . '/admin/login')->with('error', '邮箱 / 密码 无效');
            }
        }

        // 返回登录界面，并提示错误信息
        //
        return Redirect::to($com->path . '/admin/login')->withInput(Input::all())->withErrors($validator->getMessageBag());

    }

    /**
     * 退出处理
     *
     * @param $com
     */
    public function getLogout($com)
    {

        // Log the user out.
        //
        Auth::logout();

        // Redirect to the users page.
        //
        return Redirect::to($com->path . '/admin/login')->with('success', '退出成功');

    }
}