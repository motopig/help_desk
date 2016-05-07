<?php

/**
 * Class ComAdminUserController
 *
 * 公司后台用户管理控制器 by cooper
 */
class ComAdminUserController extends ComAdminController {

    // 设置分页数
    private $page = 8;

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
     * @param $com
     *
     * @return mixed
     */
    public function index($com)
    {
        // 加载模板
        //
        return View::make('com/admin/user/index')->with('com', $com);
    }

    /**
     * 管理员列表
     *
     * @param $com
     *
     * @return mixed
     */
    public function getLevelAdmin($com)
    {
        // 获取公司管理员
        //
        $users = AdminUser::where('company_id', $com->id)->where('admin_user.level', 2)->join('users', 'users.id', '=', 'admin_user.user_id')->select('admin_user.*', 'users.id', 'users.user_name', 'users.name', 'users.mobile', 'users.created_at')->paginate($this->page);
        
        // 加载模板
        //
        return View::make('com/admin/user/level_admin')->with('com', $com)->with(compact('users'));
    }

    /**
     * 管理员创建
     *
     * @param $com
     *
     * @return mixed
     */
    public function getLevelAdminCreate($com)
    {
        // 加载模板
        //
        return View::make('com/admin/user/level_admin_create')->with('com', $com);
    }

    /**
     * 管理员创建处理
     *
     * @param $com
     *
     * @return mixed
     */
    public function postLevelAdminCreate($com)
    {
        // 验证规则
        //
        $rules = array(
            // 'email'    => 'Required|Email|unique:users,user_name',
            'name'     => 'Required|min:2',
            'mobile'   => 'Required|min:8',
            'position' => 'Required|min:2',
            'password' => 'Required|min:6',
        );

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 判断是否存在管理员信息
            $user_id = 0;
            if ($userdata = User::where('user_name', Input::get('email'))->first()) {
                if (AdminUser::where('user_id', $userdata->id)->first()) {
                    return Redirect::to($com->path . '/admin/user/level/admin')->with('error', '创建管理员失败，帐号：' . e(Input::get('email')) . ' 已存在!');
                }

                $user_id = $userdata->id;
            }

            if ($user_id == 0) {
                // 初始化用户模型类
                //
                $user = new User;

                // 用户数据
                //
                $user->user_name = e(Input::get('email'));
                $user->password  = Hash::make(Input::get('password'));
                $user->name      = e(Input::get('name'));
                $user->mobile    = e(Input::get('mobile'));
                $user->head      = 0;
                $user->disabled  = 'false';

                // 保存用户数据
                //
                if ($user->save()) {
                    $user_id = $user->id;
                }
            }

            if ($user_id > 0)
            {
                // 初始化公司管理员模型
                //
                $admin_user = new AdminUser;

                // 公司管理员关系数据
                //
                $admin_user->company_id = $com->id;
                $admin_user->user_id    = $user_id;
                $admin_user->level      = 2;
                $admin_user->disabled   = 'false';
                $admin_user->position   = e(Input::get('position'));

                // 保存管理员关系数据
                //
                if ($admin_user->save())
                {
                    // 成功后转跳
                    //
                    return Redirect::to($com->path . '/admin/user/level/admin')->with('success', '创建管理员成功，帐号：' . e(Input::get('email')));
                }
                else
                {
                    // 失败后转跳
                    //
                    return Redirect::to($com->path . '/admin/user/level/admin')->with('error', '创建管理员失败，帐号：' . e(Input::get('email')));
                }
            } else {
                // 失败后转跳
                return Redirect::to($com->path . '/admin/user/level/admin')->with('error', '创建管理员失败，帐号：' . e(Input::get('email')));
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());

    }

