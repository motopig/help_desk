<?php

/**
 * Class ComAdminGroupController
 *
 * 公司后台组别管理控制器 by cooper
 */
class ComAdminGroupController extends ComAdminController {

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
        // 获取所有组
        $group = Group::join($com->id . '_work_type', $com->id . '_work_type.id', '=', $com->id . '_groups.work_type_id')
        ->select($com->id . '_groups.*', $com->id . '_work_type.work_type_name')->orderBy($com->id . '_groups.id', 'desc')
        ->paginate();

        // 加载模板
        //
        return View::make('com/admin/group/index')->with('com', $com)->with(compact('group'));
    }

    /**
     * 创建组
     *
     * @param $com
     *
     * @return mixed
     */
    public function getCreate($com)
    {
        // 获取业务形态
        //
        $work_types = WorkType::all();

        // 获取当前所有客服
        //
        // $user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')->groupBy('user_id')->get();
        $gau = GroupAdminUser::distinct()->get()->lists('user_id');
        $user = DB::table('users')->whereIn('id', $gau)->get();

        // 获取公司邮箱
        //
        $company_email = CompanyEmail::where('company_id', $com->id)->paginate();

        // 加载模板
        //
        return View::make('com/admin/group/create')->with('com', $com)->with(compact('work_types'))->with(compact('user'))->with(compact('company_email'));
    }

    /**
     * 创建组处理
     *
     * @param $com
     *
     * @return mixed
     */
    public function postCreate($com)
    {
        // 验证规则
        //
        $rules = array(
            'group_name'   => 'Required|min:3|unique:' . $com->id . '_groups,group_name',
            // 'mobile'       => 'Required|min:8',
            // 'address'      => 'Required|min:5',
            // 'work_type_id' => 'Required|unique:' . $com->id . '_groups,work_type_id',
        );

        // 未选择已有用户
        //
        if (!Input::get('check_user'))
        {
            $rules['email']    = 'Required|Email|unique:users,user_name';
            $rules['name']     = 'Required|min:2';
            $rules['mobile_u'] = 'Required|min:8';
            $rules['position'] = 'Required|min:2';
            $rules['password'] = 'Required|min:6';
        }

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 组模型
            //
            $group = new Group;

            // 组数据
            //
            $group->group_name   = e(Input::get('group_name'));
            // $group->mobile       = e(Input::get('mobile'));
            // $group->address      = e(Input::get('address'));
            $group->mobile       = '0';
            $group->address      = '0';
            $group->brief        = e(Input::get('brief'));
            $group->work_type_id = e(Input::get('work_type_id'));
            $group->disabled     = 'false';
            // $group->email_id = e(Input::get('email_id'));

            // 保存数据
            //
            if ($group->save())
            {

                // 初始化用户模型类
                //
                $user = new User;

                // 选择已有用户
                //
                if (Input::get('check_user'))
                {
                    $user_id = Input::get('user_id');

                    $user_info = User::find($user_id);

                    $user_name = $user_info->user_name;
                }
                else
                {
                    // 用户数据
                    //
                    $user->user_name = e(Input::get('email'));
                    $user->password  = Hash::make(Input::get('password'));
                    $user->name      = e(Input::get('name'));
                    $user->mobile    = e(Input::get('mobile_u'));
                    $user->head      = 0;
                    $user->disabled  = 'false';

                    $user_name = e(Input::get('email'));

                    // 保存用户数据
                    //
                    if ($user->save())
                    {
                        $user_id = $user->id;
                    }
                }

                // 判断用户
                //
                if ($user_id > 0)
                {
                    // 初始化组关系模型
                    //
                    $group_admin_user = new GroupAdminUser();

                    // 组关系绑定数据
                    //
                    $group_admin_user->group_id = $group->id;
                    $group_admin_user->user_id  = $user_id;
                    $group_admin_user->grade    = 1;

                    // 初始化公司管理员模型
                    //
                    $admin_user = new AdminUser;

                    // 公司管理员关系数据
                    //
                    $admin_user->company_id = $com->id;
                    $admin_user->user_id    = $user_id;
                    $admin_user->level      = 3;
                    $admin_user->disabled   = 'false';
                    $admin_user->position   = e(Input::get('position'));

                    // 保存组管理员关系数据
                    //
                    if ($group_admin_user->save() && $admin_user->save())
                    {
                        // 成功后转跳
                        //
                        return Redirect::to($com->path . '/admin/group')->with('success', '创建' . $group->group_name . '成功 ! 管理员帐号：' . $user_name);
                    }
                }
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 组修改
     *
     * @param      $com
     * @param null $groupId
     *
     * @return mixed
     */
    public function getEdit($com, $groupId = NULL)
    {
        // 获取组信息
        //
        if (is_null($group = Group::find($groupId)))
        {
            return Redirect::to($com->path . '/admin/group')->with('error', '组不存在');
        }

        // 获取业务形态
        //
        $work_types = WorkType::all();

        // 获取当前所有客服
        //
        // $user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')->groupBy('user_id')->orderBy('grade')->get();
        $gau = GroupAdminUser::distinct()->get()->lists('user_id');
        $user = DB::table('users')->whereIn('id', $gau)->get();
        
        // 获取当前邮箱组长
        $now_user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
        ->where($com->id . '_group_admin_user.group_id', '=', $groupId)->first();
        
        // 获取公司邮箱
        //
        $company_email = CompanyEmail::where('company_id', $com->id)->paginate();

        // 判断公司邮是否绑定组别
        // 
        $group_email = CompanyEmail::find($group->email_id);
        if (count($group_email) > 0) {
            $group_email = 'true';
        } else {
            $group_email = 'false';
        }

        // 加载模板
        //
        return View::make('com/admin/group/edit')->with('com', $com)->with(compact('work_types'))->with(compact('now_user'))
        ->with(compact('user'))->with(compact('group'))->with(compact('company_email'))->with(compact('group_email'));
    }

    /**
     * 组修改 处理
     *
     * @param      $com
     * @param null $groupId
     */
    public function postEdit($com, $groupId = NULL)
    {
        // 获取组信息
        //
        if (is_null($group = Group::find($groupId)))
        {
            return Redirect::to($com->path . '/admin/group')->with('error', '组不存在');
        }

        // 验证规则
        //
        $rules = array(
            'group_name'   => 'Required|min:3|unique:' . $com->id . '_groups,group_name,' . $groupId,
            // 'mobile'       => 'Required|min:8',
            // 'address'      => 'Required|min:5',
            // 'work_type_id' => 'Required|unique:' . $com->id . '_groups,work_type_id,' . $groupId,
        );

        // 选择创建新祖长
        //
        if (Input::get('check_user'))
        {
            $rules['email']    = 'Required|Email|unique:users,user_name';
            $rules['name']     = 'Required|min:2';
            $rules['mobile_u'] = 'Required|min:8';
            $rules['position'] = 'Required|min:2';
            $rules['password'] = 'Required|min:6';
        }

        // 验证信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证失败
        //
        if ($validator->fails())
        {
            // 返回错误信息
            //
            Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
        }

        // 组数据
        //
        $group->group_name   = e(Input::get('group_name'));
        $group->mobile       = e(Input::get('mobile'));
        $group->address      = e(Input::get('address'));
        $group->brief        = e(Input::get('brief'));
        $group->work_type_id = e(Input::get('work_type_id'));
        $group->disabled     = 'false';
        // $group->email_id = e(Input::get('email_id'));
        $group->check        = e(Input::get('check')); // 工单审核开关设置 - no

        // 保存数据
        //
        if ($group->save())
        {

            // 初始化用户模型类
            //
            $user = new User;

            // 选择已有用户
            //
            if (!Input::get('check_user'))
            {
                $user_id = Input::get('user_id');
            }
            else
            {
                // 用户数据
                //
                $user->user_name = e(Input::get('email'));
                $user->password  = Hash::make(Input::get('password'));
                $user->name      = e(Input::get('name'));
                $user->mobile    = e(Input::get('mobile_u'));
                $user->head      = 0;
                $user->disabled  = 'false';

                // 保存用户数据
                //
                if ($user->save())
                {
                    $user_id = $user->id;
                }
            }

            // 判断用户
            //
            if ($user_id > 0)
            {

                // 查询当前组长信息
                //
                $gu = GroupAdminUser::where('group_id', $group->id)->where('grade', '1')->first();

                // 如果不是当前组长
                //
                if ($gu && ($user_id != $gu->user_id))
                {
                    // 现任组长降权为客服
                    //
                    $gu->grade = 2;

                    $gu->save();

                    // 检查新组长是否为当前组客服
                    //
                    if ($gn = GroupAdminUser::where('group_id', $group->id)->where('user_id', $user_id)->where('grade', '2')->first())
                    {
                        $gn->grade = 1;
                        $gn->save();
                    }

                    // 查询是老组长否在其他组担任组长
                    //
                    $g = GroupAdminUser::where('user_id', $gu->user_id)->where('grade', '1')->get();

                    // 如果没有担任其他组长降权系统权限
                    //
                    if ($g->count() == 0)
                    {
                        $au        = AdminUser::where('company_id', $com->id)->where('user_id', $gu->user_id)->first();
                        $au->level = 4;

                        $au->save();
                    }
                }

                // 初始化组关系模型
                //
                $group_admin_user = GroupAdminUser::where('group_id', $group->id)->where('grade', '1')->first();

                // 组关系绑定数据
                //
                if ($group_admin_user)
                {
                    $group_admin_user->user_id = $user_id;
                }
                else
                {
                    $group_admin_user           = new GroupAdminUser();
                    $group_admin_user->group_id = $group->id;
                    $group_admin_user->grade    = '1';
                    $group_admin_user->user_id  = $user_id;
                }

                // 选择创建新用户
                //
                if (!Input::get('check_user'))
                {

                    // 初始化公司管理员模型
                    //
                    $admin_user = AdminUser::where('company_id', $com->id)->where('user_id', $user_id)->first();

                    // 公司管理员关系数据
                    //
                    $admin_user->level = 3;
                }
                else
                {
                    // 初始化公司管理员模型
                    //
                    $admin_user = new AdminUser;

                    // 公司管理员关系数据
                    //
                    $admin_user->company_id = $com->id;
                    $admin_user->user_id    = $user_id;
                    $admin_user->level      = 3;
                    $admin_user->disabled   = 'false';
                    $admin_user->position   = e(Input::get('position'));
                }

                // 保存组管理员关系数据
                //
                if ($group_admin_user->save() && $admin_user->save())
                {
                    // 成功后转跳
                    //
                    return Redirect::to($com->path . '/admin/group')->with('success', '修改' . $group->group_name . '成功 ! ');
                }
            }
        }

    }

}