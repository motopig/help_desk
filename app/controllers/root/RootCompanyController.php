<?php

/**
 * Class RootCompanyController
 *
 * 超级管理员公司管理控制器 by cooper
 */
class RootCompanyController extends RootController {

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
        $company = Company::orderBy('created_at', 'desc')->paginate();

        // 加载模板
        //
        return View::make('root/company/index')->with(compact('company'));
    }

    /**
     * 创建公司
     *
     * @return mixed
     */
    public function getCreate()
    {
        // 加载模板
        //
        return View::make('root/company/create');
    }

    /**
     * 创建公司处理
     *
     * @return mixed
     */
    public function postCreate()
    {
        // 验证规则
        //
        $rules = array(
            'company_name' => 'Required|min:3|unique:companys,company_name',
            'mobile'       => 'Required|min:8',
            'address'      => 'Required|min:5',
            'path'         => 'Required|alpha_num|unique:companys,path',
            'email'        => 'Required|Email|unique:users,user_name',
            'name'     => 'Required|min:2',
            'mobile_u'     => 'Required|min:8',
            'password'     => 'Required|min:6',
            'position' => 'Required|min:2'
        );

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 初始化公司模型类
            //
            $company = new Company;

            // 分配数据公司
            //
            $company->company_name = e(Input::get('company_name'));
            $company->mobile       = e(Input::get('mobile'));
            $company->address      = e(Input::get('address'));
            $company->path         = e(Input::get('path'));
            $company->logo         = 0;
            $company->brief        = e(Input::get('brief'));
            $company->disabled     = 'false';
            // 生成公司别名
            $company->byname = str_random(8);

            // 保存数据
            //
            if ($company->save())
            {
                // 初始化用户模型类
                //
                $user = new User;

                // 用户数据
                //
                $user->user_name = e(Input::get('email'));
                $user->password  = Hash::make(Input::get('password'));
                $user->name      = e(Input::get('name') );
                $user->mobile    = e(Input::get('mobile_u'));
                $user->head      = 0;
                $user->disabled  = 'false';

                // 判断用户
                //
                if ($user->save())
                {
                    // 初始化公司管理员模型
                    //
                    $admin_user = new AdminUser;

                    // 公司管理员关系数据
                    //
                    $admin_user->company_id = $company->id;
                    $admin_user->user_id    = $user->id;
                    $admin_user->level      = 1;
                    $admin_user->disabled   = 'false';
                    $admin_user->position   = e(Input::get('position'));

                    // 保存公司管理员数据
                    //
                    if ($admin_user->save())
                    {
                        // 获取表前缀
                        //
                        $TablePrefix = Schema::getConnection()->getTablePrefix();

                        // 公司表前缀
                        //
                        $COM_TablePrefix = $TablePrefix . $company->id . '_';

                        // 公司私有表
                        //
                        $com_tables = array(
                            'groups',
                            'group_admin_user',
                            'work_ask',
                            'work_claim',
                            'work_order',
                            'work_reply',
                            'work_time',
                            'work_type',
                            'pendant',
                            'bind_user',
                            'user_quere',
                            'staff_quere',
                            'leader_quere',
                            'manager_quere',
                            'ceo_quere',
                            'check_log',
                            'work_message',
                            'work_ask_message',
                            'work_reply_message',
                            'order',
                            'order_level',
                            'order_ask',
                            'order_reply',
                            'order_remark',
                            'order_draft',
                            'order_quere',
                            'order_message',
                            'order_log',
                            'order_invite',
                        );

                        // 循环创建私有表
                        //
                        foreach ($com_tables as $t)
                        {
                            DB::statement("create table `" . $COM_TablePrefix . $t . "` like `" . $TablePrefix . $t . "`");
                        }

                        // 成功后转跳
                        //
                        return Redirect::to('root/company')->with('success', '创建' . $company->company_name . '成功 ! 管理员帐号：' . e(Input::get('email')));
                    }
                }
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 修改公司
     *
     * @param null $companyId
     *
     * @return mixed
     */
    public function getEdit($companyId = NULL)
    {
        // 获取公司信息
        //
        if (is_null($com = Company::find($companyId)))
        {
            return Redirect::to('root/company')->with('error', '公司不存在');
        }

        // 加载模板
        //
        return View::make('root/company/edit')->with(compact('com'));
    }

    /**
     * 修改公司处理
     *
     * @param null $companyId
     *
     * @return mixed
     */
    public function postEdit($companyId = NULL)
    {
        // 获取公司信息
        //
        if (is_null($com = Company::find($companyId)))
        {
            return Redirect::to('root/company')->with('error', '公司不存在');
        }

        // 验证规则
        //
        $rules = array(
            'company_name' => 'Required|min:3|unique:companys,company_name,' . $companyId,
            'mobile'       => 'Required|min:8',
            'address'      => 'Required|min:5',
            'path'         => 'Required|alpha_num|unique:companys,path,' . $companyId
        );

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            // 分配数据公司
            //
            $com->company_name = e(Input::get('company_name'));
            $com->mobile       = e(Input::get('mobile'));
            $com->address      = e(Input::get('address'));
            $com->path         = e(Input::get('path'));
            $com->brief        = e(Input::get('brief'));

            // 保存数据
            //
            if ($com->save())
            {
                // 成功后转跳
                //
                return Redirect::to('root/company')->with('success', '修改' . $com->company_name . '成功 ! ');
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 禁用公司
     *
     * @param null $companyId
     *
     * @return mixed
     */
    public function getDisabled($companyId = NULL)
    {
        // 获取公司信息
        //
        if (is_null($com = Company::find($companyId)))
        {
            return Redirect::to('root/company')->with('error', '公司不存在');
        }

        // 禁用
        //
        $com->disabled = 'true';

        // 保存
        //
        if ($com->save())
        {
            // 成功后转跳
            //
            return Redirect::to('root/company')->with('success', '禁用公司成功，公司：' . $com->company_name);
        }
    }

    /**
     * 启用用户
     *
     * @param null $userId
     *
     * @return mixed
     */
    public function getEnable($companyId = NULL)
    {
        // 获取公司信息
        //
        if (is_null($com = Company::find($companyId)))
        {
            return Redirect::to('root/company')->with('error', '公司不存在');
        }

        // 禁用
        //
        $com->disabled = 'false';

        // 保存
        //
        if ($com->save())
        {
            // 成功后转跳
            //
            return Redirect::to('root/company')->with('success', '启用公司成功，公司：' . $com->company_name);
        }
    }

}
