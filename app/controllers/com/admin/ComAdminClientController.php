<?php

/**
 * Class ComAdminClientController
 *
 * 公司后台客户管理控制器 by cooper
 */
class ComAdminClientController extends ComAdminController {

    // 设置分页数
    // private $page = 10;
    private $pagecount = 0;

    /**
     * __construct 构造方法
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    // 分页配置
    public function page()
    {
        $_SESSION[Auth::user()->id.'_page'] = Input::get('page');
        $arr = array(
            'page' => 'true',
        );

        exit(json_encode($arr));
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
        // 分页配置
        if (isset($_SESSION[Auth::user()->id.'_page'])) {
            $page = $_SESSION[Auth::user()->id.'_page'];
        } else {
            $page = 20; // 默认20条
        }

        // 获取客户信息
        $users_data = SiteUser::where('company_id', $com->id)
        ->join('users', 'users.id', '=', 'site_user.user_id')
        ->join($com->id . '_work_type', $com->id . '_work_type.id', '=', 'site_user.work_type_id');

        // 判断是否包含搜索内容
        if (Input::get('search_type')) {
            $search_type = Input::get('search_type');
            $search_content = trim(Input::get('search_content'));
            if ($search_type == 1) {
                $users_data = $users_data->where('users.user_name', 'like', '%'.$search_content.'%');
            } else {
                $users_data = $users_data->where('users.name', 'like', '%'.$search_content.'%');
            }

            $this->pagecount = $users_data->count();
            if (isset($_GET['count'])) {
                $this->pagecount = $_GET['count'];
            }

            $count = $this->pagecount;
        }

        $users = $users_data->select('site_user.*', 'users.id', $com->id . '_work_type.work_type_name', 
        'users.user_name', 'users.name', 'users.mobile', 'users.created_at')->paginate($page);
        
        // 加载模板
        return View::make('com/admin/client/index')->with('com', $com)->with(compact('users'))
        ->with(compact('page'))->with(compact('count'))->with(compact('search_content'))->with(compact('search_type'));
    }

    /**
     * 创建客户
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

        // 获取公司信息
        //
        $firm_array = SiteUser::where('company_id', $com->id)->lists('firm');

        $firm = array_filter(array_unique($firm_array));

        // 获取所有组信息
        $groups = Group::all();
        
        // 加载模板
        //
        return View::make('com/admin/client/create')->with('com', $com)->with(compact('work_types'))
        ->with(compact('firm'))->with(compact('groups'));
    }

    /**
     * 创建客户处理
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
            'work_type_id' => 'Required',
            'group_id' => 'Required',
            'email'    => 'Required|Email',
            'name'     => 'Required|min:2',
            'mobile'   => 'Required|min:8',
            'password' => 'Required|min:6',
        );

        // 选择公司
        //
        if (Input::get('check_firm'))
        {
            $rules['firm_s'] = 'Required|min:2';
        }
        else
        {
            $rules['firm'] = 'Required|min:2';
        }

        // 验证表单信息
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证是否通过
        //
        if ($validator->passes())
        {
            $user_id = 0;

            // 查询用户数据
            //
            if ($u = User::where('user_name', e(Input::get('email')))->first())
            {
                // 判断是否添加过客户
                //
                if (SiteUser::where('user_id', $u->id)->first())
                {
                    // 添加失败转跳
                    //
                    return Redirect::to($com->path . '/admin/client')->with('error', '客户已存在，帐号：' . e(Input::get('email')));
                }
                else
                {
                    $user_id = $u->id;
                }
            }
            else
            {
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
                if ($user->save())
                {
                    $user_id = $user->id;
                }
            }

            // 判断用户信息
            //
            if ($user_id > 0)
            {
                // 初始化客户关系模型
                //
                $site_user = new SiteUser();

                // 客户关系数据
                //
                $site_user->company_id   = $com->id;
                $site_user->user_id      = $user_id;
                $site_user->work_type_id = Input::get('work_type_id');
                $site_user->disabled     = 'false';

                // 选择公司
                //
                if (Input::get('check_firm'))
                {
                    $site_user->firm = Input::get('firm_s');
                }
                else
                {
                    $site_user->firm = Input::get('firm');
                }


                // 客户关系保存
                //
                if ($site_user->save())
                {
                    // 客户组别绑定
                    $bind_user = new BindUser();
                    $bind_user->group_id = Input::get('group_id');
                    $bind_user->user_id = $user_id;
                    if ($bind_user->save()) {
                        // 成功后转跳
                        return Redirect::to($com->path . '/admin/client')->with('success', '创建客户成功，帐号：' . e(Input::get('email')));
                    }
                }
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());
    }

    /**
     * 禁用客户
     *
     * @param      $com
     * @param null $clientId
     *
     * @return mixed
     */
    public function getDisabled($com, $clientId = NULL)
    {
        // 获取客户信息
        //
        if (is_null($site_user = SiteUser::where('company_id', $com->id)->where('user_id', $clientId)->first()))
        {
            return Redirect::to($com->path . '/admin/client')->with('error', '客户不存在');
        }

        // 禁用
        //
        $site_user->disabled = 'true';

        // 保存
        //
        if ($site_user->save())
        {

            $user = User::find($site_user->user_id);

            // 成功后转跳
            //
            return Redirect::to($com->path . '/admin/client')->with('success', '禁用客户成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 查看客户信息
     *
     * @param      $com
     * @param null $clientId
     */
    public function getView($com, $clientId = NULL)
    {
        // 获取客户信息
        //
        if (is_null($site_user = SiteUser::where('company_id', $com->id)->where('user_id', $clientId)->first()))
        {
            return View::make('com/notifications')->with('error', '客户不存在');
        }

        // 获取详细信息
        //
        $user_info = User::find($site_user->user_id);

        // 获取公司信息
        $user_info->firm = $site_user->firm;

        // 获取国家信息
        if ($site_user->country == '') {
            $user_info->country = '-';
        }

        // 获取绑定信息
        $bind_info = DB::table($com->id . '_bind_user')->where('user_id', $site_user->user_id)->first();
        $groups = DB::table($com->id . '_groups')->where('id', $bind_info->group_id)->first();
        if ($bind_info->admin_id != 0) {
            $admins = DB::table('users')->where('id', $bind_info->admin_id)->first();

            $user_info->group_name = $groups->group_name;
            $user_info->admin_name = $admins->name;
        } else {
            $user_info->group_name = $groups->group_name;
            $user_info->admin_name = '-';
        }
        
        // 加载模板
        //
        return View::make('com/admin/client/view')->with('com', $com)->with(compact('site_user'))->with(compact('user_info'));

    }

    // 绑定客户信息
    public function getBind($com, $clientId = NULL)
    {
        // 获取客户信息
        if (is_null($site_user = SiteUser::where('company_id', $com->id)->where('user_id', $clientId)->first())) {
            return View::make('com/notifications')->with('error', '客户不存在');
        }

        // 获取客户当前所属组和当前负责人
        $bind_info = DB::table($com->id . '_bind_user')->where('user_id', $site_user->user_id)->first();
        
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');
        if ($level == 1 || $level == 2) {
            // 获取公司所有客服
            $groups = Group::all();
            $admin_user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->select('users.*', $com->id . '_group_admin_user.grade', $com->id . '_group_admin_user.group_id')
            ->orderBy($com->id . '_group_admin_user.group_id', 'desc')->paginate();

            foreach ($groups as $key => $value) {
                foreach ($admin_user as $keys => $values) {
                    if ($value['id'] == $values['group_id']) {
                        $adminUsers[$key]['group_id'] = $value['id'];
                        $adminUsers[$key]['group_name'] = $value['group_name'];
                        $adminUsers[$key]['users'][$values['id']]['user_id'] = $values['id'];
                        $adminUsers[$key]['users'][$values['id']]['name'] = $values['name'];
                        $adminUsers[$key]['users'][$values['id']]['grade'] = $values['grade'];

                        if ($bind_info->admin_id == $values['id'] && $bind_info->group_id == $values['group_id']) {
                            $adminUsers[$key]['users'][$values['id']]['yes'] = '1';
                        } else {
                            $adminUsers[$key]['users'][$values['id']]['yes'] = '0';
                        }
                    }
                }
            }
        } else {
            // 获取组长组别及组内成员
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            $groups = DB::table($com->id . '_groups')->whereIn('id', $group_id)->get();
            $admin_user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->select('users.*', $com->id . '_group_admin_user.grade', $com->id . '_group_admin_user.group_id')
            ->orderBy($com->id . '_group_admin_user.group_id', 'desc')->paginate();

            foreach ($groups as $key => $value) {
                foreach ($admin_user as $keys => $values) {
                    if ($value->id == $values['group_id']) {
                        $adminUsers[$key]['group_id'] = $value->id;
                        $adminUsers[$key]['group_name'] = $value->group_name;
                        $adminUsers[$key]['users'][$values['id']]['user_id'] = $values['id'];
                        $adminUsers[$key]['users'][$values['id']]['name'] = $values['name'];
                        $adminUsers[$key]['users'][$values['id']]['grade'] = $values['grade'];

                        if ($bind_info->admin_id == $values['id'] && $bind_info->group_id == $values['group_id']) {
                            $adminUsers[$key]['users'][$values['id']]['yes'] = '1';
                        } else {
                            $adminUsers[$key]['users'][$values['id']]['yes'] = '0';
                        }
                    }
                }
            }
        }

        return View::make('com/admin/client/bind')->with('com', $com)->with(compact('clientId'))
        ->with(compact('bind_info'))->with(compact('adminUsers'));
    }

    // 客户绑定处理
    public function bindManage($com, $clientId = NULL)
    {
        if ($_GET['admin_id'] != 0) {
            $admin_id_info = GroupAdminUser::where('group_id', $_GET['group_id'])->where('user_id', $_GET['admin_id'])
            ->get()->lists('user_id');

            if (count($admin_id_info) == 0) {
                return Redirect::to($com->path . '/admin/client')->with('error', '客户绑定失败，请选择同组成员！');
            }
        }
        
        $bind_user = BindUser::find($clientId);
        $bind_user->group_id = $_GET['group_id'];
        $bind_user->admin_id = $_GET['admin_id'];
        
        if ($bind_user->save()) {
            return Redirect::to($com->path . '/admin/client')->with('success', '客户绑定成功！');
        } else {
            return Redirect::to($com->path . '/admin/client')->with('error', '客户绑定失败！');
        }
    }

    // 向客户群发邮件
    public function getGroupSending($com)
    {
        // 获取公司邮箱
        $company_email = CompanyEmail::where('company_id', $com->id)->where('disabled', 'false')->paginate();

        return View::make('com/admin/client/groupsending')->with('com', $com)->with('company_email', $company_email);
    }

    // 向客户发送邮件处理
    public function postGroupSending($com)
    {
        // 判断是否带附件
        $file_status = 0; // 上传开关
        if (Input::hasFile('file')) {
            $file = Input::file('file');
            foreach ($file as $k => $v) {
                // 验证附件大小
                if ($v->getSize() > (2 * 1024 * 1024)) {
                    return Redirect::to($com->path . '/admin/client')
                    ->with('error', '上传附件过大，请把每个文件控制在2M以内！');
                }
            }

            $file_status = 1;
        }

        // 判断是否需要上传
        $fileID = array();
        if ($file_status == 1) {
            foreach ($file as $k => $v) {
                // 获得上传路径
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;

                // 获取文件类型
                $mimeType = explode('/', $v->getmimeType());
                if ($mimeType[0] == 'image') {
                    $file_type = $mimeType[0];
                } else {
                    $file_type = 'file';
                }
                
                // 文件上传处理
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;
                $extension = $v->getClientOriginalExtension();

                $client_original_name = explode('.', $v->getClientOriginalName()); // 获取文件名
                $end_name = $client_original_name[count($client_original_name)-1]; // 获取后缀
                $body_name = explode($end_name, $v->getClientOriginalName()); // 截取名称
                $start_name = rtrim($body_name[0], '.'); // 获取名称
                $ClientOriginalName = $start_name . '_' . time() . str_random(4) . '.' . $end_name;

                $upload_success = $v->move($destinationPath, $ClientOriginalName);
                if ($upload_success) {
                    $encl = new Enclosure();
                    $encl->enclosure_name = $v->getClientOriginalName();
                    $encl->type = $file_type;
                    $encl->suffix = $extension;
                    $encl->path = $destinationPath . '/' . $ClientOriginalName;
                    
                    if ($encl->save()) {
                        $fileID[] = $encl->id;
                    }
                }
            }
        }

        // 定义附件参数
        $enclosure = '';
        if (count($fileID) > 0) {
            foreach ($fileID as $k) {
                $enclosure .= $k . ',';
            }

            $enclosure = rtrim($enclosure, ',');
        }

        // 定义暗送邮箱
        $bcc = rtrim(Input::get('bcc'), ',');

        // 创建队列对象
        $quere = new OrderQuere;

        // 组合队列数据
        $quere->company_id = $com->id;
        $quere->byname = $com->byname;
        $quere->title = Input::get('title');
        $quere->from_email = DB::table('company_email')->where('id', Input::get('email_id'))->pluck('email');
        $quere->to_email = Auth::user()->user_name;
        $quere->content = Input::get('content');
        $quere->bcc = $bcc;
        $quere->enclosure = $enclosure;
        
        // 保存队列数据
        if ($quere->save()) {
            return Redirect::to($com->path . '/admin/client')->with('success', '创建群发邮件成功！');
        } else {
            return Redirect::to($com->path . '/admin/client')->with('error', '创建群发邮件失败！');
        }
    }

    // 向客户发送邮件
    public function getClientEmail($com, $clientId = NULL)
    {
        // 获取客户详细信息
        $user_info = User::find($clientId);

        // 获取组别
        $groups = Group::where('email_id', '>', 0)->get();

        // 去除未启用邮箱的组别
        foreach ($groups as $k => $v) {
            if (DB::table('company_email')->where('company_id', $com->id)
                ->where('id', $v->email_id)
                ->where('disabled', '=', 'true')->pluck('id')) {
                unset($groups[$k]);
            }
        }
        
        return View::make('com/admin/client/email')->with('com', $com)->with(compact('user_info'))->with(compact('groups'));
    }

    // 向客户发送邮件处理
    public function postClientEmail($com, $clientId = NULL)
    {
        // 获取管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取组别
        $groupID = Input::get('group');

        // 判断是否带附件
        $file_status = 0; // 上传开关
        if (Input::hasFile('file')) {
            $file = Input::file('file');
            foreach ($file as $k => $v) {
                // 验证附件大小
                if ($v->getSize() > (2 * 1024 * 1024)) {
                    return Redirect::to($com->path . '/admin/client')
                    ->with('error', '上传附件过大，请把每个文件控制在2M以内！');
                }
            }

            $file_status = 1;
        }

        // 判断回复内容是否需要审核
        $system = 0; // 审核开关
        $check = DB::table($com->id . '_groups')->where('id', $groupID)->pluck('check');
        if ($check == '1') { // 需要审核
            if ($level == 4) {
                $admin_check = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('check');
                if ($admin_check == '1') { // 判断客服是否设置为需要审核
                    $system = 1;
                }
            }
        }

        // 获取表单数据
        $data = array(
            'title' => Input::get('title'),
            'content' => Input::get('content'),
            'ask' => Input::get('member'),
            'group' => $groupID,
            'reply' => Auth::user()->id,
            'system' => $system,
        );

        // 创建工单
        $email_id = DB::table($com->id . '_groups')->where('id', $data['group'])->pluck('email_id');
        if ($email_id == '') {
            return Redirect::to($com->path . '/admin/client')->with('error', '创建工单失败，选择的组别未绑定邮箱！');
        }

        if (DB::table('company_email')->where('disabled', 'true')->where('id', $email_id)->pluck('id')) {
            return Redirect::to($com->path . '/admin/client')->with('error', '创建工单失败，组别所绑定邮箱未启用！');
        }

        $order = new Order();

        // 组合工单数据
        $order->title = $data['title'];
        $order->ask = $data['ask'];
        $order->group = $data['group'];
        $order->reply = $data['reply'];
        $order->email = $email_id;
        $order->status = 2;

        // 保存工单数据
        if ($order->save()) {
            $order_id = $order->id;
        }

        // 判断是否需要上传
        $fileID = array();
        if ($file_status == 1) {
            foreach ($file as $k => $v) {
                // 获得上传路径
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;

                // 获取文件类型
                $mimeType = explode('/', $v->getmimeType());
                if ($mimeType[0] == 'image') {
                    $file_type = $mimeType[0];
                } else {
                    $file_type = 'file';
                }
                
                // 文件上传处理
                $destinationPath = 'uploads/' . $com->byname . '/reply/' . date('Y/m/d/') . Auth::user()->id;
                $extension = $v->getClientOriginalExtension();

                $client_original_name = explode('.', $v->getClientOriginalName()); // 获取文件名
                $end_name = $client_original_name[count($client_original_name)-1]; // 获取后缀
                $body_name = explode($end_name, $v->getClientOriginalName()); // 截取名称
                $start_name = rtrim($body_name[0], '.'); // 获取名称
                $ClientOriginalName = $start_name . '_' . time() . str_random(4) . '.' . $end_name;

                $upload_success = $v->move($destinationPath, $ClientOriginalName);
                if ($upload_success) {
                    $encl = new Enclosure();
                    $encl->enclosure_name = $v->getClientOriginalName();
                    $encl->type = $file_type;
                    $encl->suffix = $extension;
                    $encl->path = $destinationPath . '/' . $ClientOriginalName;
                    $encl->reply = Auth::user()->id;
                    $encl->order = $order_id;

                    if ($encl->save()) {
                        $fileID[] = $encl->id;
                    }
                }
            }
        }

        // 定义附件参数
        $reply_enclosure = '';
        if (count($fileID) > 0) {
            foreach ($fileID as $k) {
                $reply_enclosure .= $k . ',';
            }

            $reply_enclosure = rtrim($reply_enclosure, ',');
        }

        // 创建回答对象
        $reply = new OrderReply;
        
        // 组合回答数据
        $reply->order = $order_id;
        $reply->reply = $data['reply'];
        $reply->content = $data['content'];
        $reply->system = $data['system'];
        $reply->enclosure = $reply_enclosure;
        $reply->email = $email_id;

        // 保存回答数据
        if ($reply->save()) {
            $reply_id = $reply->id;

            // 判断是否带备注内容
            if (Input::get('remark_content') != '') {
                $remark_content = str_replace("\r\n", "<br>", Input::get('remark_content'));

                // 创建工单备注对象
                $remark = new OrderRemark;
                
                // 组合工单备注数据
                $remark->order_reply = $reply_id;
                $remark->order = $order_id;
                $remark->admin = Auth::user()->id;
                $remark->content = $remark_content;

                // 保存工单备注数据
                $remark->save();
            }
        }

        $e_config = 0;
        if ($email_id > 0) {
            $from_email = DB::table('company_email')->where('id', $email_id)->pluck('email');
            $cc = rtrim(Input::get('cc'), ',');

            // 创建队列对象
            $quere = new OrderQuere;

            // 组合队列数据
            $quere->company_id = $com->id;
            $quere->byname = $com->byname;
            $quere->order = $order_id;
            $quere->reply = $reply->id;
            $quere->title = '#'.$order_id.': '.$data['title'];
            $quere->from_email = $from_email;
            $quere->to_email = DB::table('users')->where('id', $data['ask'])->pluck('user_name');
            $quere->content = $data['content'];
            $quere->process = $data['system'];
            $quere->enclosure = $reply_enclosure;
            if ($cc != '') {
                $quere->cc = $cc;
            }

            // 保存队列数据
            if ($quere->save()) {
                $e_config = 0;
            } else {
                $e_config = 1;
            }

            // 创建工单日志对象
            $log = new OrderLog;

            // 组合工单日志数据
            $log->order = $order_id;
            $log->admin = Auth::user()->id;
            $log->content = '创建工单并发送回复消息';

            // 保存工单日志
            if ($log->save()) {
                $e_config = 0;
            } else {
                $e_config = 1;
            }
        }

        // 加载模板
        if ($e_config == 0) {
            return Redirect::to($com->path . '/admin/client')->with('success', '创建工单成功！');
        } else {
            return Redirect::to($com->path . '/admin/client')->with('error', '创建工单失败！');
        }
    }

    /**
     * 启用客户
     *
     * @param      $com
     * @param null $clientId
     *
     * @return mixed
     */
    public function getEnable($com, $clientId = NULL)
    {
        // 获取客户信息
        //
        if (is_null($site_user = SiteUser::where('company_id', $com->id)->where('user_id', $clientId)->first()))
        {
            return Redirect::to($com->path . '/admin/client')->with('error', '客户不存在');
        }

        // 禁用
        //
        $site_user->disabled = 'false';

        // 保存
        //
        if ($site_user->save())
        {

            // 获取客户详细信息
            //
            $user = User::find($site_user->user_id);

            // 成功后转跳
            //
            return Redirect::to($com->path . '/admin/client')->with('success', '启用客户成功，帐号：' . $user->user_name);
        }
    }

    /**
     * 更改客户密码及信息
     *
     * @param      $com
     * @param null $clientId
     *
     * @return mixed
     */
    public function getChangePassword($com, $clientId = NULL)
    {
        // 获取客户信息
        //
        if (is_null($site_user = SiteUser::where('company_id', $com->id)->where('user_id', $clientId)->first()))
        {
            return Redirect::to($com->path . '/admin/client')->with('error', '客户不存在');
        }

        // 获取详细信息
        //
        $user_info = User::find($site_user->user_id);

        // 获取业务形态
        //
        $work_types = WorkType::all();

        // 获取公司信息
        //
        $firm_array = SiteUser::where('company_id', $com->id)->lists('firm');

        $firm = array_filter(array_unique($firm_array));
        
        // 加载模板
        //
        return View::make('com/admin/client/changepassword')->with('com', $com)->with(compact('site_user'))
        ->with(compact('user_info'))->with(compact('work_types'))->with(compact('firm'));
    }

    /**
     * 更改客户密码及信息处理
     *
     * @param      $com
     * @param null $clientId
     *
     * @return mixed
     */
    public function postChangePassword($com, $clientId = NULL)
    {
        // 获取客户信息
        //
        if (is_null($site_user = SiteUser::where('company_id', $com->id)->where('user_id', $clientId)->first()))
        {
            return Redirect::to($com->path . '/admin/client')->with('error', '客户不存在');
        }

        // 验证规则
        //
        $rules = array(
            'name'     => 'Required|min:2',
            'mobile'   => 'Required|min:8',
        );

        // 选择公司
        //
        if (Input::get('check_firm'))
        {
            $rules['firm_s'] = 'Required|min:2';
        }
        else
        {
            $rules['firm'] = 'Required|min:2';
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

        // 验证
        //
        $validator = Validator::make(Input::all(), $rules);

        // 验证通过
        //
        $user_info = User::find($site_user->user_id);
        $user_info->name   = e(Input::get('name'));
        $user_info->mobile = e(Input::get('mobile'));
        if ($validator->passes()) {
            // 新密码
            //
            $user_info->password = Hash::make(Input::get('password'));
        }

        // 保存
        //
        if ($user_info->save()) {
            $site_user = SiteUser::find(Input::get('suid'));

            // 选择公司
            //
            if (Input::get('check_firm'))
            {
                $site_user->firm = Input::get('firm_s');
            }
            else
            {
                $site_user->firm = Input::get('firm');
            }

            $site_user->work_type_id = e(Input::get('work_type_id'));

            if ($site_user->save()) {
                // 提示修改信息
                //
                return Redirect::to($com->path . '/admin/client')->with('success', '资料更新成功!');
            }
        }

        // 返回错误信息
        //
        Return Redirect::back()->withInput(Input::all())->withErrors($validator->getMessageBag());

    }

    // 客户导入
    public function getImportClient($com)
    {
        // 加载模板
        //
        return View::make('com/admin/client/import')->with('com', $com);
    }

    // 客户导入处理
    public function postImportClient($com)
    {
        $csv = $_FILES['csv']; // 获取导入文件
        $handle = fopen($csv['tmp_name'], 'r'); // 打开文件流
        $result = $this->input_csv($handle); // 读取文件内容
        
        // 导入内容验证
        $status = $this->checkImportClient($com, $result);
        if ($status == 'true') {
            $ins = $this->importClientDB($com, $result); // 执行导入进数据库
            fclose($handle); // 关闭文件流

            if ($ins) {
                return Redirect::to($com->path . '/admin/client')->with('success', '客户导入成功！');
            } else {
                return Redirect::to($com->path . '/admin/client')->with('error', '客户导入失败！');
            }
        } else {
            fclose($handle); // 关闭文件流
            return Redirect::to($com->path . '/admin/client')->with('error', '客户导入验证失败！失败原因：' . $status);
        }
    }

    // 客户导入验证
    public function checkImportClient($com, $result)
    {
        $status = 'true'; // 定义导入开关
        if (count($status) == 0) {
            $status = 'CSV文件内没有任何数据！';
        } else {
            foreach ($result as $k => $v) {
                // 业务类型验证
                if (!DB::table($com->id . '_work_type')->where('work_type_name', $v[0])->pluck('id')) {
                    $num = $k + 1; // 数组下标＋1
                    $status = '第' . $num . '行中，业务类型不存在！';
                    break;
                }

                // 组别验证
                if (!DB::table($com->id . '_groups')->where('group_name', $v[1])->pluck('id')) {
                    $num = $k + 1;
                    $status = '第' . $num . '行中，所属组别不存在！';
                    break;
                }

                // 邮箱必填验证
                if (!$v[2]) {
                    $num = $k + 1;
                    $status = '第' . $num . '行中，用户名(邮箱)不能为空！';
                    break;
                }

                // 邮箱格式验证
                if (!preg_match('/.+@.++$/', $v[2])) {
                    $num = $k + 1;
                    $status = '第' . $num . '行中，用户名(邮箱)格式不正确！';
                    break;
                }

                // 邮箱是否存在验证
                if ($check_id = DB::table('users')->where('user_name', $v[2])->pluck('id')) {
                    if (DB::table('site_user')->where('company_id', $com->id)->where('user_id', $check_id)->pluck('id')) {
                        $num = $k + 1;
                        $status = '第' . $num . '行中，用户名(邮箱)已经存在，请重新输入！';
                        break;
                    }
                }

                // 姓名验证
                if (!$v[3] || strlen($v[3]) < 2) {
                    $num = $k + 1;
                    $status = '第' . $num . '行中，客户姓名不能为空且必须大于等于2位！';
                    break;
                }

                // 电话验证
                if (!$v[4] || strlen($v[4]) < 8) {
                    $num = $k + 1;
                    $status = '第' . $num . '行中，客户电话不能为空且必须大于等于8位！';
                    break;
                }

                // 公司验证
                if (!$v[5] || strlen($v[5]) < 2) {
                    $num = $k + 1;
                    $status = '第' . $num . '行中，客户电话不能为空且必须大于等于2位！';
                    break;
                }

                // 密码验证
                if (!$v[6] || strlen($v[6]) < 6) {
                    $num = $k + 1;
                    $status = '第' . $num . '行中，客户电话不能为空且必须大于等于6位！';
                    break;
                }
            }
        }

        return $status;
    }

    // 客户导入数据库
    public function importClientDB($com, $result)
    {
        foreach ($result as $k => $v) {
            // 判断用户是否存在
            if ($check_id = DB::table('users')->where('user_name', $v[2])->pluck('id')) {
                $user_id = $check_id;
            } else {
                // 初始化用户模型类
                $user = new User;
                $user->user_name = $v[2];
                $user->password  = Hash::make($v[6]);
                $user->name = $v[3];
                $user->mobile = $v[4];
                $user->head = 0;
                $user->disabled = 'false';

                if ($user->save()) {
                    $user_id = $user->id;
                }
            }

            // 初始化客户关系模型
            $work_type_id = DB::table($com->id . '_work_type')->where('work_type_name', $v[0])->pluck('id');
            $site_user = new SiteUser();
            $site_user->company_id = $com->id;
            $site_user->user_id = $user_id;
            $site_user->work_type_id = $work_type_id;
            $site_user->firm = $v[5];
            $site_user->disabled = 'false';

            if ($site_user->save()) {
                // 客户组别绑定
                $group_id = DB::table($com->id . '_groups')->where('group_name', $v[1])->pluck('id');
                $bind_user = new BindUser();
                $bind_user->group_id = $group_id;
                $bind_user->user_id = $user_id;
                if ($bind_user->save()) {
                    $ins = true;
                }
            }
        }
        
        return $ins;
    }

    // CSV导入
    private function input_csv($handle) {
        $out = array();
        $n = 0;
        while ($data = fgetcsv($handle, 1000, ',')) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }

            $n++;
        }

        return $out;
    }
}
