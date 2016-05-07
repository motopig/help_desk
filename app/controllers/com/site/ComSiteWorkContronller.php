<?php
/**
 * Class ComSiteWorkController
 *
 * 前台工单主控制器 by no
 */
class ComSiteWorkController extends ComSiteContronller {
    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();
    }

    // 设置工单分页数
    private $page = 9;

    // 二维数组按条件排序 － no
    public function array_sort_work($arr, $keys, $type = 'asc') { 
        $keysvalue = $new_array = array();

        foreach ($arr as $k=>$v) {
            $keysvalue[$k] = $v->$keys;
        }

        if($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }

        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){ 
            $new_array[$k] = $arr[$k];
        }

        return $new_array; 
    }

    // 提交工单
    public function getJobWork($com)
    {
        return View::make('com/site/work/job')->with('com', $com);
    }

    // 提交工单处理
    public function postJobWork($com)
    {
        // 验证标题
        $rules = array(
            'title' => 'required|max:20',
            'ask' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->with('error', '请输入标题(限制20个字符)和内容！');
        }

        // 获得业务类型
        $work_type_id = DB::table('site_user')->where('user_id', Auth::user()->id)->pluck('work_type_id');
        // 获得组别
        // $group_id = DB::table($com->id . '_groups')->where('work_type_id', $work_type_id)->pluck('id');
        $group_id = DB::table($com->id . '_bind_user')->where('user_id', Auth::user()->id)->pluck('group_id');
        // 获得绑定组员
        $admin_id = DB::table($com->id . '_bind_user')->where('user_id', Auth::user()->id)->pluck('admin_id');
        if (!isset($group_id)) {
            return Redirect::back()->with('error', '工单负责组不存在，请联系管理员！');
        }

        // 验证是否上传附件
        if (Input::hasFile('file')) {
            $file = Input::file('file');
            // 验证附件大小
            if ($file->getSize() > (1024 * 1024)) {
                return Redirect::back()->with('error', '上传附件过大，请控制在1M以内！');
            }

            // 验证附件后缀
            $suffixName = explode('.', $file->getClientOriginalName());
            if ($suffixName['1'] != 'docx' && $suffixName['1'] != 'xlsx' && $suffixName['1'] != 'txt' 
                && $suffixName['1'] != 'png' && $suffixName['1'] != 'jpg' && $suffixName['1'] != 'gif' 
                && $suffixName['1'] != 'bmp' && $suffixName['1'] != 'jpeg') {
                return Redirect::back()->with('error', '文件格式必须为docx、xlsx、txt；图片格式必须为png、jpg、jpeg、gif或bmp！');
            }

            // $destinationPath = 'uploads/' . $com->id . '/' . date('Y/m/d');
            $destinationPath = 'uploads/' . $com->byname . '/' . date('Y/m/d');
            $extension = $file->getClientOriginalExtension();
            $filename = str_random(8) . '.' . $extension;
            $upload_success = $file->move($destinationPath, $filename);
            if ($upload_success) {
                $istr = explode(".", $filename);
                $encl = new Enclosure();
                $encl->enclosure_name = $file->getClientOriginalName();
                // 判断附件类型
                if ($suffixName['1'] == 'docx' || $suffixName['1'] == 'xlsx' || $suffixName['1'] == 'txt' ) {
                    $encl->type = 'file';
                } else {
                    $encl->type = 'image';
                }
                $encl->suffix = $file->getClientOriginalExtension();
                $encl->path = $destinationPath . '/' . $filename;

                if ($encl->save()) {
                    $ask_enclosure_id = $encl->id;
                }
            } else {
                return Redirect::back()->with('error', '上传附件失败！');
            }
        } else {
            $ask_enclosure_id = '0';
        }

        // 创建工单
        $job = new WorkOrder;
        $job->user_id  = Auth::user()->id;
        $job->group_id = $group_id;
        $job->admin_id = $admin_id;
        $job->work_type_id = $work_type_id;
        $job->work_level = e(Input::get('work_level'));
        $job->title = e(Input::get('title'));

        if ($job->save()) {
            $ask = new WorkAsk;
            $ask->work_order_id = $job->id;
            $ask->user_id = Auth::user()->id;
            // 将换行符转换成<br />
            $askContent = e(Input::get('ask'));
            $ask_content = str_replace("\r\n", "<br>", $askContent);
            $ask->ask = $ask_content;
            $ask->enclosure_id = $ask_enclosure_id;
            $ask->ask_anchor = '';
            $ask->system = 0;

            if ($ask->save()) {
                // 工单通知信息处理 － no
                $work_message = new WorkMessage;
                $work_message->work_order_id = $job->id;
                $work_message->work_status = '1';
                $work_message->type = 'ask';
                $work_message->ask_id = $ask->id;
                $work_message->user_id = Auth::user()->id;
                $work_message->admin_id = $admin_id;
                $work_message->save();

                // 客户发起内容接收邮件队列
                // 获得公司路由
                $path = Config::get('app.com_path');
                $this->quereUser($com, $ask->id, $admin_id, $group_id, $ask_content, $path, $job->id);

                // 获取公司工单总数
                $workCount = $this->workCounts($com);

                // 获取待受理工单列表
                if ($workCount['1'] > 0) {
                    $workerOrder = $this->workStatusIndex($com, $this->page);
                } else {
                    $workerOrder = '';
                }

                // 工单最新回复排序 - no
                if ($workerOrder == '') {
                    $workerOrders = '';
                } else {
                    $workList = $workerOrder->toArray(); // 对象转为数组
                    $workerOrders = $this->array_sort_work($workList['data'], 'time', 'desc'); // 最新时间倒叙
                }
                
                return View::make('com/site/work/index')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('workerOrders'));
            } else {
                return Redirect::back()->with('error', '创建工单失败！');
            }
        } else {
            return Redirect::back()->with('error', '创建工单失败！');
        }
    }

    // 获取工单总数
    public function workCounts($com)
    {
        // 待受理工单总数
        $workCount['1'] = DB::table($com->id . '_work_order')->where('user_id', '=', Auth::user()->id)
        ->where('work_status', '=', '1')->count();

        // 处理中工单总数
        $workCount['2'] = DB::table($com->id . '_work_order')->where('user_id', '=', Auth::user()->id)
        ->where('work_status', '=', '2')->count();

        // 已处理工单总数
        $workCount['3'] = DB::table($com->id . '_work_order')->where('user_id', '=', Auth::user()->id)
        ->where('work_status', '=', '3')->count();

        // 已关闭工单总数
        $workCount['4'] = DB::table($com->id . '_work_order')->where('user_id', '=', Auth::user()->id)
        ->where('work_status', '=', '4')->count();

        return $workCount;
    }

    // 待受理工单列表(工单首页)
    public function getWork($com)
    {
        // 获取公司工单总数
        $workCount = $this->workCounts($com);

        // 获取待受理工单列表
        if ($workCount['1'] > 0) {
            $workerOrder = $this->workStatusIndex($com, $this->page);
        } else {
            $workerOrder = '';
        }

        // 工单最新回复排序 - no
        if ($workerOrder == '') {
            $workerOrders = '';
        } else {
            $workList = $workerOrder->toArray(); // 对象转为数组
            $workerOrders = $this->array_sort_work($workList['data'], 'time', 'desc'); // 最新时间倒叙
        }

        return View::make('com/site/work/index')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('workerOrders'));
    }

    // 待受理工单列表获取处理
    public function workStatusIndex($com, $page)
    {
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '1')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
        $com->id . '_groups.group_name')->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        $wId = WorkOrder::where('work_status', '1')->get()->lists('id');
        if(is_array($wId)) {
            $wId = implode(',', $wId);
        }

        // 获取客户头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->head > 0) {
                $workerOrder[$tx]->head = DB::table('enclosures')->where('id', $tv->head)->pluck('path');
            }
        }

        // 获取客户最新回复
        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $sql = "select work_order_id, user_id, ask, created_at as asktime from 
                (select work_order_id, user_id, ask, created_at from " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
        $asks = DB::select($sql);
        foreach ($workerOrder as $woo) {
            foreach ($asks as $ask) {
                if ($woo->id == $ask->work_order_id) {
                    $woo->content = $ask->ask;
                    $woo->time = $ask->asktime;
                }
            }
        }

        return $workerOrder;
    }

    // 处理中工单列表
    public function getUntreated($com)
    {
        // 获取公司工单总数
        $workCount = $this->workCounts($com);

        // 获取处理中工单列表
        if ($workCount['2'] > 0) {
            $workerOrder = $this->workStatusUntreatedAll($com, $this->page);
        } else {
            $workerOrder = '';
        }

        // 工单最新回复排序 - no
        if ($workerOrder == '') {
            $workerOrders = '';
        } else {
            $workList = $workerOrder->toArray(); // 对象转为数组
            $workerOrderList = $workList['data'];

            // 客户与用户最新回复过滤
            foreach ($workerOrderList as $k => $v) {
                if ($v->rtime > $v->time) {
                    $workerOrderList[$k]->head = $v->radmin_head;
                    $workerOrderList[$k]->name = $v->rname;
                    $workerOrderList[$k]->time = $v->rtime;
                    $workerOrderList[$k]->content = $v->rcontent;
                }
            }

            $workerOrders = $this->array_sort_work($workerOrderList, 'time', 'desc'); // 最新时间倒叙
        }

        return View::make('com/site/work/untreated')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('workerOrders'));
    }

    // 处理中工单列表获取处理
    public function workStatusUntreatedAll($com, $page)
    {
        // 待处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '2')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
        $com->id . '_groups.group_name') 
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);

        // 获取头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->head > 0) {
                $workerOrder[$tx]->head = DB::table('enclosures')->where('id', $tv->head)->pluck('path');
            }
        }

        // 获取当前工单处理人员
        $admins =  DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '2')
        ->select('users.user_name', 'users.name', 'users.id', 'admin_user.position', 
        $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();
        foreach ($workerOrder as $woo) {
            foreach ($admins as $admin) {
                if ($woo->id == $admin->work_order_id && $woo->admin_id == $admin->id) {
                    $woo->admin_name = $admin->name;
                    if ($admin->position != '') {
                        $woo->position = $admin->position;
                    } else {
                        $woo->position = '管理员';
                    }
                }
            }
        }

        // 获取客户最新回复
        $wId = WorkOrder::where('work_status', '2')->get()->lists('id');
        if(is_array($wId)) {
            $wId = implode(',', $wId);
        }

        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $sql = "select work_order_id, ask, created_at as asktime from 
                (select work_order_id, ask, created_at from " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
        $asks = DB::select($sql);
        foreach ($workerOrder as $woo) {
            foreach ($asks as $ask) {
                if ($woo->id == $ask->work_order_id) {
                    $woo->content = $ask->ask;
                    $woo->time = $ask->asktime;
                }
            }
        }

        // 获取用户最新回复
        $rsql = "select work_order_id, reply, admin_id as radmin_id, created_at as replytime, system from 
                (select work_order_id, reply, admin_id, created_at, system from " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 GROUP BY work_order_id";
        $replys = DB::select($rsql);

        // 获取用户头像
        if(is_array($replys)) {
            foreach ($replys as $radmin) {
                $adminHeadId[] = $radmin->radmin_id;
            }
        }
        if(is_array($adminHeadId)) {
            $adminHeadId = implode(',', $adminHeadId);
        }
        $rsqls = "select id, head, name from " . $TablePrefix . "users where id in (" . $adminHeadId .") ORDER BY created_at DESC";
        $adminHead = DB::select($rsqls);
        foreach ($replys as $rh => $rv) {
            foreach ($adminHead as $ah=>$av) {
                if ($rv->radmin_id == $av->id && $av->head > 0) {
                    $replys[$rh]->radmin_head = DB::table('enclosures')->where('id', $av->head)->pluck('path');
                } else {
                    $replys[$rh]->radmin_head = '0';
                }
                $replys[$rh]->rname = $av->name;
            }
        }
        foreach ($workerOrder as $woo) {
            foreach ($replys as $reply) {
                if ($woo->id == $reply->work_order_id) {
                    $woo->rname = $reply->rname;
                    $woo->rcontent = $reply->reply;
                    $woo->rtime = $reply->replytime;
                    $woo->radmin_head = $reply->radmin_head;
                    $woo->system = $reply->system;
                }
            }
        }

        return $workerOrder;
    }

    // 已处理工单列表
    public function getProcessed($com)
    {
        // 获取公司工单总数
        $workCount = $this->workCounts($com);

        // 获取已处理工单列表
        if ($workCount['3'] > 0) {
            $workerOrder = $this->workStatusProcessedAll($com, $this->page);
        } else {
            $workerOrder = '';
        }

        // 工单最新回复排序 - no
        if ($workerOrder == '') {
            $workerOrders = '';
        } else {
            $workList = $workerOrder->toArray(); // 对象转为数组
            $workerOrderList = $workList['data'];

            // 客户与用户最新回复过滤
            foreach ($workerOrderList as $k => $v) {
                $workerOrderList[$k]->head = $v->admin_head;
                $workerOrderList[$k]->name = $v->admin_name;
            }

            $workerOrders = $this->array_sort_work($workerOrderList, 'time', 'desc'); // 最新时间倒叙
        }

        return View::make('com/site/work/processed')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('workerOrders'));
    }

    // 已处理工单列表获取处理
    public function workStatusProcessedAll($com, $page)
    {
        // 已处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '3')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
        $com->id . '_groups.group_name') 
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);

        // 获取当前工单处理人员
        $admins =  DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '3')
        ->select('users.user_name', 'users.name', 'users.id', 'users.head', 'admin_user.position', 
        $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();
        foreach ($workerOrder as $woo) {
            foreach ($admins as $admin) {
                if ($woo->id == $admin->work_order_id && $woo->admin_id == $admin->id) {
                    $woo->admin_name = $admin->name;
                    $woo->admin_head = $admin->head; // 用户头像ID赋值
                    if ($admin->position != '') {
                        $woo->position = $admin->position;
                    } else {
                        $woo->position = '管理员';
                    }
                }
            }
        }

        // 获取用户头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->admin_head > 0) {
                $workerOrder[$tx]->admin_head = DB::table('enclosures')->where('id', $tv->admin_head)->pluck('path');
            }
        }

        // 获取用户最新回复
        $wId = WorkOrder::where('work_status', '3')->get()->lists('id');
        if(is_array($wId)) {
            $wId = implode(',', $wId);
        }

        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $sql = "select work_order_id, reply, created_at, system from 
                (select work_order_id, reply, created_at, system from " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 GROUP BY work_order_id";
        $replys = DB::select($sql);
        foreach ($workerOrder as $woo) {
            foreach ($replys as $reply) {
                if ($woo->id == $reply->work_order_id) {
                    $woo->content = $reply->reply;
                    $woo->time = $reply->created_at;
                    $woo->system = $reply->system;
                }
            }
        }

        return $workerOrder;
    }

    // 已关闭工单列表
    public function getClosed($com)
    {
        // 获取公司工单总数
        $workCount = $this->workCounts($com);

        // 获取已关闭工单列表
        if ($workCount['4'] > 0) {
            $workerOrder = $this->workStatusClosedAll($com, $this->page);
        } else {
            $workerOrder = '';
        }

        // 关闭工单对象转为数组 - no
        if ($workerOrder == '') {
            $workerOrders = '';
        } else {
            $workList = $workerOrder->toArray();
            $workerOrders = $workList['data'];
        }

        return View::make('com/site/work/closed')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('workerOrders'));
    }

    // 已关闭工单列表获取处理
    public function workStatusClosedAll($com, $page)
    {
        // 已关闭工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '4')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
        $com->id . '_groups.group_name') 
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);

        // 获取当前工单处理人员
        $admins =  DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '4')
        ->select('users.user_name', 'users.name', 'users.id', 'admin_user.position', 
        $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();

        if (count($admins) == 0) {
            foreach ($workerOrder as $woo) {
                $woo->position = '管理员';
                $woo->admin_name = '无';
            }
        } else {
            foreach ($workerOrder as $woo) {
                foreach ($admins as $admin) {
                    if ($woo->id == $admin->work_order_id && $woo->admin_id == $admin->id) {
                        $woo->admin_name = $admin->name;
                        if ($admin->position != '') {
                            $woo->position = $admin->position;
                        } else {
                            $woo->position = '管理员';
                        }
                    }
                }
            }
        }

        return $workerOrder;
    }

    // 工单详情页面
    public function workDetail($com, $workID = NULL)
    {
        // 判断是否为工单通知跳转 - no
        if ($_GET) {
            if ($_GET['message_id'] > 0) {
                // 修改通知记录为已阅读 － no
                $workMessage = WorkMessage::find($_GET['message_id']);
                $workMessage->status = 'true';
                $workMessage->save();
            }
        }

        // 工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.id', '=', $workID)->select($com->id . '_work_order.*', 'users.user_name', 
        'users.name', 'users.created_at as user_created_at', 'users.head', 'site_user.firm', 
        $com->id . '_groups.group_name')->orderBy($com->id . '_work_order.id', 'desc')->paginate();

        // 获取当前工单处理人员
        $admins = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.id', '=', $workID)->select('users.user_name', 'users.name', 
        'users.id', 'users.created_at as admin_created_at', 'users.head as admin_head', 'admin_user.position', 
        $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();

        if (count($admins) == 0) {
            foreach ($workerOrder as $woo) {
                $woo->position = '管理员';
                $woo->admin_name = '无';
            }
        } else {
            foreach ($workerOrder as $woo) {
                foreach ($admins as $admin) {
                    if ($woo->id == $admin->work_order_id && $woo->admin_id == $admin->id) {
                        $woo->admin_name = $admin->name;
                        $woo->admin_head = $admin->admin_head;
                        $woo->admin_created_at = $admin->admin_created_at;
                        if ($admin->position != '') {
                            $woo->position = $admin->position;
                        } else {
                            $woo->position = '管理员';
                        }
                    }
                }
            }
        }

        // 获取当前工单用户与客户头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->head > 0) {
                $workerOrder[$tx]->head = DB::table('enclosures')->where('id', $tv->head)->pluck('path');
            } else {
                $workerOrder[$tx]->head = 'false';
            }

            // 如果已有用户领取则查询头像
            if (count($admins) > 0 && $tv->admin_head > 0) {
                $workerOrder[$tx]->admin_head = DB::table('enclosures')->where('id', $tv->admin_head)->pluck('path');
            } else {
                $workerOrder[$tx]->admin_head = 'false';
            }
        }

        // 获取客户的提问
        $asks = DB::table($com->id . '_work_ask')->where('work_order_id', $workID)->get();

        // 获取客户上传附件信息
        foreach ($asks as $askk => $askv) {
            if ($askv->enclosure_id > 0) {
                $asks[$askk]->enclosure_path = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('path');
                $asks[$askk]->enclosure_suffix = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('suffix');
                $asks[$askk]->enclosure_name = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('enclosure_name');
                $asks[$askk]->enclosure_created_at = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('created_at');
            }
        }

        // 获取多用户的回答
        $replys = DB::table($com->id . '_work_reply')
        ->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_reply.admin_id')
        ->where($com->id . '_work_reply.work_order_id', '=', $workID)
        // ->where($com->id . '_work_reply.system', '!=', 2)->select($com->id . '_work_reply.*', 
        ->where($com->id . '_work_reply.system', '!=', 2)->where($com->id . '_work_reply.check', '=', 0)->select($com->id . '_work_reply.*', 
        'users.name', 'admin_user.position', 'users.head as admin_head')
        ->orderBy($com->id . '_work_reply.work_order_id', 'desc')->get();

        // 获取用户上传附件信息
        foreach ($replys as $replyk => $replyv) {
            if ($replyv->enclosure_id > 0) {
                $replys[$replyk]->enclosure_path = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('path');
                $replys[$replyk]->enclosure_suffix = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('suffix');
                $replys[$replyk]->enclosure_name = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('enclosure_name');
                $replys[$replyk]->enclosure_created_at = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('created_at');
            }
        }

        // 获取多用户的头像
        if (count($replys) > 0) {
            foreach ($replys as $rtx=>$rtv) {
                if ($rtv->admin_head > 0) {
                    $replys[$rtx]->admin_head = DB::table('enclosures')->where('id', $rtv->admin_head)->pluck('path');
                } else {
                    $replys[$rtx]->admin_head = 'false';
                }

                if ($rtv->position == '') {
                    $replys[$rtx]->position = '管理员';
                }
            }
        } else {
            $replys = '0';
        }

        // 客户提问与用户回答按时间倒叙排列
        if ($replys != '0') {
            foreach ($asks as $rs) {
                $roughAsk[] = $rs;
            }
            foreach ($replys as $rr) {
                $roughReply[] = $rr;
            }
            $content = array_merge($roughAsk, $roughReply);
            foreach ($content as $key => $row) {
                $accuracy[$key] = $row->created_at;
            }
            // array_multisort($accuracy, SORT_DESC, $content); // 倒叙排列回复
            array_multisort($accuracy, SORT_ASC, $content); // 正叙排列回复
        } else {
            $content = $asks;
            foreach ($content as $key => $row) {
                $accuracy[$key] = $row->created_at;
            }
            // array_multisort($accuracy, SORT_DESC, $content); // 倒叙排列回复
            array_multisort($accuracy, SORT_ASC, $content); // 正叙排列回复
        }

        // 获取工单以往附件列表
        if ($replys != '0') {
            foreach ($asks as $kes=>$kesv) {
                if ($kesv->enclosure_id > 0) {
                    $roughAskenc[$kes]['enclosure_path'] = $kesv->enclosure_path;
                    $roughAskenc[$kes]['enclosure_suffix'] = $kesv->enclosure_suffix;
                    $roughAskenc[$kes]['enclosure_name'] = $kesv->enclosure_name;
                    $roughAskenc[$kes]['enclosure_created_at'] = $kesv->enclosure_created_at;
                }
            }
            foreach ($replys as $res=>$resv) {
                if ($resv->enclosure_id > 0) {
                    $roughReplyenc[$res]['enclosure_path'] = $resv->enclosure_path;
                    $roughReplyenc[$res]['enclosure_suffix'] = $resv->enclosure_suffix;
                    $roughReplyenc[$res]['enclosure_name'] = $resv->enclosure_name;
                    $roughReplyenc[$res]['enclosure_created_at'] = $resv->enclosure_created_at;
                }
            }

            if (isset($roughAskenc) && isset($roughReplyenc)) {
                $enclosureAll = array_merge($roughAskenc, $roughReplyenc);
                foreach ($enclosureAll as $key => $row) {
                    $eaccuracy[$key] = $row['enclosure_created_at'];
                }
                array_multisort($eaccuracy, SORT_DESC, $enclosureAll);
            } else if (isset($roughAskenc)) {
                $enclosureAll = $roughAskenc;
            } else if (isset($roughReplyenc)) {
                $enclosureAll = $roughReplyenc;
            } else {
                $enclosureAll = '0';
            }
        } else {
            foreach ($asks as $kes=>$kesv) {
                if ($kesv->enclosure_id > 0) {
                    $roughAskenc[$kes]['enclosure_path'] = $kesv->enclosure_path;
                    $roughAskenc[$kes]['enclosure_suffix'] = $kesv->enclosure_suffix;
                    $roughAskenc[$kes]['enclosure_name'] = $kesv->enclosure_name;
                    $roughAskenc[$kes]['enclosure_created_at'] = $kesv->enclosure_created_at;
                }
            }

            if (isset($roughAskenc)) {
                $enclosureAll = $roughAskenc;
            } else {
                $enclosureAll = '0';
            }
        }

        // 根据工单状态到不同页面
        if ($workerOrder[0]->work_status == '1') {
            return View::make('com/site/work/workdetail')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        } else if ($workerOrder[0]->work_status == '2') {
            return View::make('com/site/work/workdetailuntreated')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        } else if ($workerOrder[0]->work_status == '3') {
            return View::make('com/site/work/workdetailprocessed')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        } else if ($workerOrder[0]->work_status == '4') {
            return View::make('com/site/work/workdetailclosed')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        }
    }

    // 回复处理工单
    public function workProcessed($com, $workID = NULL)
    {
        // 验证回复内容
        $rules = array(
            'ask' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->with('error', '请输入回复内容！');
        }
        
        // 验证是否为@回复内容
        $arr = explode("\r\n", Input::get('ask'));
        $arr_first = substr($arr['0'], 0, 1);
        if ($arr_first == '@') {
            $arr_str = explode(" ", $arr['0']);
            if (!$arr['1']) {
                return Redirect::back()->with('error', $arr_str['0'] . ', 请在第二行输入回复内容！');
            }

            $anchorTime = $arr_str['1'] . " " . $arr_str['2'];
            $anchorTimeData = DB::table($com->id . '_work_reply')->where('created_at', $anchorTime)->pluck('created_at');
            if (!$anchorTimeData) {
                return Redirect::back()->with('error', $arr_str['0'] . ', 回复时间不存在！');
            }

            $ask_anchor = $anchorTimeData;
            // 删除第一行@内容组合成新内容
            unset($arr['0']);
            $new_arr = implode("\r\n", $arr); 
            // 将换行符转换成<br />
            $ask_content = str_replace("\r\n", "<br>", $new_arr);
        } else {
            $ask_anchor = '';
            // 将换行符转换成<br />
            $askContent = e(Input::get('ask'));
            $ask_content = str_replace("\r\n", "<br>", $askContent);
        }

        // 验证是否上传附件
        if (Input::hasFile('file')) {
            $file = Input::file('file');
            // 验证附件大小
            if ($file->getSize() > (1024 * 1024)) {
                return Redirect::back()->with('error', '上传附件过大，请控制在1M以内！');
            }

            // 验证附件后缀
            $suffixName = explode('.', $file->getClientOriginalName());
            if ($suffixName['1'] != 'docx' && $suffixName['1'] != 'xlsx' && $suffixName['1'] != 'txt' 
                && $suffixName['1'] != 'png' && $suffixName['1'] != 'jpg' && $suffixName['1'] != 'gif' 
                && $suffixName['1'] != 'bmp' && $suffixName['1'] != 'jpeg') {
                return Redirect::back()->with('error', '文件格式必须为docx、xlsx、txt；图片格式必须为png、jpg、jpeg、gif或bmp！');
            }

            // $destinationPath = 'uploads/' . $com->id . '/' . date('Y/m/d');
            $destinationPath = 'uploads/' . $com->byname . '/' . date('Y/m/d');
            $extension = $file->getClientOriginalExtension();
            $filename = str_random(8) . '.' . $extension;
            $upload_success = $file->move($destinationPath, $filename);
            if ($upload_success) {
                $istr = explode(".", $filename);
                $encl = new Enclosure();
                $encl->enclosure_name = $file->getClientOriginalName();
                // 判断附件类型
                if ($suffixName['1'] == 'docx' || $suffixName['1'] == 'xlsx' || $suffixName['1'] == 'txt' ) {
                    $encl->type = 'file';
                } else {
                    $encl->type = 'image';
                }
                $encl->suffix = $file->getClientOriginalExtension();
                $encl->path = $destinationPath . '/' . $filename;

                if ($encl->save()) {
                    $ask_enclosure_id = $encl->id;
                }
            } else {
                return Redirect::back()->with('error', '上传附件失败！');
            }
        } else {
            $ask_enclosure_id = '0';
        }

        // 前台回复工单
        $ask = new WorkAsk;
        $ask->work_order_id = $workID;
        $ask->user_id = Auth::user()->id;
        $ask->ask = $ask_content;
        $ask->enclosure_id = $ask_enclosure_id;
        $ask->ask_anchor = $ask_anchor;
        $ask->system = 0;
        if ($ask->save()) {
            $work_order = WorkOrder::find($workID);
            
            // 获得工单处理人员
            if ($work_order->work_status == '1') {
                // 获得绑定组员
                $admin_id = DB::table($com->id . '_bind_user')->where('user_id', Auth::user()->id)->pluck('admin_id');
            } elseif ($work_order->work_status == '2') {
                // 获得工单受理人员
                $admin_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('admin_id');
            }

            // 工单通知信息处理 － no
            $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
            if ($message_id) {
                $work_message = WorkMessage::find($message_id);
                $work_message->work_status = '2';
                $work_message->type = 'ask';
                $work_message->status = 'false';
                $work_message->ask_id = $ask->id;

                // 待受理工单回复直接发送通知
                if ($work_order->work_status == '1') {
                    $work_message->save();
                } elseif ($work_order->work_status == '2') {
                    // 获得工单受理人员
                    $admin_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('admin_id');

                    $admin_message = DB::table('admin_user')->where('user_id', $admin_id)->pluck('message');
                    if ($admin_message == '0') {
                        $work_message->save();
                    }
                }
            }
            
            // 客户发起内容接收邮件队列
            // 获得组别
            $group_id = DB::table($com->id . '_bind_user')->where('user_id', Auth::user()->id)->pluck('group_id');
            // 获得公司路由
            $path = Config::get('app.com_path');
            $this->quereUser($com, $ask->id, $admin_id, $group_id, $ask_content, $path, $workID);

            return Redirect::to($com->path . '/site/work/workdetail/' . $workID)
            ->with('success', '工单#' . $workID . '回复成功！');
        } else {
            return Redirect::back()->with('error', '工单#' . $workID . '回复失败！');
        }
    }

    // 工单重启
    public function workAllot($com, $workID = NULL)
    {
        $work_order = WorkOrder::find($workID);
        if ($work_order->work_status != '3') {
            return Redirect::to($com->path . '/site/work')->with('error', '工单未完成，无法重启！');
        } else {
            $work_order->work_status = '2';
            
            if ($work_order->save()) {
                // 工单重启自动回复
                $workask = new WorkAsk();
                $workask->work_order_id = $workID;
                $workask->user_id = Auth::user()->id;
                $workask->ask = "工单 #". $workID . "已重启，操作人为客户：" . Auth::user()->name . "。";
                $workask->enclosure_id = 0;
                
                if ($workask->save()) {
                    // 获得工单受理人员
                    $admin_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('admin_id');

                    // 工单通知信息处理 － no
                    $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                    if ($message_id) {
                        $work_message = WorkMessage::find($message_id);
                        $work_message->work_status = $work_order->work_status;
                        $work_message->type = 'ask';
                        $work_message->status = 'false';
                        $work_message->ask_id = $workask->id;
                        
                        $admin_message = DB::table('admin_user')->where('user_id', $admin_id)->pluck('message');
                        if ($admin_message == '0') {
                            $work_message->save();
                        }
                    }

                    // 客户发起内容接收邮件队列
                    // 获得组别
                    $group_id = DB::table($com->id . '_bind_user')->where('user_id', Auth::user()->id)->pluck('group_id');
                    // 获得公司路由
                    $path = Config::get('app.com_path');
                    $this->quereUser($com, $workask->id, $admin_id, $group_id, $workask->ask, $path, $workID);

                    return Redirect::to($com->path . '/site/work')->with('success', '工单#' . $workID . '重启成功！');
                } else {
                    return Redirect::to($com->path . '/site/work')->with('error', '工单#' . $workID . '重启自动回复失败！');
                }
            } else {
                return Redirect::to($com->path . '/site/work')->with('error', '工单#' . $workID . '重启失败！');
            }
        }
    }

    // 工单关闭
    public function workClosed($com, $workID = NULL)
    {
        $work_order = WorkOrder::find($workID);
        $work_order->work_status = '4';

        if ($work_order->save()) {
            // 工单关闭自动回复
            $workask = new WorkAsk();
            $workask->work_order_id = $workID;
            $workask->user_id = Auth::user()->id;
            $workask->ask = "工单 #". $workID . "已关闭，操作人为客户：" . Auth::user()->name . "。";
            $workask->enclosure_id = 0;
            
            if ($workask->save()) {
                // 获得工单受理人员
                $admin_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('admin_id');

                // 工单通知信息处理 － no
                $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                if ($message_id) {
                    $work_message = WorkMessage::find($message_id);
                    $work_message->work_status = $work_order->work_status;
                    $work_message->type = 'ask';
                    $work_message->status = 'false';
                    $work_message->ask_id = $workask->id;
                    
                    $admin_message = DB::table('admin_user')->where('user_id', $admin_id)->pluck('message');
                    if ($admin_message == '0') {
                        $work_message->save();
                    }
                }

                // 客户发起内容接收邮件队列
                // 获得组别
                $group_id = DB::table($com->id . '_bind_user')->where('user_id', Auth::user()->id)->pluck('group_id');
                // 获得公司路由
                $path = Config::get('app.com_path');
                $this->quereUser($com, $workask->id, $admin_id, $group_id, $workask->ask, $path, $workID);

                return Redirect::to($com->path . '/site/work')->with('success', '工单#' . $workID . '关闭成功！');
            } else {
                return Redirect::to($com->path . '/site/work')->with('error', '工单#' . $workID . '关闭自动回复失败！');
            }
        } else {
            return Redirect::to($com->path . '/site/work')->with('error', '工单#' . $workID . '关闭失败！');
        }
    }

    // 工单标题搜索
    public function searchWork($com, $status = NULL, $keyword = NULL)
    {
        $workCount = $this->workCounts($com); // 获取工单总数

        if ($status == '1') {
            // 获取待受理工单列表
            if ($workCount['1'] > 0) {
                $workerOrder = $this->searchWorkStatusIndex($com, $keyword, $this->page);
                // 获取搜索工单总数
                if (count($workerOrder) == 0) {
                    $workerOrder = '';
                    $searchCount = 0;
                } else {
                    $searchCount = WorkOrder::where('work_status', '1')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
                    $searchCount = $searchCount;
                }
            } else {
                $workerOrder = '';
                $searchCount = 0;
            }

            // 工单最新回复排序 - no
            if ($workerOrder == '') {
                $workerOrders = '';
            } else {
                $workList = $workerOrder->toArray(); // 对象转为数组
                $workerOrders = $this->array_sort_work($workList['data'], 'time', 'desc'); // 最新时间倒叙
            }

            return View::make('com/site/work/worksearch')->with('com', $com)->with(compact('workCount'))
            ->with(compact('workerOrder'))->with(compact('workerOrders'))->with(compact('searchCount'));
        } else if ($status == '2') {
            // 获取处理中工单列表
            if ($workCount['2'] > 0) {
                $workerOrder = $this->searchWorkStatusUntreatedAll($com, $keyword, $this->page);
                // 获取搜索工单总数
                if (count($workerOrder) == 0) {
                    $workerOrder = '';
                    $searchCount = 0;
                } else {
                    $searchCount = WorkOrder::where('work_status', '2')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
                    $searchCount = $searchCount;
                }
            } else {
                $workerOrder = '';
                $searchCount = 0;
            }

            // 工单最新回复排序 - no
            if ($workerOrder == '') {
                $workerOrders = '';
            } else {
                $workList = $workerOrder->toArray(); // 对象转为数组
                $workerOrderList = $workList['data'];

                // 客户与用户最新回复过滤
                foreach ($workerOrderList as $k => $v) {
                    if ($v->rtime > $v->time) {
                        $workerOrderList[$k]->head = $v->radmin_head;
                        $workerOrderList[$k]->name = $v->rname;
                        $workerOrderList[$k]->time = $v->rtime;
                        $workerOrderList[$k]->content = $v->rcontent;
                    }
                }

                $workerOrders = $this->array_sort_work($workerOrderList, 'time', 'desc'); // 最新时间倒叙
            }

            return View::make('com/site/work/worksearchuntreated')->with('com', $com)->with(compact('workCount'))
            ->with(compact('workerOrder'))->with(compact('workerOrders'))->with(compact('searchCount'));
        } else if ($status == '3') {
            // 获取已处理工单列表
            if ($workCount['3'] > 0) {
                $workerOrder = $this->searchWorkStatusProcessedAll($com, $keyword, $this->page);
                // 获取搜索工单总数
                if (count($workerOrder) == 0) {
                    $workerOrder = '';
                    $searchCount = 0;
                } else {
                    $searchCount = WorkOrder::where('work_status', '3')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
                    $searchCount = $searchCount;
                }
            } else {
                $workerOrder = '';
                $searchCount = 0;
            }

            // 工单最新回复排序 - no
            if ($workerOrder == '') {
                $workerOrders = '';
            } else {
                $workList = $workerOrder->toArray(); // 对象转为数组
                $workerOrderList = $workList['data'];

                // 客户与用户最新回复过滤
                foreach ($workerOrderList as $k => $v) {
                    $workerOrderList[$k]->head = $v->admin_head;
                    $workerOrderList[$k]->name = $v->admin_name;
                }

                $workerOrders = $this->array_sort_work($workerOrderList, 'time', 'desc'); // 最新时间倒叙
            }

            return View::make('com/site/work/worksearchprocessed')->with('com', $com)->with(compact('workCount'))
            ->with(compact('workerOrder'))->with(compact('workerOrders'))->with(compact('searchCount'));
        } else if ($status == '4') {
            // 获取已关闭工单列表
            if ($workCount['4'] > 0) {
                $workerOrder = $this->searchWorkStatusClosedAll($com, $keyword, $this->page);
                // 获取搜索工单总数
                if (count($workerOrder) == 0) {
                    $workerOrder = '';
                    $searchCount = 0;
                } else {
                    $searchCount = WorkOrder::where('work_status', '4')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
                    $searchCount = $searchCount;
                }
            } else {
                $workerOrder = '';
                $searchCount = 0;
            }

            // 关闭工单对象转为数组 - no
            $workList = $workerOrder->toArray();
            $workerOrders = $workList['data'];

            return View::make('com/site/work/worksearchclosed')->with('com', $com)->with(compact('workCount'))
            ->with(compact('workerOrder'))->with(compact('workerOrders'))->with(compact('searchCount'));
        }
    }

    // 待受理工单列表搜索处理
    public function searchWorkStatusIndex($com, $keyword = NULL, $page)
    {
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '1')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
        $com->id . '_groups.group_name')->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        $wId = WorkOrder::where('work_status', '1')->get()->lists('id');
        if(is_array($wId)) {
            $wId = implode(',', $wId);
        }

        // 获取客户头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->head > 0) {
                $workerOrder[$tx]->head = DB::table('enclosures')->where('id', $tv->head)->pluck('path');
            }
        }

        // 获取客户最新回复
        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $sql = "select work_order_id, user_id, ask, created_at as asktime from 
                (select work_order_id, user_id, ask, created_at from " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
        $asks = DB::select($sql);
        foreach ($workerOrder as $woo) {
            foreach ($asks as $ask) {
                if ($woo->id == $ask->work_order_id) {
                    $woo->content = $ask->ask;
                    $woo->time = $ask->asktime;
                }
            }
        }

        return $workerOrder;
    }

    // 处理中工单列表搜索处理
    public function searchWorkStatusUntreatedAll($com, $keyword = NULL, $page)
    {
        // 待处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '2')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
        $com->id . '_groups.group_name') 
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);

        // 获取头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->head > 0) {
                $workerOrder[$tx]->head = DB::table('enclosures')->where('id', $tv->head)->pluck('path');
            }
        }

        // 获取当前工单处理人员
        $admins =  DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '2')
        ->select('users.user_name', 'users.name', 'users.id', 'admin_user.position', 
        $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();
        foreach ($workerOrder as $woo) {
            foreach ($admins as $admin) {
                if ($woo->id == $admin->work_order_id && $woo->admin_id == $admin->id) {
                    $woo->admin_name = $admin->name;
                    if ($admin->position != '') {
                        $woo->position = $admin->position;
                    } else {
                        $woo->position = '管理员';
                    }
                }
            }
        }

        // 获取客户最新回复
        $wId = WorkOrder::where('work_status', '2')->get()->lists('id');
        if(is_array($wId)) {
            $wId = implode(',', $wId);
        }

        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $sql = "select work_order_id, ask, created_at as asktime from 
                (select work_order_id, ask, created_at from " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_ask 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
        $asks = DB::select($sql);
        foreach ($workerOrder as $woo) {
            foreach ($asks as $ask) {
                if ($woo->id == $ask->work_order_id) {
                    $woo->content = $ask->ask;
                    $woo->time = $ask->asktime;
                }
            }
        }

        // 获取用户最新回复
        $rsql = "select work_order_id, reply, admin_id as radmin_id, created_at as replytime, system from 
                (select work_order_id, reply, admin_id, created_at, system from " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 GROUP BY work_order_id";
        $replys = DB::select($rsql);
        // 获取用户头像
        if(is_array($replys)) {
            foreach ($replys as $radmin) {
                $adminHeadId[] = $radmin->radmin_id;
            }
        }
        if(is_array($adminHeadId)) {
            $adminHeadId = implode(',', $adminHeadId);
        }
        $rsqls = "select id, head, name from " . $TablePrefix . "users where id in (" . $adminHeadId .") ORDER BY created_at DESC";
        $adminHead = DB::select($rsqls);
        foreach ($replys as $rh => $rv) {
            foreach ($adminHead as $ah=>$av) {
                if ($rv->radmin_id == $av->id && $av->head > 0) {
                    $replys[$rh]->radmin_head = DB::table('enclosures')->where('id', $av->head)->pluck('path');
                } else {
                    $replys[$rh]->radmin_head = '0';
                }
                $replys[$rh]->rname = $av->name;
            }
        }
        foreach ($workerOrder as $woo) {
            foreach ($replys as $reply) {
                if ($woo->id == $reply->work_order_id) {
                    $woo->rname = $reply->rname;
                    $woo->rcontent = $reply->reply;
                    $woo->rtime = $reply->replytime;
                    $woo->radmin_head = $reply->radmin_head;
                    $woo->system = $reply->system;
                }
            }
        }

        return $workerOrder;
    }

    // 已处理工单列表搜索处理
    public function searchWorkStatusProcessedAll($com, $keyword = NULL, $page)
    {
        // 已处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '3')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
        $com->id . '_groups.group_name') 
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);

        // 获取当前工单处理人员
        $admins =  DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '3')
        ->select('users.user_name', 'users.name', 'users.id', 'users.head', 'admin_user.position', 
        $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();
        foreach ($workerOrder as $woo) {
            foreach ($admins as $admin) {
                if ($woo->id == $admin->work_order_id && $woo->admin_id == $admin->id) {
                    $woo->admin_name = $admin->name;
                    $woo->admin_head = $admin->head; // 用户头像ID赋值
                    if ($admin->position != '') {
                        $woo->position = $admin->position;
                    } else {
                        $woo->position = '管理员';
                    }
                }
            }
        }

        // 获取用户头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->admin_head > 0) {
                $workerOrder[$tx]->admin_head = DB::table('enclosures')->where('id', $tv->admin_head)->pluck('path');
            }
        }

        // 获取用户最新回复
        $wId = WorkOrder::where('work_status', '3')->get()->lists('id');
        if(is_array($wId)) {
            $wId = implode(',', $wId);
        }

        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $sql = "select work_order_id, reply, created_at, system from 
                (select work_order_id, reply, created_at, system from " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") and system != 2 GROUP BY work_order_id";
        $replys = DB::select($sql);
        foreach ($workerOrder as $woo) {
            foreach ($replys as $reply) {
                if ($woo->id == $reply->work_order_id) {
                    $woo->content = $reply->reply;
                    $woo->time = $reply->created_at;
                    $woo->system = $reply->system;
                }
            }
        }

        return $workerOrder;
    }

    // 已关闭工单列表搜索处理
    public function searchWorkStatusClosedAll($com, $keyword = NULL, $page)
    {
        // 已关闭工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '4')->where($com->id . '_work_order.user_id', '=', Auth::user()->id)
        ->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
        $com->id . '_groups.group_name') 
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);

        // 获取当前工单处理人员
        $admins =  DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '4')
        ->select('users.user_name', 'users.name', 'users.id', 'admin_user.position', 
        $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();
        if (count($admins) == 0) {
            foreach ($workerOrder as $woo) {
                $woo->position = '管理员';
                $woo->admin_name = '无';
            }
        } else {
            foreach ($workerOrder as $woo) {
                foreach ($admins as $admin) {
                    if ($woo->id == $admin->work_order_id && $woo->admin_id == $admin->id) {
                        $woo->admin_name = $admin->name;
                        // $woo->admin_head = $admin->admin_head;
                        // $woo->admin_created_at = $admin->admin_created_at;
                        if ($admin->position != '') {
                            $woo->position = $admin->position;
                        } else {
                            $woo->position = '管理员';
                        }
                    }
                }
            }
        }

        return $workerOrder;
    }

    // 工单往来附件记录
    public function enclosureWork($com, $workID = NULL)
    {
        // 获取客户的提问
        $asks = DB::table($com->id . '_work_ask')->where('work_order_id', $workID)->get();
        // 获取客户上传附件信息
        foreach ($asks as $askk => $askv) {
            if ($askv->enclosure_id > 0) {
                $asks[$askk]->enclosure_path = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('path');
                $asks[$askk]->enclosure_suffix = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('suffix');
                $asks[$askk]->enclosure_name = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('enclosure_name');
                $asks[$askk]->enclosure_created_at = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('created_at');
                $asks[$askk]->enclosure_type = DB::table('enclosures')->where('id', $askv->enclosure_id)->pluck('type');
            }
        }

        // 获取用户的提问
        $replys = DB::table($com->id . '_work_reply')->where('work_order_id', $workID)->get();
        // 获取客户上传附件信息
        foreach ($replys as $replyk => $replyv) {
            if ($replyv->enclosure_id > 0) {
                $replys[$replyk]->enclosure_path = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('path');
                $replys[$replyk]->enclosure_suffix = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('suffix');
                $replys[$replyk]->enclosure_name = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('enclosure_name');
                $replys[$replyk]->enclosure_created_at = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('created_at');
                $replys[$replyk]->enclosure_type = DB::table('enclosures')->where('id', $replyv->enclosure_id)->pluck('type');
            }
        }

        // 获取工单以往附件列表
        if ($replys != '0') {
            foreach ($asks as $kes=>$kesv) {
                if ($kesv->enclosure_id > 0) {
                    $roughAskenc[$kes]['enclosure_path'] = $kesv->enclosure_path;
                    $roughAskenc[$kes]['enclosure_suffix'] = $kesv->enclosure_suffix;
                    $roughAskenc[$kes]['enclosure_name'] = $kesv->enclosure_name;
                    $roughAskenc[$kes]['enclosure_created_at'] = $kesv->enclosure_created_at;
                    $roughAskenc[$kes]['enclosure_type'] = $kesv->enclosure_type;
                }
            }
            foreach ($replys as $res=>$resv) {
                if ($resv->enclosure_id > 0) {
                    $roughReplyenc[$res]['enclosure_path'] = $resv->enclosure_path;
                    $roughReplyenc[$res]['enclosure_suffix'] = $resv->enclosure_suffix;
                    $roughReplyenc[$res]['enclosure_name'] = $resv->enclosure_name;
                    $roughReplyenc[$res]['enclosure_created_at'] = $resv->enclosure_created_at;
                    $roughReplyenc[$res]['enclosure_type'] = $resv->enclosure_type;
                }
            }

            if (isset($roughAskenc) && isset($roughReplyenc)) {
                $enclosureAll = array_merge($roughAskenc, $roughReplyenc);
                foreach ($enclosureAll as $key => $row) {
                    $eaccuracy[$key] = $row['enclosure_created_at'];
                }
                array_multisort($eaccuracy, SORT_DESC, $enclosureAll);
            } else if (isset($roughAskenc)) {
                $enclosureAll = $roughAskenc;
            } else if (isset($roughReplyenc)) {
                $enclosureAll = $roughReplyenc;
            } else {
                $enclosureAll = '0';
            }
        } else {
            foreach ($asks as $kes=>$kesv) {
                if ($kesv->enclosure_id > 0) {
                    $roughAskenc[$kes]['enclosure_path'] = $kesv->enclosure_path;
                    $roughAskenc[$kes]['enclosure_suffix'] = $kesv->enclosure_suffix;
                    $roughAskenc[$kes]['enclosure_name'] = $kesv->enclosure_name;
                    $roughAskenc[$kes]['enclosure_created_at'] = $kesv->enclosure_created_at;
                    $roughAskenc[$kes]['enclosure_type'] = $kesv->enclosure_type;
                }
            }

            if (isset($roughAskenc)) {
                $enclosureAll = $roughAskenc;
            } else {
                $enclosureAll = '0';
            }
        }

        return View::make('com/site/work/getenclosurework')->with('com', $com)->with(compact('enclosureAll'));
    }

    // 客户发起内容接收邮件队列
    public function quereUser($com, $ask_id = NULL, $admin_id = NULL, $group_id = NULL, $content = NULL, $path = NULL, $work_id = NULL)
    {
        // 插入公用矩阵邮件队列
        $status = 'true'; // 队列开关
        $from_email = Auth::user()->user_name; // 发起人邮箱
        // 接受人邮箱，无绑定组员则选择组别邮箱
        if ($admin_id == 0) {
            $email_id = DB::table($com->id . '_groups')->where('id', $group_id)->pluck('email_id');
            $level = 0;

            if ($email_id > 0) {
                $to_email = DB::table('company_email')->where('disabled', 'false')->where('id', $email_id)->pluck('email');
                if (!$to_email)
                    $status = 'false';
            } else {
                // $to_email = $email_id;
                $status = 'false';
            }
        } else {
            // 查询接收人邮箱是否开启接收功能
            $enable = DB::table('admin_user')->where('user_id', $admin_id)->pluck('enable');
            if ($enable == 'stop') {
                $status = 'false';
            } else {
                $to_email = DB::table('users')->where('id', $admin_id)->pluck('user_name');
                $level = DB::table('admin_user')->where('user_id', $admin_id)->pluck('level');
            }
        }

        if ($status != 'false') {
            // 公用矩阵邮件队列处理
            $quere = new Quere;
            $quere->company_id = $com->id;
            $quere->from_email = $from_email;
            $quere->to_email = $to_email;
            $quere->ask_id = $ask_id;
            
            // 插入私有公司邮件队列
            if ($quere->save()) {
                // 判断用户等级获取用户对象
                if (($admin_id == 0 && $level == 0) || $level == 4) {
                    $com_quere = new StaffQuere;
                } else if ($level == 3) {
                    // $com_quere = new LeaderQuere;
                    $com_quere = new StaffQuere;
                } else if ($level == 2) {
                    $com_quere = new ManagerQuere;
                } else if ($level == 1) {
                    $com_quere = new CeoQuere;
                }

                // 私有公司邮件队列处理
                $com_quere->quere_id = $quere->id;
                $com_quere->company_id = $com->id;
                $com_quere->from_email = $from_email;
                $com_quere->to_email = $to_email;
                $com_quere->ask_id = $ask_id;
                $com_quere->content = $content;
                $com_quere->path = $path;
                $com_quere->work_id = $work_id;
                $com_quere->save();
            }
        }
    }

    // 工单消息通知 - no
    public function messageWork($com)
    {
        // 获取工单消息通知总数
        $workCount = DB::table($com->id . '_work_message')->where('user_id', '=', Auth::user()->id)
        ->where('type', '=', 'reply')->where('status', '=', 'false')->count();

        echo json_encode(array('workCount'=>$workCount)); 
    }

    // 工单消息通知详情 - no
    public function getMessage($com)
    {
        // 获取工单消息通知
        $messageData = DB::table($com->id . '_work_message')->where('user_id', '=', Auth::user()->id)
        ->where('type', '=', 'reply')->where('status', '=', 'false')->get();

        // 最新时间倒叙
        $message = $this->array_sort_work($messageData, 'updated_at', 'desc');

        // 获取客户姓名
        $user_name = DB::table('users')->where('id', Auth::user()->id)->pluck('name');

        // 获取受理人和处理人姓名
        foreach ($message as $k => $v) {
            $message[$k]->admin_name = DB::table('users')->where('id', $v->admin_id)->pluck('name');
            $message[$k]->reply_admin_name = DB::table('users')->where('id', $v->reply_admin_id)->pluck('name');
        }
        
        return View::make('com/site/work/message')->with('com', $com)->with(compact('message'))->with(compact('user_name'));
    }
}
