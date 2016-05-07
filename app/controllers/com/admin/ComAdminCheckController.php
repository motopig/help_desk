<?php

/**
 * Class ComAdminCheckController
 *
 * 公司后台工单审核管理控制器 by no
 */
class ComAdminCheckController extends ComAdminController 
{
	/**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();
    }

    // 设置工单审核分页数
    private $page = 10;

    // 工单审核首页
    public function index($com)
    {
    	// 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取需审核的用户回复记录
        if ($level > 2) {
            if ($level > 3) {
                $check = DB::table($com->id . '_work_reply')
                ->join($com->id . '_work_order', $com->id . '_work_order.id', '=', $com->id . '_work_reply.work_order_id')
                ->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
                ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
                ->where($com->id . '_work_reply.checkreply', '1')->where($com->id . '_work_order.work_status', '2')
                ->where($com->id . '_work_order.admin_id', Auth::user()->id)
                ->select($com->id . '_work_reply.*', $com->id . '_work_order.work_level as work_level', 
                $com->id . '_work_order.work_status as status', $com->id . '_groups.group_name as group_name', 
                $com->id . '_work_order.created_at as w_created_at', $com->id . '_groups.group_name as group_name', 
                'users.name as admin', 'users.user_name as email_admin')->paginate($this->page);
            } else {
                // 获取作为组长的所属组别
                $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', 1)->get()->lists('group_id');

                $check = DB::table($com->id . '_work_reply')
                ->join($com->id . '_work_order', $com->id . '_work_order.id', '=', $com->id . '_work_reply.work_order_id')
                ->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
                ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
                ->where($com->id . '_work_reply.checkreply', '1')->where($com->id . '_work_order.work_status', '2')
                ->whereIn($com->id . '_work_order.group_id', $group_id)
                ->select($com->id . '_work_reply.*', $com->id . '_work_order.work_level as work_level', 
                $com->id . '_work_order.work_status as status', $com->id . '_groups.group_name as group_name', 
                $com->id . '_work_order.created_at as w_created_at', $com->id . '_groups.group_name as group_name', 
                'users.name as admin', 'users.user_name as email_admin')->paginate($this->page);
            }
        } else {
        	$check = DB::table($com->id . '_work_reply')
	        ->join($com->id . '_work_order', $com->id . '_work_order.id', '=', $com->id . '_work_reply.work_order_id')
	        ->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
	        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
	        ->where($com->id . '_work_reply.checkreply', '1')->where($com->id . '_work_order.work_status', '2')
	        ->select($com->id . '_work_reply.*', $com->id . '_work_order.work_level as work_level', 
	        $com->id . '_work_order.work_status as status', $com->id . '_groups.group_name as group_name', 
	        $com->id . '_work_order.created_at as w_created_at', $com->id . '_groups.group_name as group_name', 
	        'users.name as admin', 'users.user_name as email_admin')->paginate($this->page);
        }

        // 获取工单发起人邮箱
        foreach ($check as $k => $v) {
        	$user_id = DB::table($com->id . '_work_order')->where('id', $v->work_order_id)->pluck('user_id');
        	$name = DB::table('users')->where('id', $user_id)->pluck('name');
        	$user_name = DB::table('users')->where('id', $user_id)->pluck('user_name');
            $check[$k]->user = $name;
            $check[$k]->email_user = $user_name;
        }

        // 获取审核建议
        foreach ($check as $k => $v) {
            if ($check_log = DB::table($com->id . '_check_log')->where('reply_id', $v->id)->where('content', '!=', '0')
                ->pluck('id')) {
                $check[$k]->check_log = $check_log;
            } else {
                $check[$k]->check_log = 'false';
            }
        }

        // 加载模板
        return View::make('com/admin/check/index')->with('com', $com)->with(compact('check'))->with(compact('level'));
    }

    // 审核工单历史查看
    public function checkReplyDetail($com, $work_id = NULL, $reply_id = NULL)
    {
        // 获得当前回复
    	$reply = DB::table($com->id . '_work_reply')->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
        ->join($com->id . '_work_order', $com->id . '_work_order.id', '=', $com->id . '_work_reply.work_order_id')
        ->join('admin_user', 'admin_user.user_id', '=', 'users.id')->where($com->id . '_work_reply.id', $reply_id)
        ->select($com->id . '_work_reply.*', 'users.name as admin', 'admin_user.position')->paginate();

        // 获得工单历史记录
        $content = $this->workCheckDetail($com, $work_id);

        // 判断工单级别
        $work_level = DB::table($com->id . '_work_order')->where('id', $work_id)->pluck('work_level');

        // 加载模板
        return View::make('com/admin/check/work')->with('com', $com)->with(compact('reply'))->with(compact('content'))->with(compact('work_level'));
    }

    // 工单审核
    public function checkReplyGet($com, $work_id = NULL, $reply_id = NULL)
    {
        // 获得当前回复
        $reply = DB::table($com->id . '_work_reply')->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
        ->join($com->id . '_work_order', $com->id . '_work_order.id', '=', $com->id . '_work_reply.work_order_id')
        ->join('admin_user', 'admin_user.user_id', '=', 'users.id')->where($com->id . '_work_reply.id', $reply_id)
        ->select($com->id . '_work_reply.*', 'users.name as admin', 'admin_user.position')->paginate();

        // 检查是否有较早的回复未审核
        $upside = DB::table($com->id . '_work_reply')->where($com->id . '_work_reply.id', '<', $reply_id)
        ->where($com->id . '_work_reply.work_order_id', $work_id)->where($com->id . '_work_reply.checkreply', '=', '1')
        ->where($com->id . '_work_reply.check_status', '=', '0')->select($com->id . '_work_reply.id', $com->id . '_work_reply.created_at')
        ->paginate();
        if ((count($upside) == 0)) {
            $upside = 'false';
        } else {
            $count = count($upside);
        }

        // 获取当前用户ID
        $admin_id = Auth::user()->id;
    	
        // 加载模板
        return View::make('com/admin/check/process')->with('com', $com)->with(compact('reply'))->with(compact('upside'))
        ->with(compact('reply_id'))->with(compact('admin_id'))->with(compact('count'))->with(compact('work_id'));
    }

    // js escape解码
    function unescape($str) { 
        $ret = ''; 
        $len = strlen($str); 

        for ($i = 0; $i < $len; $i++) { 
            if ($str[$i] == '%' && $str[$i+1] == 'u') { 
                $val = hexdec(substr($str, $i+2, 4)); 
                if ($val < 0x7f) $ret .= chr($val); 
                else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
                else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
                $i += 5; 
            } elseif ($str[$i] == '%'){ 
                $ret .= urldecode(substr($str, $i, 3)); 
                $i += 2; 
            } else $ret .= $str[$i]; 
        } 

        return $ret; 
    } 

    // 工单审核处理
    public function checkProcess($com, $work_id = NULL, $reply_id = NULL, $admin_id = NULL)
    {
        $work_reply = WorkReply::find($reply_id);
        if ($_GET['check'] == '1') {
            $check = '1';
            $check_status = '1'; 
        } elseif ($_GET['check'] == '2') {
            $check = '0';
            $check_status = '2'; 
        }

        $work_reply->check = $check;
        $work_reply->check_status = $check_status;
        if ($work_reply->save()) {
            // 判断是否填写建议内容
            if ($_GET['content'] != '0') {
                // JS内容解码并记录
                $j_content = $this->unescape($_GET['content']);
                $content = str_replace("\n", "<br>", $j_content);
                $check_log = new CheckLog;
                $check_log->reply_id = $reply_id;
                $check_log->content = $content;
                $check_log->admin = $admin_id;
                $wpy = $check_log->save();

                // binway需求：工单审核建议进入工单回复列表中 - start
                if ($check_status == '1') {
                    $check_content = $work_reply->created_at.' 审核未通过；建议如下：<br>';
                } else {
                    $check_content = $work_reply->created_at.' 审核已通过；建议如下：<br>';
                }
                $check_reply = new WorkReply;
                $check_reply->work_order_id = $work_id;
                $check_reply->admin_id = $admin_id;
                $check_reply->reply = $check_content . $content;
                $check_reply->enclosure_id = 0;
                $check_reply->system = 2;
                $check_reply->reply_anchor = '';
                $wpy = $check_reply->save();
                // binway需求：工单审核建议进入工单回复列表中 - end
            } else {
                $wpy = 'true';
            }

            if ($wpy) {
                // 用户回复邮件队列处理
                $group_id = DB::table($com->id . '_work_order')->where('id', $work_id)->pluck('group_id'); // 获得组别
                $user_id = DB::table($com->id . '_work_order')->where('id', $work_id)->pluck('user_id'); // 获得客户
                $reply = DB::table($com->id . '_work_reply')->where('id', $reply_id)->pluck('reply'); // 获得回复内容
                $path = Config::get('app.com_path'); // 获得公司路由
                $this->quereAdmin($com, $reply_id, $user_id, $group_id, $reply, $path, $work_id);

                return Redirect::to($com->path . '/admin/check')->with('success', '回复ID: '.$reply_id.' 审核成功！');
            } else {
                return Redirect::to($com->path . '/admin/check')->with('error', '回复ID: '.$reply_id.' 审核建议失败！');
            }
            exit;
        } else {
            return Redirect::to($com->path . '/admin/check')->with('error', '回复ID: '.$reply_id.' 审核失败！');
        }
    }

    // 工单审核建议
    public function checkLog($com, $work_id = NULL, $reply_id = NULL)
    {
        // 获得当前回复
        $reply = DB::table($com->id . '_work_reply')->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
        ->join($com->id . '_work_order', $com->id . '_work_order.id', '=', $com->id . '_work_reply.work_order_id')
        ->join('admin_user', 'admin_user.user_id', '=', 'users.id')->where($com->id . '_work_reply.id', $reply_id)
        ->select($com->id . '_work_reply.*', 'users.name as admin', 'admin_user.position')->paginate();

        // 获取审核建议
        $log = DB::table($com->id . '_check_log')->join('users', 'users.id', '=', $com->id . '_check_log.admin')
        ->join('admin_user', 'admin_user.user_id', '=', 'users.id')->where($com->id . '_check_log.reply_id', $reply_id)
        ->select($com->id . '_check_log.*', 'users.name as admin', 'admin_user.position')->paginate();

        // 加载模板
        return View::make('com/admin/check/log')->with('com', $com)->with(compact('reply'))->with(compact('log'));
    }

    // 工单详情记录获取
    public function workCheckDetail($com, $workID = NULL)
    {
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

        // 获取客户的提问
        $asks = DB::table($com->id . '_work_ask')->where('work_order_id', $workID)->get();
        foreach ($asks as $uk => $uv) {
            $name = DB::table('users')->where('id', $uv->user_id)->pluck('name');
            $asks[$uk]->name = $name;
        }

        // 获取多用户的回答
        $replys = DB::table($com->id . '_work_reply')
        ->join('users', 'users.id', '=', $com->id . '_work_reply.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_reply.admin_id')
        ->where($com->id . '_work_reply.work_order_id', '=', $workID)->select($com->id . '_work_reply.*', 
        'users.name', 'admin_user.position')->orderBy($com->id . '_work_reply.work_order_id', 'desc')->get();

        // 客户提问与用户回答按时间正序排列
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

        return $content;
    }

    // 邮件队列处理
    public function quereAdmin($com, $reply_id = NULL, $user_id = NULL, $group_id = NULL, $content = NULL, $path = NULL, $work_id = NULL)
    {
    	// 插入公用矩阵邮件队列
        // 查询接收人邮箱是否开启接收功能
        $status = 'true'; // 队列开关
        $enable = DB::table('site_user')->where('user_id', $user_id)->pluck('enable');
        if ($enable == 'stop') {
            $status = 'false';
        }

        // 发起人企业服务邮箱
        $email_id = DB::table($com->id . '_groups')->where('id', $group_id)->pluck('email_id');
        if ($email_id > 0) {
            $from_email = DB::table('company_email')->where('disabled', 'false')->where('id', $email_id)->pluck('email');
            if (!$from_email)
                $status = 'false';
        } else {
            $status = 'false';
        }

        // 查询用户回复列表是否需要验证并通过
        $check = DB::table($com->id . '_work_reply')->where('id', $reply_id)->pluck('check');
        if ($check == 1) {
            $status = 'false';
        }

        if ($status != 'false') {
            // 接受人邮箱
            $to_email = DB::table('users')->where('id', $user_id)->pluck('user_name');
            // 公用矩阵邮件队列处理
            $quere = new Quere;
            $quere->company_id = $com->id;
            $quere->from_email = $from_email;
            $quere->to_email = $to_email;
            $quere->reply_id = $reply_id;

            // 插入私有公司用户邮件队列
            if ($quere->save()) {
                $user_quere = new UserQuere;
                $user_quere->quere_id = $quere->id;
                $user_quere->company_id = $com->id;
                $user_quere->from_email = $from_email;
                $user_quere->to_email = $to_email;
                $user_quere->reply_id = $reply_id;
                $user_quere->content = $content;
                $user_quere->path = $path;
                $user_quere->work_id = $work_id;
                $user_quere->save();
            }
        }
    }
}