    /**
     * 管理员修改
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function getLevelAdminEdit($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/admin')->with('error', '管理员不存在');
        }

        // 获取详细信息
        //
        $user_info = User::find($admin_user->user_id);

        // 加载模板
        //
        return View::make('com/admin/user/level_admin_edit')->with('com', $com)->with(compact('admin_user'))->with(compact('user_info'));
    }

    /**
     * 管理员修改处理
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function postLevelAdminEdit($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/admin')->with('error', '管理员不存在');
        }

        // 验证规则
        //
        $rules = array(
            // 'email'    => 'Required|Email|unique:users,user_name,' . $userId,
            'name'     => 'Required|min:2',
            'position' => 'Required|min:2',
            'mobile'   => 'Required|min:8',
        );

        // 如果提交更改密码
        //
        if (Input::get('password'))
        {
            // 增加验证规则
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
            $user            = User::find($userId);
            $user->user_name = Input::get('email');
            $user->name      = Input::get('name');
            $user->mobile    = Input::get('mobile');

            // 如果密码不为空
            //
            if (Input::get('password') !== '')
            {
                $user->password = Hash::make(Input::get('password'));
            }

            // 职位
            //
            $admin_user->position = e(Input::get('position'));

            // 保存数据
            //
            if ($user->save() && $admin_user->save())
            {
                // 提示修改信息
                //
                return Redirect::to($com->path . '/admin/user/level/admin')->with('success', '管理员更新成功! 帐号:' . $user->email);
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 禁用管理员
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function getLevelAdminDisabled($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/admin')->with('error', '管理员不存在');
        }

        // 禁用
        //
        $admin_user->disabled = 'true';

        // 保存
        //
        if ($admin_user->save())
        {

            $user = User::find($admin_user->user_id);

            // 成功后转跳
            //
            return Redirect::to($com->path . '/admin/user/level/admin')->with('success', '禁用管理员成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 启用管理员
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function getLevelAdminEnable($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/admin')->with('error', '管理员不存在');
        }

        // 禁用
        //
        $admin_user->disabled = 'false';

        // 保存
        //
        if ($admin_user->save())
        {

            // 获取客户详细信息
            //
            $user = User::find($admin_user->user_id);

            // 成功后转跳
            //
            return Redirect::to($com->path . '/admin/user/level/admin')->with('success', '启用管理员成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 客服列表
     *
     * @param $com
     *
     * @return mixed
     */
    public function getLevelCustomer($com)
    {

        // 获取等级
        //
        $level = Config::get('auth.com_admin_level_id');

        // 判断权限
        //
        if ($level < 3)
        {
            // 获取组成员
            //
            $users = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')->join('admin_user', 'admin_user.user_id', '=', $com->id . '_group_admin_user.user_id')->select('admin_user.*', $com->id . '_group_admin_user.*', 'users.id', 'users.user_name', 'users.name', 'users.mobile', 'users.created_at')->groupBy($com->id . '_group_admin_user.user_id')->paginate();
        }
        else
        {
            if ($level < 4)
            {
                // 获取管理组
                //
                $g = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');


            }
            else
            {
                $g = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '2')->get()->lists('group_id');
            }

            // 获取组成员
            //
            $users = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')->join('admin_user', 'admin_user.user_id', '=', $com->id . '_group_admin_user.user_id')->select('admin_user.*', $com->id . '_group_admin_user.*', 'users.id', 'users.user_name', 'users.name', 'users.mobile', 'users.created_at')->whereIn($com->id . '_group_admin_user.group_id', $g)->groupBy($com->id . '_group_admin_user.user_id')->paginate();

        }

