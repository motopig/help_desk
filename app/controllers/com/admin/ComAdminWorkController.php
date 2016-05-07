<?php

/**
 * Class ComAdminWorkController
 *
 * 公司后台工单管理控制器 by no
 */
class ComAdminWorkController extends ComAdminController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();
    }

    // 设置工单分页数
    private $page = 9;

    // 获取工单总数
    public function workCounts($com, $level, $group_id = NULL)
    {
        if ($level == 4) {
            // 所属组工单池总数
            // $workCount['1'] = DB::table($com->id . '_work_order')->whereIn('group_id', $group_id)
            // ->where('work_status', '=', '1')->count();

            // binway需求：工单池客服不许看见，主要应对他们的业务，每个工单都必须要主管手动分配
            $workCount['1'] = 0;

            // 个人待处理工单总数
            $workCount['2'] = DB::table($com->id . '_work_order')->where('admin_id', '=', Auth::user()->id)
            ->where('work_status', '=', '2')->count();

            // 个人已处理工单总数
            $workCount['3'] = DB::table($com->id . '_work_order')->where('admin_id', '=', Auth::user()->id)
            ->where('work_status', '=', '3')->count();

            // 个人已关闭工单总数
            $workCount['4'] = DB::table($com->id . '_work_order')->where('admin_id', '=', Auth::user()->id)
            ->where('work_status', '=', '4')->count();
        } else if ($level == 3 || $level == 'g' || $level == 'm') {
            // 获取作为组长的所属组别
            $Grade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');

            // 个人待处理工单总数
            $tadminCount = DB::table($com->id . '_work_order')->where('admin_id', '=', Auth::user()->id)
            ->where('work_status', '=', '2')->count();
            // 个人已处理工单总数
            $thadminCount = DB::table($com->id . '_work_order')->where('admin_id', '=', Auth::user()->id)
            ->where('work_status', '=', '3')->count();
            // 个人已关闭工单总数
            $fadminCount = DB::table($com->id . '_work_order')->where('admin_id', '=', Auth::user()->id)
            ->where('work_status', '=', '4')->count();

            // 组员待处理工单总数
            $tgroupCount = DB::table($com->id . '_work_order')->where('admin_id', '!=', Auth::user()->id)
            ->whereIn('group_id', $Grade)->where('work_status', '=', '2')->count();
            // 组员已处理工单总数
            $thgroupCount = DB::table($com->id . '_work_order')->where('admin_id', '!=', Auth::user()->id)
            ->whereIn('group_id', $Grade)->where('work_status', '=', '3')->count();
            // 组员已关闭工单总数
            $fgroupCount = DB::table($com->id . '_work_order')->where('admin_id', '!=', Auth::user()->id)
            ->whereIn('group_id', $Grade)->where('work_status', '=', '4')->count();

            // 所属组工单池总数
            $workCount['1'] = DB::table($com->id . '_work_order')->whereIn('group_id', $group_id)
            ->where('work_status', '=', '1')->count();
            // 所属组待处理工单总数组合
            $workCount['2'] = $tadminCount + $tgroupCount;
            $workCount['2g'] = $tgroupCount;
            $workCount['2m'] = $tadminCount;
            // 所属组已处理工单总数组合
            $workCount['3'] = $thadminCount + $thgroupCount;
            $workCount['3g'] = $thgroupCount;
            $workCount['3m'] = $thadminCount;
            // 所属组已关闭工单总数组合
            $workCount['4'] = $fadminCount + $fgroupCount;
            $workCount['4g'] = $fgroupCount;
            $workCount['4m'] = $fadminCount;
        } else {
            // 公司工单池总数
            $workCount['1'] = DB::table($com->id . '_work_order')->where('work_status', '=', '1')->count();
            // 公司待处理工单总数
            $workCount['2'] = DB::table($com->id . '_work_order')->where('work_status', '=', '2')->count();
            // 公司已处理工单总数
            $workCount['3'] = DB::table($com->id . '_work_order')->where('work_status', '=', '3')->count();
            // 公司已关闭工单总数
            $workCount['4'] = DB::table($com->id . '_work_order')->where('work_status', '=', '4')->count();
        }

        // 邀请工单总数
        // 获取受邀表中的工单号
        $cilmWorkOrder = WorkClaim::where('admin_id', Auth::user()->id)->get()->lists('work_order_id');
        if (count($cilmWorkOrder) == 0) {
            $workCount['5'] = 0;
        } else {
            $workCount['5'] = DB::table($com->id . '_work_order')->where('work_status', '=', '2')
            ->whereIn($com->id . '_work_order.id', $cilmWorkOrder)->count();
        }

        return $workCount;
    }

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

    // 首页(工单池列表获取)
    public function index($com)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');
        
        if ($level == 3 || $level == 4) {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');

            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取所属组工单池列表
            if ($workCount['1'] > 0) {
                $workerOrder = $this->workStatusIndex($com, $group_id, $this->page);
            } else {
                $workerOrder = '';
            }
        } else {
            // 获取公司工单总数
            $workCount = $this->workCounts($com, $level, $group_id = NULL);

            // 获取公司工单池列表
            if ($workCount['1'] > 0) {
                $workerOrder = $this->workStatusIndex($com, $group_id = NULL, $this->page);
            } else {
                $workerOrder = '';
            }
        }

        // 工单最新回复排序 - no
        if ($workerOrder == '') {
            $workerOrders = '';
        } else {
            $workList = $workerOrder->toArray(); // 对象转为数组
            $workerOrders = $this->array_sort_work($workList['data'], 'time', 'desc'); // 最新时间倒叙
        }

        return View::make('com/admin/work/index')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('workerOrders'));
    }

    // 工单池列表获取处理
    public function workStatusIndex($com, $group_id = NULL, $page)
    {
        if ($group_id == NULL) {
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where($com->id . '_work_order.work_status', '=', '1')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
            $wId = WorkOrder::where('work_status', '1')->get()->lists('id');
            if(is_array($wId)) {
                $wId = implode(',', $wId);
            }
        } else {
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where($com->id . '_work_order.work_status', '=', '1')
            ->whereIn($com->id . '_work_order.group_id', $group_id)
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
            $wId = WorkOrder::where('work_status', '1')->whereIn('group_id', $group_id)->get()->lists('id');
            if(is_array($wId)) {
                $wId = implode(',', $wId);
            }
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

    // 用户待处理工单列表
    public function getUntreated($com, $level)
    {
        if ($level == '1' || $level == '2') {
            // 获取公司工单总数
            $workCount = $this->workCounts($com, $level, $group_id = NULL);

            // 获取公司待处理工单列表
            if ($workCount['2'] > 0) {
                $workerOrder = $this->workStatusUntreatedAll($com, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'a';
        } else if ($level == '4') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取个人待处理工单列表
            if ($workCount['2'] > 0) {
                $workerOrder = $this->workStatusUntreatedMe($com, $group_id, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'o';
        } else if ($level == 'm') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取组长个人待处理工单列表
            if ($workCount['2m'] > 0) {
                $workerOrder = $this->workStatusUntreatedMe($com, $group_id, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'm';
        } else if ($level == 'g') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取组待处理工单列表
            if ($workCount['2g'] > 0) {
                $workerOrder = $this->workStatusUntreatedGroup($com, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'g';
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

        return View::make('com/admin/work/untreated')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('grade'))->with(compact('workerOrders'));
    }

    // 用户等级1|2待处理工单列表获取处理
    public function workStatusUntreatedAll($com, $page)
    {
        // 待处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '2')
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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
        $rsqls = "select id, head, name from " . $TablePrefix ."users where id in (" . $adminHeadId .") ORDER BY created_at DESC";
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

    // 用户等级4|3(m)待处理工单列表获取处理
    public function workStatusUntreatedMe($com, $group_id, $page)
    {
        // 待处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where('admin_id', '=', Auth::user()->id)
        ->where($com->id . '_work_order.work_status', '=', '2')
        ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
        $com->id . '_groups.group_name') 
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);

        // 获取客户头像
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
        $wId = WorkOrder::where('work_status', '2')->where('admin_id', '=', Auth::user()->id)->get()->lists('id');
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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

    // 用户等级3(g)待处理工单列表获取处理
    public function workStatusUntreatedGroup($com, $page)
    {
        // 获取作为组长的组别
        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
        if (count($tGrade) > 0) {
            // 所属组组员待处理工单
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)
            ->where($com->id . '_work_order.work_status', '=', '2')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }

        // 获取客户头像
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
        $wId = WorkOrder::where('work_status', '2')->where('admin_id', '!=', Auth::user()->id)
        ->whereIn('group_id', $tGrade)->get()->lists('id');
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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
    public function getProcessed($com, $level)
    {
        if ($level == '1' || $level == '2') {
            // 获取公司工单总数
            $workCount = $this->workCounts($com, $level, $group_id = NULL);

            // 获取公司已处理工单列表
            if ($workCount['3'] > 0) {
                $workerOrder = $this->workStatusProcessedAll($com, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'a';
        } else if ($level == '4') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取个人已处理工单列表
            if ($workCount['3'] > 0) {
                $workerOrder = $this->workStatusProcessedMe($com, $group_id, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'o';
        } else if ($level == 'm') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取组长个人已处理工单列表
            if ($workCount['3m'] > 0) {
                $workerOrder = $this->workStatusProcessedMe($com, $group_id, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'm';
        } else if ($level == 'g') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取组已处理工单列表
            if ($workCount['3g'] > 0) {
                $workerOrder = $this->workStatusProcessedGroup($com, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'g';
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

        return View::make('com/admin/work/processed')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('grade'))->with(compact('workerOrders'));
    }

    // 用户等级1|2已处理工单列表获取处理
    public function workStatusProcessedAll($com, $page)
    {
        // 已处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '3')
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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

    // 用户等级4|3(m)已处理工单列表获取处理
    public function workStatusProcessedMe($com, $group_id, $page)
    {
        // 已处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where('admin_id', '=', Auth::user()->id)
        ->where($com->id . '_work_order.work_status', '=', '3')
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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

    // 用户等级3(g)已处理工单列表获取处理
    public function workStatusProcessedGroup($com, $page)
    {
        // 获取作为组长的组别
        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
        if (count($tGrade) > 0) {
            // 所属组组员待处理工单
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)
            ->where($com->id . '_work_order.work_status', '=', '3')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }

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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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
    public function getClosed($com, $level)
    {
        if ($level == '1' || $level == '2') {
            // 获取公司工单总数
            $workCount = $this->workCounts($com, $level, $group_id = NULL);

            // 获取公司已关闭工单列表
            if ($workCount['4'] > 0) {
                $workerOrder = $this->workStatusClosedAll($com, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'a';
        } else if ($level == '4') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取个人已关闭工单列表
            if ($workCount['4'] > 0) {
                $workerOrder = $this->workStatusClosedMe($com, $group_id, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'o';
        } else if ($level == 'm') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取组长个人已关闭工单列表
            if ($workCount['4m'] > 0) {
                $workerOrder = $this->workStatusClosedMe($com, $group_id, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'm';
        } else if ($level == 'g') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            // 获取组已关闭工单列表
            if ($workCount['4g'] > 0) {
                $workerOrder = $this->workStatusClosedGroup($com, $this->page);
            } else {
                $workerOrder = '';
            }
            $grade = 'g';
        }

        // 关闭工单对象转为数组 - no
        if ($workerOrder == '') {
            $workerOrders = '';
        } else {
            $workList = $workerOrder->toArray();
            $workerOrders = $workList['data'];
        }

        return View::make('com/admin/work/closed')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('grade'))->with(compact('workerOrders'));
    }

    // 用户等级1|2已关闭工单列表获取处理
    public function workStatusClosedAll($com, $page)
    {
        // 已关闭工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '4')
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

    // 用户等级4|3(m)已关闭工单列表获取处理
    public function workStatusClosedMe($com, $group_id, $page)
    {
        // 已关闭工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where('admin_id', '=', Auth::user()->id)
        ->where($com->id . '_work_order.work_status', '=', '4')
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

    // 用户等级3(g)已关闭工单列表获取处理
    public function workStatusClosedGroup($com, $page)
    {
        // 获取作为组长的组别
        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
        if (count($tGrade) > 0) {
            // 所属组组员已关闭工单
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)
            ->where($com->id . '_work_order.work_status', '=', '4')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }

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

    // 工单邀请列表
    public function getInvite($com, $level)
    {
        if ($level == '1' || $level == '2') {
            // 获取公司工单总数
            $workCount = $this->workCounts($com, $level, $group_id = NULL);
        } else if ($level == '4') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);
        } else if ($level == '3') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);
        }

        // 获取邀请处理中工单列表
        if ($workCount['5'] > 0) {
            $workerOrder = $this->climUntreatedAll($com, $this->page);
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

        return View::make('com/admin/work/invite')->with('com', $com)
        ->with(compact('workCount'))->with(compact('workerOrder'))->with(compact('workerOrders'));
    }

    // 工单邀请列表获取处理
    public function climUntreatedAll($com, $page)
    {
        // 获取受邀表中的工单号
        $cilmWorkOrder = WorkClaim::where('admin_id', Auth::user()->id)->get()->lists('work_order_id');

        // 待处理工单列表获取处理
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '2')->whereIn($com->id . '_work_order.id', $cilmWorkOrder)
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
        ->select('users.user_name', 'users.name', 'users.id', 'admin_user.position', 'users.head as admin_head',
        'users.created_at as admin_created_at', $com->id . '_work_order.id as work_order_id')
        ->orderBy($com->id . '_work_order.id', 'desc')->paginate();
        
        // foreach ($admins as $key => $value) {
        //     echo 'z<pre>';
        //     print_r($value);
        // }
        
        // exit;

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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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

    // 工单详情页面
    public function workDetail($com, $workID = NULL)
    {
        // 判断是否为工单通知跳转 - no
        if ($_GET) {
            if ($_GET['message_id'] > 0) {
                // 修改已认领工单的通知记录为已阅读 － no
                $workMessage = WorkMessage::find($_GET['message_id']);
                if ($workMessage->work_status != '1') {
                    $workMessage->status = 'true';
                    $workMessage->save();
                }
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
        ->where($com->id . '_work_reply.work_order_id', '=', $workID)->select($com->id . '_work_reply.*', 
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
            return View::make('com/admin/work/workdetail')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        } else if ($workerOrder[0]->work_status == '2') {
            return View::make('com/admin/work/workdetailuntreated')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        } else if ($workerOrder[0]->work_status == '3') {
            return View::make('com/admin/work/workdetailprocessed')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        } else if ($workerOrder[0]->work_status == '4') {
            return View::make('com/admin/work/workdetailclosed')->with('com', $com)->with(compact('workerOrder'))
            ->with(compact('content'))->with(compact('enclosureAll'));
        }
    }

    // 邀请工单详情页
    public function workDetailInvite($com, $workID = NULL)
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
        ->where($com->id . '_work_reply.work_order_id', '=', $workID)->select($com->id . '_work_reply.*', 
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
        return View::make('com/admin/work/workdetailinvite')->with('com', $com)->with(compact('workerOrder'))
        ->with(compact('content'))->with(compact('enclosureAll'));
    }

    // 工单认领
    public function workClaim($com, $workID = NULL)
    {
        $work_order = WorkOrder::find($workID);
        if ($work_order->work_status != '1') {
            return Redirect::to($com->path . '/admin/work')->with('error', '工单已认领！');
        } else {
            $work_order->work_status = '2';
            $work_order->admin_id = Auth::user()->id;
            
            if ($work_order->save()) {
                $position = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('position');

                // 工单认领系统回复
                $workreply = new WorkReply();
                $workreply->work_order_id = $workID;
                $workreply->admin_id = Auth::user()->id;
                if ($position != '') {
                    $workreply->reply = "工单 #". $workID . "已接受处理，受理人为" . Auth::user()->name . " (" . $position . ")；请耐心等待...";
                } else {
                    $workreply->reply = "工单 #". $workID . "已接受处理，受理人为" . Auth::user()->name . " (管理员)；请耐心等待...";
                }
                $workreply->enclosure_id = 0;
                $workreply->system = 1;

                if ($workreply->save()) {
                    // 工单通知信息处理 － no
                    $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                    if ($message_id) {
                        $work_message = WorkMessage::find($message_id);
                        $work_message->work_status = $work_order->work_status;
                        $work_message->type = 'reply';
                        $work_message->status = 'false';
                        $work_message->reply_id = $workreply->id;
                        $work_message->admin_id = Auth::user()->id;
                        $work_message->reply_admin_id = Auth::user()->id;

                        $work_order = WorkOrder::find($workID);
                        $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                        $user_message = DB::table('site_user')->where('user_id', $user_id)->pluck('message');
                        if ($user_message == '0') {
                            $work_message->save();
                        }
                    }

                    // 用户发起内容接收邮件队列
                    // 获得组别
                    $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
                    // 获得公司路由
                    $path = Config::get('app.com_path');
                    $this->quereAdmin($com, $workreply->id, $user_id, $group_id, $workreply->reply, $path, $workID);

                    return Redirect::to($com->path . '/admin/work')->with('success', '工单#' . $workID . '认领成功！');
                } else {
                    return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '认领系统回复失败！');
                }
            } else {
                return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '认领失败！');
            }
        }
    }

    // 回复处理工单
    public function workProcessed($com, $workID = NULL)
    {   
        // 验证回复内容
        $rules = array(
            'reply' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->with('error', '请输入回复内容！');
        }
        
        // 验证是否为@回复内容
        $arr = explode("\r\n", Input::get('reply'));
        $arr_first = substr($arr['0'], 0, 1);
        if ($arr_first == '@') {
            $arr_str = explode(" ", $arr['0']);
            if (!$arr['1']) {
                return Redirect::back()->with('error', $arr_str['0'] . ', 请在第二行输入回复内容！');
            }

            // // 验证客户回复ID是否存在 - 再议
            // $arr_str_id = explode("#", $arr_str['1']);
            // $anchorID = DB::table($com->id . '_work_ask')->where('id', $arr_str_id['1'])->pluck('id');
            // if (!$anchorID) {
            //     return Redirect::back()->with('error', $arr_str['0'] . ', 回复时间不存在！');
            // }

            // $reply_anchor = 'ask' . $anchorID;

            $anchorTime = $arr_str['1'] . " " . $arr_str['2'];
            $anchorTimeData = DB::table($com->id . '_work_ask')->where('created_at', $anchorTime)->pluck('created_at');
            if (!$anchorTimeData) {
                return Redirect::back()->with('error', $arr_str['0'] . ', 回复时间不存在！');
            }

            $reply_anchor = $anchorTimeData;
            // 删除第一行@内容组合成新内容
            unset($arr['0']);
            $new_arr = implode("\r\n", $arr); 
            // 将换行符转换成<br />
            $reply_content = str_replace("\r\n", "<br>", $new_arr);
        } else {
            $reply_anchor = '';
            // 将换行符转换成<br />
            $replyContent = e(Input::get('reply'));
            $reply_content = str_replace("\r\n", "<br>", $replyContent);
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
                if ($suffixName['1'] == 'docx' || $suffixName['1'] == 'xlsx' || $suffixName['1'] == 'txt') {
                    $encl->type = 'file';
                } else {
                    $encl->type = 'image';
                }
                $encl->suffix = $file->getClientOriginalExtension();
                $encl->path = $destinationPath . '/' . $filename;

                if ($encl->save()) {
                    $reply_enclosure_id = $encl->id;
                }
            } else {
                return Redirect::back()->with('error', '上传附件失败！');
            }
        } else {
            $reply_enclosure_id = '0';
        }

        // 后台回复工单
        $reply = new WorkReply;
        $reply->work_order_id = $workID;
        $reply->admin_id = Auth::user()->id;
        $reply->reply = $reply_content;
        $reply->enclosure_id = $reply_enclosure_id;
        $reply->reply_anchor = $reply_anchor;

        // 判断工单回复是否需要审核
        $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id'); // 获得组别
        $check = DB::table($com->id . '_groups')->where('id', $group_id)->pluck('check'); // 查询用户回复列表是否需要验证并通过
        if ($check == '1') {
            $level = Config::get('auth.com_admin_level_id'); // 判断管理员等级
            if ($level == 4) {
                // $reply->check = 1;
                // $reply->checkreply = 1;
                
                // binway需求：客服可设置不需要审核发送工单邮件
                $admin_check = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('check');
                if ($admin_check == '1') {
                    $reply->check = 1;
                    $reply->checkreply = 1;
                }
            }
        }
        
        // 判断回复可见隐藏
        if (Input::get('eyes') == '2') {
            $reply->system = 2;
        } else {
            $reply->system = 0;
        }
        
        if ($reply->save()) {
            // 工单通知信息处理 － no
            // 不需要审核的工单回复以及回复未隐藏的才处理通知消息
            if ($reply->system != 2 && $reply->check != 1) {
                $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                if ($message_id) {
                    $work_message = WorkMessage::find($message_id);
                    $work_message->work_status = '2';
                    $work_message->type = 'reply';
                    $work_message->status = 'false';
                    $work_message->reply_id = $reply->id;
                    $work_message->reply_admin_id = Auth::user()->id;

                    $work_order = WorkOrder::find($workID);
                    $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                    $user_message = DB::table('site_user')->where('user_id', $user_id)->pluck('message');
                    if ($user_message == '0') {
                        $work_message->save();
                    }
                }
            }
            
            // 用户发起内容接收邮件队列
            // 获得组别
            $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
            // 获得客户
            $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
            // 获得公司路由
            $path = Config::get('app.com_path');
            $this->quereAdmin($com, $reply->id, $user_id, $group_id, $reply_content, $path, $workID);

            return Redirect::to($com->path . '/admin/work/workdetail/' . $workID)
            ->with('success', '工单#' . $workID . '回复成功！');
        } else {
            return Redirect::back()->with('error', '工单#' . $workID . '回复失败！');
        }
    }

    // 回复处理邀请工单
    public function workProcessedInvite($com, $workID = NULL)
    {
        // 验证回复内容
        $rules = array(
            'reply' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->with('error', '请输入回复内容！');
        }
        
        // 验证是否为@回复内容
        $arr = explode("\r\n", Input::get('reply'));
        $arr_first = substr($arr['0'], 0, 1);
        if ($arr_first == '@') {
            $arr_str = explode(" ", $arr['0']);
            if (!$arr['1']) {
                return Redirect::back()->with('error', $arr_str['0'] . ', 请在第二行输入回复内容！');
            }

            // // 验证客户回复ID是否存在 - 再议
            // $arr_str_id = explode("#", $arr_str['1']);
            // $anchorID = DB::table($com->id . '_work_ask')->where('id', $arr_str_id['1'])->pluck('id');
            // if (!$anchorID) {
            //     return Redirect::back()->with('error', $arr_str['0'] . ', 回复时间不存在！');
            // }

            // $reply_anchor = 'ask' . $anchorID;

            $anchorTime = $arr_str['1'] . " " . $arr_str['2'];
            $anchorTimeData = DB::table($com->id . '_work_ask')->where('created_at', $anchorTime)->pluck('created_at');
            if (!$anchorTimeData) {
                return Redirect::back()->with('error', $arr_str['0'] . ', 回复时间不存在！');
            }

            $reply_anchor = $anchorTimeData;
            // 删除第一行@内容组合成新内容
            unset($arr['0']);
            $new_arr = implode("\r\n", $arr); 
            // 将换行符转换成<br />
            $reply_content = str_replace("\r\n", "<br>", $new_arr);
        } else {
            $reply_anchor = '';
            // 将换行符转换成<br />
            $replyContent = e(Input::get('reply'));
            $reply_content = str_replace("\r\n", "<br>", $replyContent);
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
                    $reply_enclosure_id = $encl->id;
                }
            } else {
                return Redirect::back()->with('error', '上传附件失败！');
            }
        } else {
            $reply_enclosure_id = '0';
        }

        // 后台回复工单
        $reply = new WorkReply;
        $reply->work_order_id = $workID;
        $reply->admin_id = Auth::user()->id;
        $reply->reply = $reply_content;
        $reply->enclosure_id = $reply_enclosure_id;
        $reply->reply_anchor =$reply_anchor;

        // 判断工单回复是否需要审核
        $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id'); // 获得组别
        $check = DB::table($com->id . '_groups')->where('id', $group_id)->pluck('check'); // 查询用户回复列表是否需要验证并通过
        if ($check == '1') {
            $level = Config::get('auth.com_admin_level_id'); // 判断管理员等级
            if ($level == 4) {
                $reply->check = 1;
                $reply->checkreply = 1;
            } else if ($level == 3) {
                $grade = DB::table($com->id . '_group_admin_user')->where('group_id', $group_id)
                ->where('user_id', Auth::user()->id)->pluck('grade'); // 获得组员级别

                if (!$grade) {
                    $reply->check = 1;
                    $reply->checkreply = 1;
                }
            }
        }

        // 判断回复可见隐藏
        if (Input::get('eyes') == '2') {
            $reply->system = 2;
        } else {
            $reply->system = 0;
        }
        
        if ($reply->save()) {
            // 用户发起内容接收邮件队列
            // 获得组别
            $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
            // 获得客户
            $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
            // 获得公司路由
            $path = Config::get('app.com_path');
            $this->quereAdmin($com, $reply->id, $user_id, $group_id, $reply_content, $path, $workID);

            return Redirect::to($com->path . '/admin/work/workinvite/' . $workID)
            ->with('success', '工单#' . $workID . '回复成功！');
        } else {
            return Redirect::back()->with('error', '工单#' . $workID . '回复失败！');
        }
    }

    // 工单分配
    public function getAllot($com, $workID = NULL)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');
        // 获取当前工单处理人员
        $adminID = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('admin_id');
        // 获取当前工单状态
        $workStatus = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('work_status');
        
        if ($level == 1 || $level == 2) {
            // 获取公司所有客服
            $user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id')
            ->groupBy('user_id')->orderBy('grade')->get();
        } else {
            // 获取工单所属组别
            $groupID = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');

            // 获取组下所有客服
            $user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id')
            ->where($com->id . '_group_admin_user.group_id', $groupID)->where($com->id . '_group_admin_user.grade', '2')
            ->groupBy('user_id')->orderBy('grade')->get();
        }
        
        // 判断是否为重启分配
        if (!$_GET) {
            $manageoriginal = 'false';
        } else {
            if ($_GET['manageoriginal'] == 'true') {
                $manageoriginal = 'true';
            } else {
                $manageoriginal = 'false';
            }
        }



        return View::make('com/admin/work/getallot')->with('com', $com)->with(compact('user'))->with(compact('workID'))
        ->with('adminID', $adminID)->with('manageoriginal', $manageoriginal)->with('workStatus', $workStatus);
    }

    // 工单分配处理
    public function allotManage($com, $workID = NULL)
    {
        // 获取分配用户的姓名及职位
        $userID = $_GET['userID'];
        $adminuser = AdminUser::join('users', 'users.id', '=', 'admin_user.user_id')
        ->where('admin_user.user_id', '=', $userID)->select('users.name', 'admin_user.position')->get();
        // 获取当前工单的状态
        $work_status = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('work_status');
        $work_order = WorkOrder::find($workID);
        if ($work_status == '1' || $work_status == '3') {
            $work_order->work_status = '2'; // 更改状态为处理中
        }
        $work_order->admin_id = $userID; // 指派工单受理人

        if ($work_order->save()) {
            $allotPosition = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('position'); // 分配人职位
            $adminName = $adminuser[0]->name; // 受理人姓名
            $adminPosition = $adminuser[0]->position; // 受理人职位

            // 工单分配系统回复
            $workreply = new WorkReply();
            $workreply->work_order_id = $workID;
            $workreply->admin_id = Auth::user()->id;
            if ($allotPosition != '') {
                $reply = "工单 #". $workID . "已接受处理，" . Auth::user()->name . " (" . $allotPosition . ")分配至 ";
            } else {
                $reply = "工单 #". $workID . "已接受处理，" . Auth::user()->name . " (管理员)分配至 ";
            }
            if ($adminPosition != '') {
                $reply .= $adminName . " (" . $adminPosition . ")；请耐心等待...";
            } else {
                $reply .= $adminName . " (管理员)；请耐心等待...";
            }

            $workreply->reply = $reply;
            $workreply->enclosure_id = 0;
            $workreply->system = 1;
            $workreply->reply_anchor = '';

            if ($workreply->save()) {
                // 工单通知信息处理 － no
                $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                if ($message_id) {
                    $work_message = WorkMessage::find($message_id);
                    $work_message->work_status = $work_order->work_status;
                    $work_message->type = 'reply';
                    $work_message->status = 'false';
                    $work_message->reply_id = $workreply->id;
                    $work_message->admin_id = $work_order->admin_id;
                    $work_message->reply_admin_id = Auth::user()->id;

                    $work_order = WorkOrder::find($workID);
                    $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                    $user_message = DB::table('site_user')->where('user_id', $user_id)->pluck('message');
                    if ($user_message == '0') {
                        $work_message->save();
                    }
                }

                // 用户发起内容接收邮件队列
                // 获得组别
                $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
                // 获得客户
                $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                // 获得公司路由
                $path = Config::get('app.com_path');
                $this->quereAdmin($com, $workreply->id, $user_id, $group_id, $reply, $path, $workID);

                return Redirect::to($com->path . '/admin/work')->with('success', '工单#' . $workID . '分配成功！');
            } else {
                return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '分配系统回复失败！');
            }
        } else {
            return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '分配失败！');
        }
    }

    // 工单重启分配给原受理人
    public function allotManageOriginal($com, $workID = NULL)
    {
        // 获取员工单分配用户的姓名及职位
        $userID = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('admin_id');
        $adminuser = AdminUser::join('users', 'users.id', '=', 'admin_user.user_id')
        ->where('admin_user.user_id', '=', $userID)->select('users.name', 'admin_user.position')->get();
        // 获取当前工单的状态
        $work_status = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('work_status');
        $work_order = WorkOrder::find($workID);
        if ($work_status == '3') {
            $work_order->work_status = '2'; // 更改状态为处理中
        }
        $work_order->admin_id = $userID; // 指派工单受理人

        if ($work_order->save()) {
            $allotPosition = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('position'); // 分配人职位
            $adminName = $adminuser[0]->name; // 受理人姓名
            $adminPosition = $adminuser[0]->position; // 受理人职位

            // 工单分配系统回复
            $workreply = new WorkReply();
            $workreply->work_order_id = $workID;
            $workreply->admin_id = Auth::user()->id;
            if ($allotPosition != '') {
                $reply = "工单 #". $workID . "已接受处理，" . Auth::user()->name . " (" . $allotPosition . ")分配至 ";
            } else {
                $reply = "工单 #". $workID . "已接受处理，" . Auth::user()->name . " (管理员)分配至 ";
            }
            if ($adminPosition != '') {
                $reply .= $adminName . " (" . $adminPosition . ")；请耐心等待...";
            } else {
                $reply .= $adminName . " (管理员)；请耐心等待...";
            }

            $workreply->reply = $reply;
            $workreply->enclosure_id = 0;
            $workreply->system = 1;

            if ($workreply->save()) {
                // 工单通知信息处理 － no
                $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                if ($message_id) {
                    $work_message = WorkMessage::find($message_id);
                    $work_message->work_status = $work_order->work_status;
                    $work_message->type = 'reply';
                    $work_message->status = 'false';
                    $work_message->reply_id = $workreply->id;
                    $work_message->admin_id = $work_order->admin_id;
                    $work_message->reply_admin_id = Auth::user()->id;

                    $work_order = WorkOrder::find($workID);
                    $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                    $user_message = DB::table('site_user')->where('user_id', $user_id)->pluck('message');
                    if ($user_message == '0') {
                        $work_message->save();
                    }
                }

                // 用户发起内容接收邮件队列
                // 获得组别
                $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
                // 获得客户
                $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                // 获得公司路由
                $path = Config::get('app.com_path');
                $this->quereAdmin($com, $workreply->id, $user_id, $group_id, $reply, $path, $workID);

                return Redirect::to($com->path . '/admin/work')->with('success', '工单#' . $workID . '分配成功！');
            } else {
                return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '分配系统回复失败！');
            }
        } else {
            return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '分配失败！');
        }
    }

    // 工单邀请
    public function getDispatch($com, $workID = NULL)
    {
        // 获取工单当前参与人ID
        $adminid = WorkClaim::where('work_order_id', $workID)->get()->lists('admin_id');
        if (count($adminid) == 0) {
            $adminuser = 'false';

            $user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id')
            ->groupBy('user_id')->orderBy('grade')->get();
        } else {
            // 获取参与人信息
            $adminuser = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id')
            ->whereIn('users.id', $adminid)->groupBy('user_id')->orderBy('grade')->get();
            if (count($adminuser) == 0) {
                $adminuser = 'false';
            }

            // 获取除参与人、受理人外公司所有组人员信息
            $user = GroupAdminUser::join('users', 'users.id', '=', $com->id . '_group_admin_user.user_id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_group_admin_user.group_id')
            ->whereNotIn('users.id', $adminid)->groupBy('user_id')->orderBy('grade')->get();
            if (count($user) == 0) {
                $user = 'false';
            }
        }

        return View::make('com/admin/work/getdispatch')->with('com', $com)->with(compact('adminuser'))
        ->with(compact('user'))->with(compact('workID'));
    }

    // 工单邀请处理
    public function dispatchManage($com, $workID = NULL)
    {
        $adminuser = $_GET['adminuser'];
        $au_arr = explode(":", $adminuser);

        // 处理参与人
        $admin_replay = 'false'; // 参与人标识
        $adminid = WorkClaim::where('work_order_id', $workID)->get()->lists('admin_id'); //获取当前参与人ID
        if ($au_arr[0] == 0 && count($adminid) > 0) {
            if (DB::table($com->id . '_work_claim')->where('work_order_id', $workID)->whereIn('admin_id', $adminid)->delete()) {
                $admin_replay = 'true';
            }
            $admin_ids = $adminid;
        } else if ($au_arr[0] != 0 && count($adminid) > 0) {
            $admin_idsf = explode(",", $au_arr[0]);
            if (count($admin_idsf) != count($adminid)) {
                $admin_ids = array_diff($adminid, $admin_idsf);
                if (DB::table($com->id . '_work_claim')->where('work_order_id', $workID)->whereIn('admin_id', $admin_ids)->delete()) {
                    $admin_replay = 'true';
                }
            }
        }

        if ($admin_replay == 'true') {
            $allotPosition = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('position');
            if ($allotPosition == '') {
                $allotPosition = "管理员";
            }

            $admins = AdminUser::join('users', 'users.id', '=', 'admin_user.user_id')
            ->whereIn('users.id', $admin_ids)->groupBy('user_id')->get();
            $info = Auth::user()->name . ' (' . $allotPosition . ')已回收：';
            foreach ($admins as $a) {
                $info .=  $a->name . ' (' . $a->position . ')、';
            }
            $info = rtrim($info, '、');
            $info .= '工单 #' . $workID . '处理权限。';

            $workreplys = new WorkReply();
            $workreplys->work_order_id = $workID;
            $workreplys->admin_id = Auth::user()->id;
            $workreplys->reply = $info;
            $workreplys->enclosure_id = 0;
            $workreplys->system = 1;
            $workreplys->save();
        }
        
        // 处理受邀人
        if ($au_arr[1] != 0) {
            $user_id = explode(",", $au_arr[1]);

            foreach ($user_id as $uid) {
                $work_claim = new WorkClaim();
                $work_claim->work_order_id = $workID;
                $work_claim->admin_id = $uid;
                
                if ($work_claim->save()) {
                    $user_replay = 'true';
                }
            }

            if ($user_replay == 'true') {
                $allotPosition = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('position');
                if ($allotPosition == '') {
                    $allotPosition = "管理员";
                }

                $users = AdminUser::join('users', 'users.id', '=', 'admin_user.user_id')
                ->whereIn('users.id', $user_id)->groupBy('user_id')->get();
                $info = Auth::user()->name . ' (' . $allotPosition . ')已邀请：';
                foreach ($users as $u) {
                    $info .=  $u->name . ' (' . $u->position . ')、';
                }
                $info = rtrim($info, '、');
                $info .= '参与工单 #' . $workID . '处理中。';

                $workreply = new WorkReply();
                $workreply->work_order_id = $workID;
                $workreply->admin_id = Auth::user()->id;
                $workreply->reply = $info;
                $workreply->enclosure_id = 0;
                $workreply->system = 1;
                $workreply->save();

                // 用户发起内容接收邮件队列
                // 获得组别
                $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
                // 获得客户
                $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                // 获得公司路由
                $path = Config::get('app.com_path');
                $this->quereAdmin($com, $workreply->id, $user_id, $group_id, $info, $path, $workID);
            }
        }

        return Redirect::to($com->path . '/admin/work')->with('success', '工单#' . $workID . '邀请完成！');
    }

    // 工单完成处理
    public function workAchieve($com, $workID = NULL)
    {
        $work_order = WorkOrder::find($workID);
        if ($work_order->work_status != '2') {
            return Redirect::to($com->path . '/admin/work')->with('error', '工单未认领，无法处理！');
        } else {
            $work_order->work_status = '3';
            
            if ($work_order->save()) {
                $position = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('position');

                // 工单完成自动回复
                $workreply = new WorkReply();
                $workreply->work_order_id = $workID;
                $workreply->admin_id = Auth::user()->id;
                if ($position != '') {
                    $workreply->reply = "工单 #". $workID . "已处理，操作人为" . Auth::user()->name . " (" . $position . ")。";
                } else {
                    $workreply->reply = "工单 #". $workID . "已处理，操作人为" . Auth::user()->name . " (管理员)。";
                }
                $workreply->enclosure_id = 0;
                $workreply->system = 1;

                if ($workreply->save()) {
                    // 工单通知信息处理 － no
                    $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                    if ($message_id) {
                        $work_message = WorkMessage::find($message_id);
                        $work_message->work_status = $work_order->work_status;
                        $work_message->type = 'reply';
                        $work_message->status = 'false';
                        $work_message->reply_id = $workreply->id;
                        $work_message->reply_admin_id = Auth::user()->id;

                        $work_order = WorkOrder::find($workID);
                        $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                        $user_message = DB::table('site_user')->where('user_id', $user_id)->pluck('message');
                        if ($user_message == '0') {
                            $work_message->save();
                        }
                    }

                    // 用户发起内容接收邮件队列
                    // 获得组别
                    $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
                    // 获得客户
                    $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                    // 获得公司路由
                    $path = Config::get('app.com_path');
                    $this->quereAdmin($com, $workreply->id, $user_id, $group_id, $workreply->reply, $path, $workID);

                    return Redirect::to($com->path . '/admin/work')->with('success', '工单#' . $workID . '变更已处理成功！');
                } else {
                    return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '处理自动回复失败！');
                }
            } else {
                return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '变更已处理失败！');
            }
        }
    }

    // 工单关闭处理
    public function workClosed($com, $workID = NULL)
    {
        $work_order = WorkOrder::find($workID);
        // if ($work_order->work_status != '3') {
        //     return Redirect::to($com->path . '/admin/work')->with('error', '工单未处理，无法关闭！');
        // } else {
        // binway需求：组长(含组长)以上权限方便关闭无效工单
            $work_order->work_status = '4';
            
            if ($work_order->save()) {
                $position = DB::table('admin_user')->where('user_id', Auth::user()->id)->pluck('position');

                // 工单关闭自动回复
                $workreply = new WorkReply();
                $workreply->work_order_id = $workID;
                $workreply->admin_id = Auth::user()->id;
                if ($position != '') {
                    $workreply->reply = "工单 #". $workID . "已关闭，操作人为" . Auth::user()->name . " (" . $position . ")。";
                } else {
                    $workreply->reply = "工单 #". $workID . "已关闭，操作人为" . Auth::user()->name . " (管理员)。";
                }
                $workreply->enclosure_id = 0;
                $workreply->system = 1;
                
                if ($workreply->save()) {
                    // 工单通知信息处理 － no
                    $message_id = DB::table($com->id . '_work_message')->where('work_order_id', $workID)->pluck('id');
                    if ($message_id) {
                        $work_message = WorkMessage::find($message_id);
                        $work_message->work_status = $work_order->work_status;
                        $work_message->type = 'reply';
                        $work_message->status = 'false';
                        $work_message->reply_id = $workreply->id;
                        $work_message->reply_admin_id = Auth::user()->id;

                        $work_order = WorkOrder::find($workID);
                        $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                        $user_message = DB::table('site_user')->where('user_id', $user_id)->pluck('message');
                        if ($user_message == '0') {
                            $work_message->save();
                        }
                    }

                    // 用户发起内容接收邮件队列
                    // 获得组别
                    $group_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('group_id');
                    // 获得客户
                    $user_id = DB::table($com->id . '_work_order')->where('id', $workID)->pluck('user_id');
                    // 获得公司路由
                    $path = Config::get('app.com_path');
                    $this->quereAdmin($com, $workreply->id, $user_id, $group_id, $workreply->reply, $path, $workID);

                    return Redirect::to($com->path . '/admin/work')->with('success', '工单#' . $workID . '关闭成功！');
                } else {
                    return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '关闭自动回复失败！');
                }
            } else {
                return Redirect::to($com->path . '/admin/work')->with('error', '工单#' . $workID . '关闭失败！');
            }
        // }
    }

    // 工单标题搜索
    public function searchWork($com, $status = NULL, $keyword = NULL)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');
        if ($level == 4) {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');

            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            if ($status == '1') {
                // 获取所属组工单池列表
                if ($workCount['1'] > 0) {
                    $workerOrder = $this->searchWorkStatusIndex($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '1')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->whereIn($com->id . '_work_order.group_id', $group_id)->count();
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

                return View::make('com/admin/work/worksearch')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            } else if ($status == '2') {
                // 获取所属组处理中工单列表
                if ($workCount['2'] > 0) {
                    $workerOrder = $this->searchWorkStatusUntreatedAll($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '2')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '=', Auth::user()->id)->count();
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

                return View::make('com/admin/work/worksearchuntreated')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            } else if ($status == '3') {
                // 获取所属组已处理工单列表
                if ($workCount['3'] > 0) {
                    $workerOrder = $this->searchWorkStatusProcessedAll($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '3')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '=', Auth::user()->id)->count();
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

                return View::make('com/admin/work/worksearchprocessed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            } else if ($status == '4') {
                // 获取所属组已关闭工单列表
                if ($workCount['4'] > 0) {
                    $workerOrder = $this->searchWorkStatusClosedAll($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '4')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '=', Auth::user()->id)->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }

                // 关闭工单对象转为数组 - no
                $workList = $workerOrder->toArray();
                $workerOrders = $workList['data'];

                return View::make('com/admin/work/worksearchclosed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            }
        }  else if ($level == 3) {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');

            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            if ($status == '1') {
                // 获取所属组工单池列表
                if ($workCount['1'] > 0) {
                    $workerOrder = $this->searchWorkStatusIndex($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '1')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->whereIn($com->id . '_work_order.group_id', $group_id)->count();
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

                return View::make('com/admin/work/worksearch')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            } else if ($status == '2') {
                // 获取所属组处理中工单列表
                if ($workCount['2'] > 0) {
                    $workerOrder = $this->searchWorkStatusUntreatedAll($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '2')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '=', Auth::user()->id)->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }
                $searchgrade = 'sm';

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

                return View::make('com/admin/work/worksearchuntreated')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('workerOrders'))->with(compact('searchCount'))->with(compact('searchgrade'));
            } else if ($status == '3') {
                // 获取所属组已处理工单列表
                if ($workCount['3'] > 0) {
                    $workerOrder = $this->searchWorkStatusProcessedAll($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '3')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '=', Auth::user()->id)->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }
                $searchgrade = 'sm';

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

                return View::make('com/admin/work/worksearchprocessed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('workerOrders'))->with(compact('searchCount'))->with(compact('searchgrade'));
            } else if ($status == '4') {
                // 获取所属组已关闭工单列表
                if ($workCount['4'] > 0) {
                    $workerOrder = $this->searchWorkStatusClosedAll($com, $group_id, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '4')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '=', Auth::user()->id)->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }
                $searchgrade = 'sm';

                // 关闭工单对象转为数组 - no
                $workList = $workerOrder->toArray();
                $workerOrders = $workList['data'];

                return View::make('com/admin/work/worksearchclosed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('workerOrders'))->with(compact('searchCount'))->with(compact('searchgrade'));
            }
        } else {
            $workCount = $this->workCounts($com, $level, $group_id = NULL); // 获取公司工单总数

            if ($status == '1') {
                // 获取公司工单池列表
                if ($workCount['1'] > 0) {
                    $workerOrder = $this->searchWorkStatusIndex($com, $group_id = NULL, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '1')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
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

                return View::make('com/admin/work/worksearch')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            } else if ($status == '2') {
                // 获取公司处理中工单列表
                if ($workCount['2'] > 0) {
                    $workerOrder = $this->searchWorkStatusUntreatedAll($com, $group_id = NULL, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '2')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
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

                return View::make('com/admin/work/worksearchuntreated')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            } else if ($status == '3') {
                // 获取公司已处理工单列表
                if ($workCount['3'] > 0) {
                    $workerOrder = $this->searchWorkStatusProcessedAll($com, $group_id = NULL, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '3')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
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

                return View::make('com/admin/work/worksearchprocessed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            } else if ($status == '4') {
                // 获取公司已关闭工单列表
                if ($workCount['4'] > 0) {
                    $workerOrder = $this->searchWorkStatusClosedAll($com, $group_id = NULL, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        $searchCount = WorkOrder::where('work_status', '4')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }

                // 关闭工单对象转为数组 - no
                $workList = $workerOrder->toArray();
                $workerOrders = $workList['data'];

                return View::make('com/admin/work/worksearchclosed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
            }
        }
    }

    // 工单邀请标题搜索
    public function searchInviteWork($com, $status = NULL, $keyword = NULL)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');
        if ($level == '1' || $level == '2') {
            // 获取公司工单总数
            $workCount = $this->workCounts($com, $level, $group_id = NULL);
        } else if ($level == '4') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);
        } else if ($level == '3') {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);
        }

        if ($status == '5') {
            // 获取公司工单池列表
            if ($workCount['5'] > 0) {
                $workerOrder = $this->searchWorkStatusInvite($com, $keyword, $this->page);

                // 获取搜索工单总数
                if (count($workerOrder) == 0) {
                    $workerOrder = '';
                    $searchCount = 0;
                } else {
                    $searchCount = WorkOrder::where('work_status', '2')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->count();
                }
            } else {
                $workerOrder = '';
                $searchCount = 0;
            }
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

        return View::make('com/admin/work/worksearchinvite')->with('com', $com)->with(compact('workCount'))
        ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('workerOrders'));
    }

    // 工单邀请标题搜索处理
    public function searchWorkStatusInvite($com, $keyword = NULL, $page)
    {
        // 获取邀请工单
        $cilmWorkOrder = WorkClaim::where('admin_id', Auth::user()->id)->get()->lists('work_order_id');
        $workerOrder = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
        ->join('site_user', 'site_user.user_id', '=', 'users.id')
        ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
        ->where($com->id . '_work_order.work_status', '=', '2')->whereIn($com->id . '_work_order.id', $cilmWorkOrder)
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
        $admins = DB::table($com->id . '_work_order')
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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

    // 工单标题工单池搜索处理
    public function searchWorkStatusIndex($com, $group_id = NULL, $keyword = NULL, $page)
    {
        if ($group_id == NULL) {
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->where($com->id . '_work_order.work_status', '=', '1')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name')->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
            $wId = WorkOrder::where('work_status', '1')->get()->lists('id');
            if(is_array($wId)) {
                $wId = implode(',', $wId);
            }
        } else {
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where($com->id . '_work_order.work_status', '=', '1')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
            ->whereIn($com->id . '_work_order.group_id', $group_id)
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
            $wId = WorkOrder::where('work_status', '1')->whereIn('group_id', $group_id)->get()->lists('id');
            if(is_array($wId)) {
                $wId = implode(',', $wId);
            }
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

    // 待处理工单列表搜索处理
    public function searchWorkStatusUntreatedAll($com, $group_id = NULL, $keyword = NULL, $page)
    {
        if ($group_id == NULL) {
            // 待处理工单列表获取处理
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where($com->id . '_work_order.work_status', '=', '2')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        } else {
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '=', Auth::user()->id)->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
            ->where($com->id . '_work_order.work_status', '=', '2')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }
        
        // 获取头像
        foreach ($workerOrder as $tx=>$tv) {
            if ($tv->head > 0) {
                $workerOrder[$tx]->head = DB::table('enclosures')->where('id', $tv->head)->pluck('path');
            }
        }

        // 获取当前工单处理人员
        $admins = DB::table($com->id . '_work_order')
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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
    public function searchWorkStatusProcessedAll($com, $group_id = NULL, $keyword = NULL, $page)
    {
        if ($group_id == NULL) {
            // 已处理工单列表获取处理
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where($com->id . '_work_order.work_status', '=', '3')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        } else {
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '=', Auth::user()->id)->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
            ->where($com->id . '_work_order.work_status', '=', '3')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }

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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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
    public function searchWorkStatusClosedAll($com, $group_id = NULL, $keyword = NULL, $page)
    {
        if ($group_id == NULL) {
            // 已关闭工单列表获取处理
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where($com->id . '_work_order.work_status', '=', '4')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        } else {
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '=', Auth::user()->id)->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
            ->where($com->id . '_work_order.work_status', '=', '4')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }
        
        // 获取当前工单处理人员
        $admins = DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '4')
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

        return $workerOrder;
    }

    // 工单标题搜索(查询组员工单)
    public function searchGroupWork($com, $status = NULL, $keyword = NULL)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');
        
        if ($level == 3) {
            // 获取所属组别
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            // 获取所属组工单总数
            $workCount = $this->workCounts($com, $level, $group_id);

            if ($status == '2') {
                // 获取所属组处理中工单列表
                if ($workCount['2'] > 0) {
                    $workerOrder = $this->searchWorkStatusUntreatedGroup($com, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        // 获取作为组长的组别
                        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
                        $searchCount = WorkOrder::where('work_status', '2')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }
                $searchgrade = 'sg';

                return View::make('com/admin/work/worksearchuntreated')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('searchgrade'));
            } else if ($status == '3') {
                // 获取所属组已处理工单列表
                if ($workCount['3'] > 0) {
                    $workerOrder = $this->searchWorkStatusProcessedGroup($com, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        // 获取作为组长的组别
                        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
                        $searchCount = WorkOrder::where('work_status', '3')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }
                $searchgrade = 'sg';

                return View::make('com/admin/work/worksearchprocessed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('searchgrade'));
            } else if ($status == '4') {
                // 获取所属组已关闭工单列表
                if ($workCount['4'] > 0) {
                    $workerOrder = $this->searchWorkStatusClosedGroup($com, $keyword, $this->page);
                    // 获取搜索工单总数
                    if (count($workerOrder) == 0) {
                        $workerOrder = '';
                        $searchCount = 0;
                    } else {
                        // 获取作为组长的组别
                        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
                        $searchCount = WorkOrder::where('work_status', '4')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
                                        ->where($com->id . '_work_order.admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)->count();
                    }
                } else {
                    $workerOrder = '';
                    $searchCount = 0;
                }
                $searchgrade = 'sg';

                return View::make('com/admin/work/worksearchclosed')->with('com', $com)->with(compact('workCount'))
                ->with(compact('workerOrder'))->with(compact('searchCount'))->with(compact('searchgrade'));
            }
        }
    }

    // 处理中组员工单搜索
    public function searchWorkStatusUntreatedGroup($com, $keyword = NULL, $page)
    {
        // 获取作为组长的组别
        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
        if (count($tGrade) > 0) {
            // 所属组组员待处理工单
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '!=', Auth::user()->id)->where($com->id . '_work_order.work_status', '=', '2')
            ->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')->whereIn('group_id', $tGrade)
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'users.head', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }

        // 获取客户头像
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
        $wId = WorkOrder::where('work_status', '2')->where('admin_id', '!=', Auth::user()->id)
        ->whereIn('group_id', $tGrade)->get()->lists('id');
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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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

    // 已处理组员工单搜索
    public function searchWorkStatusProcessedGroup($com, $keyword = NULL, $page)
    {
        // 获取作为组长的组别
        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
        if (count($tGrade) > 0) {
            // 所属组组员待处理工单
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)
            ->where($com->id . '_work_order.work_status', '=', '3')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }

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
                where work_order_id in (" . $wId .") ORDER BY created_at DESC) " . $TablePrefix . $com->id . "_work_reply 
                where work_order_id in (" . $wId .") GROUP BY work_order_id";
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

    // 已关闭组员工单搜索
    public function searchWorkStatusClosedGroup($com, $keyword = NULL, $page)
    {
        // 获取作为组长的组别
        $tGrade = GroupAdminUser::where('user_id', Auth::user()->id)->where('grade', '1')->get()->lists('group_id');
        if (count($tGrade) > 0) {
            // 所属组组员已关闭工单
            $workerOrder = DB::table($com->id . '_work_order')
            ->join('users', 'users.id', '=', $com->id . '_work_order.user_id')
            ->join('site_user', 'site_user.user_id', '=', 'users.id')
            ->join($com->id . '_groups', $com->id . '_groups.id', '=', $com->id . '_work_order.group_id')
            ->where('admin_id', '!=', Auth::user()->id)->whereIn('group_id', $tGrade)
            ->where($com->id . '_work_order.work_status', '=', '4')->where($com->id . '_work_order.title', 'like', '%' . $keyword . '%')
            ->select($com->id . '_work_order.*', 'users.user_name', 'users.name', 'site_user.firm', 
            $com->id . '_groups.group_name') 
            ->orderBy($com->id . '_work_order.id', 'desc')->paginate($page);
        }

        // 获取当前工单处理人员
        $admins =  DB::table($com->id . '_work_order')
        ->join('users', 'users.id', '=', $com->id . '_work_order.admin_id')
        ->join('admin_user', 'admin_user.user_id', '=', $com->id . '_work_order.admin_id')
        ->where($com->id . '_work_order.work_status', '=', '4')
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

        return View::make('com/admin/work/getenclosurework')->with('com', $com)->with(compact('enclosureAll'));
    }

    // 用户发起内容接收邮件队列
    public function quereAdmin($com, $reply_id = NULL, $user_id = NULL, $group_id = NULL, $content = NULL, $path = NULL, $work_id = NULL)
    {
        // binway需求：系统提醒消息邮件发送取消
        $system = DB::table('work_reply')->where('id', $reply_id)->pluck('system');
        if ($system == '0') {
            
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
                // $from_email = $email_id;
                $status = 'false';
            }

            // 查询用户回复列表是否需要验证并通过
            $check = DB::table($com->id . '_work_reply')->where('id', $reply_id)->pluck('check');
            if ($check == '1') {
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

    // 工单消息通知 - no
    public function messageWork($com)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取工单消息通知总数
        // if ($level > 2) {
        if ($level == 3) { // binway需求：工单池客服不许看见，主要应对他们的业务，每个工单都必须要主管手动分配
            // 工单池消息获取
            $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
            $group_work_order_id = WorkOrder::whereIn('group_id', $group_id)->get()->lists('id');
            $workCount = DB::table($com->id . '_work_message')->where('admin_id', '=', 0)->where('type', '=', 'ask')
            ->where('status', '=', 'false')->whereIn('work_order_id', $group_work_order_id)->count();

            // 客户回复工单消息获取
            $workCountOne = DB::table($com->id . '_work_message')->where('admin_id', '=', Auth::user()->id)
            ->where('type', '=', 'ask')->where('status', '=', 'false')->count();

            // 用户回复工单消息获取
            // coding...

            echo json_encode(array('workCount'=>$workCount, 'workCountOne'=>$workCountOne)); 
        }
    }

    // 工单消息通知详情 - no
    public function getMessage($com)
    {
        // 判断管理员等级
        $level = Config::get('auth.com_admin_level_id');

        // 获取工单消息通知
        if ($level > 2) {
            if ($_GET['type'] == 'group') {
                $group_id = GroupAdminUser::where('user_id', Auth::user()->id)->get()->lists('group_id');
                $group_work_order_id = WorkOrder::whereIn('group_id', $group_id)->get()->lists('id');

                $messageData = DB::table($com->id . '_work_message')->where('admin_id', '=', 0)->where('type', '=', 'ask')
                ->where('status', '=', 'false')->whereIn('work_order_id', $group_work_order_id)->get();
            } elseif ($_GET['type'] == 'me') {
                $messageData = DB::table($com->id . '_work_message')->where('admin_id', '=', Auth::user()->id)
                ->where('type', '=', 'ask')->where('status', '=', 'false')->get();
            }
            
            // 最新时间倒叙
            $message = $this->array_sort_work($messageData, 'updated_at', 'desc');

            // 获取客户和受理人以及处理人姓名
            foreach ($message as $k => $v) {
                $message[$k]->user_name = DB::table('users')->where('id', $v->user_id)->pluck('name');
                $message[$k]->admin_name = DB::table('users')->where('id', $v->admin_id)->pluck('name');
                $message[$k]->reply_admin_name = DB::table('users')->where('id', $v->reply_admin_id)->pluck('name');
            }
            
            return View::make('com/admin/work/message')->with('com', $com)->with(compact('message'));
        }
    }
}