        // 加载模板
        //
        return View::make('com/admin/user/level_customer')->with('com', $com)->with(compact('users'));
    }

    /**
     * 客服创建
     *
     * @param $com
     *
     * @return mixed
     */
    public function getLevelCustomerCreate($com)
    {
        // 获取组
        //
        $group = $this->_getCustomerGroup();

        // 加载模板
        //
        return View::make('com/admin/user/level_customer_create')->with('com', $com)->with(compact('group'));
    }

    /**
     * 客服创建处理
     *
     * @param $com
     *
     * @return mixed
     */
    public function postLevelCustomerCreate($com)
    {
        // 验证规则
        //
        $rules = array(
            // 'email'    => 'Required|Email|unique:users,user_name',
            'name'     => 'Required|min:2',
            'mobile'   => 'Required|min:8',
            'password' => 'Required|min:6',
            'position' => 'Required|min:2',
            'group_id' => 'Required',
        );

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 判断是否存在管理员信息
            $user_id = 0;
            if ($userdata = User::where('user_name', Input::get('email'))->first()) {
                if (AdminUser::where('user_id', $userdata->id)->first()) {
                    return Redirect::to($com->path . '/admin/user/level/customer')->with('error', '创建客服失败，帐号：' . e(Input::get('email')) . ' 已存在!');
                }

                $user_id = $userdata->id;
            }

            if ($user_id == 0) {
                // 初始化用户模型类
                //
                $user = new User;

                // 用户数据
                //
                $user->user_name = e(Input::get('email'));
                $user->password  = Hash::make(Input::get('password'));
                $user->name      = e(Input::get('name'));
                $user->mobile    = e(Input::get('mobile'));
                $user->head      = 0;
                $user->disabled  = 'false';

                // 保存用户数据
                //
                if ($user->save()) {
                    $user_id = $user->id;
                }
            }

            
            if ($user_id > 0)
            {
                // 初始化公司管理员模型
                //
                $admin_user = new AdminUser;

                // 公司管理员关系数据
                //
                $admin_user->company_id = $com->id;
                $admin_user->user_id    = $user_id;
                $admin_user->level      = 4;
                $admin_user->disabled   = 'false';
                $admin_user->position   = e(Input::get('position'));

                // 保存管理员关系数据
                //
                if ($admin_user->save())
                {
                    // 绑定组，循环
                    //
                    foreach (Input::get('group_id') as $g)
                    {
                        // 初始化组关系模型
                        //
                        $group_admin_user = new GroupAdminUser();

                        // 组关系绑定数据
                        //
                        $group_admin_user->group_id = $g;
                        $group_admin_user->user_id  = $user_id;
                        $group_admin_user->grade    = 2;

                        // 保存数据
                        //
                        $group_admin_user->save();
                    }

                    // 成功后转跳
                    //
                    return Redirect::to($com->path . '/admin/user/level/customer')->with('success', '创建客服成功，帐号：' . e(Input::get('email')));
                }

            } else {
                return Redirect::to($com->path . '/admin/user/level/customer')->with('error', '创建客服失败，帐号：' . e(Input::get('email')));
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 修改客服
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function getLevelCustomerEdit($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/customer')->with('error', '客服不存在');
        }


        $group = NULL;

        // 获取组
        //
        $g  = $this->_getCustomerGroup()->lists('id');
        $gu = GroupAdminUser::where('user_id', $userId)->where('grade', '1')->get()->lists('group_id');
        $gd = array_diff($g, $gu);

        // 根据对比组权限数据，获得获得组数据
        //
        if ($gd)
        {
            $group = Group::whereIn('id', $gd)->get();
        }

        // 获取详细信息
        //
        $user_info = User::find($admin_user->user_id);

        // 获取原来组
        //
        $group_admin_users = GroupAdminUser::where('user_id', $userId)->get()->lists('group_id', 'group_id');

        if ($group)
        {
            // 加载模板
            //
            return View::make('com/admin/user/level_customer_edit')->with('com', $com)->with(compact('admin_user'))->with(compact('user_info'))->with(compact('group'))->with(compact('group_admin_users'));
        }
        else
        {
            // 加载模板
            //
            return View::make('com/admin/user/level_customer_edit')->with('com', $com)->with(compact('admin_user'))->with(compact('user_info'))->with(compact('group'))->with(compact('group_admin_users'));
        }

    }

    /**
     * 修改客服处理
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function postLevelCustomerEdit($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/customer')->with('error', '客服不存在');
        }

        // 验证规则
        //
        $rules = array(
            // 'email'    => 'Required|Email|unique:users,user_name,' . $userId,
            'name'     => 'Required|min:2',
            'mobile'   => 'Required|min:8',
            'position' => 'Required|min:2',
        );

        // 组别
        //
        if (Input::get('use_group_id'))
        {
            $rules['group_id'] = 'Required';
        }

        // 如果提交更改密码
        //
        if (Input::get('password'))
        {
            // 增加验证规则
            //
            $rules['password']              = 'Required|Confirmed|min:6';
            $rules['password_confirmation'] = 'Required';
        }

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 初始化用户模型类
            //
            $user = User::find($admin_user->user_id);

            // 用户数据
            //
            $user->user_name = e(Input::get('email'));
            $user->name      = e(Input::get('name'));
            $user->mobile    = e(Input::get('mobile'));

            // 如果密码不为空
            //
            if (Input::get('password') !== '')
            {
                $user->password = Hash::make(Input::get('password'));
            }

            // 职位
            //
            $admin_user->position = e(Input::get('position'));

            // binway需求：客服可设置不需要审核发送工单邮件
            if (isset($_POST['check'])) {
                $admin_user->check = e(Input::get('check'));
            }

            // 保存用户数据
            //
            if ($user->save() && $admin_user->save())
            {

                // 初始化组关系模型
                //
                $group_admin_user = new GroupAdminUser();

                // 获取原来组
                //
                $group_admin_users = $group_admin_user->where('user_id', $userId)->get()->lists('group_id', 'group_id');

                // 提交组信息
                //
                $Groups = Input::get('group_id', array());

                // 计算数据差
                //
                $GroupsToAdd    = array_diff($Groups, $group_admin_users);
                $GroupsToRemove = array_diff($group_admin_users, $Groups);

                // 添加数据
                //
                foreach ($GroupsToAdd as $gaid)
                {
                    $group_admin_user->insert(
                        array(
                            'group_id' => $gaid,
                            'user_id'  => $userId,
                            'grade'    => '2'
                        )
                    );
                }

                // 删除数据
                //
                foreach ($GroupsToRemove as $gdid)
                {
                    $group_admin_user->where('group_id', $gdid)->where('user_id', $userId)->where('grade', '2')->delete();
                }


                // 成功后转跳
                //
                return Redirect::to($com->path . '/admin/user/level/customer')->with('success', '更新客服成功，帐号：' . e(Input::get('email')));


            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 禁用客服
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function getLevelCustomerDisabled($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/customer')->with('error', '客服不存在');
        }

        // 禁用
        //
        $admin_user->disabled = 'true';

        // 保存
        //
        if ($admin_user->save())
        {

            $user = User::find($admin_user->user_id);

            // 成功后转跳
            //
            return Redirect::to($com->path . '/admin/user/level/customer')->with('success', '禁用客服成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 启用客服
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function getLevelCustomerEnable($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/customer')->with('error', '客服不存在');
        }

        // 禁用
        //
        $admin_user->disabled = 'false';

        // 保存
        //
        if ($admin_user->save())
        {

            // 获取客户详细信息
            //
            $user = User::find($admin_user->user_id);

            // 成功后转跳
            //
            return Redirect::to($com->path . '/admin/user/level/customer')->with('success', '启用客服成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 查看客服信息
     *
     * @param      $com
     * @param null $userId
     *
     * @return mixed
     */
    public function getLevelCustomerView($com, $userId = NULL)
    {
        // 获取用户信息
        //
        if (is_null($admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $userId)->first()))
        {
            return Redirect::to($com->path . '/admin/user/level/customer')->with('error', '客服不存在');
        }

        // 获取详细信息
        //
        $user_info = User::find($admin_user->user_id);

        // 组权限信息
        //
        $group_info = GroupAdminUser::join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id')->where('user_id', $userId)->get();


        // 加载模板
        //
        return View::make('com/admin/user/level_customer_view')->with('com', $com)->with(compact('admin_user'))->with(compact('user_info'))->with(compact('group_info'));
    }

    /**
     * 获取客服组
     *
     * @return mixed
     */
    protected function _getCustomerGroup()
    {
        // 获取等级
        //
        $level = Config::get('auth.com_admin_level_id');

        // 判断权限
        //
        if ($level < 3)
        {
            // 获取组
            //
            $group = Group::all();
        }
        else
        {
            // 获取管理组
            //
            $g = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');

            // 获取组
            //
            $group = Group::whereIn('id', $g)->get();
        }

        // 返回组信息
        //
        return $group;
    }


}