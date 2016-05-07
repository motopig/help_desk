<?php

/**
 * Class ComAdminReportController
 *
 * 公司后台报表统计控制器 by no
 */
class ComAdminReportController extends ComAdminController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 首页(工单报表)
     *
     * @param $com
     *
     * @return mixed
     */
    // public function index($com)
    // {
    //     $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
    //     $COM_TablePrefix = $TablePrefix . $com->id . '_'; // 公司表前缀
        
    //     /* 工单总数统计 */
    //     // 今日工单总数
    //     $today_time = strtotime(date('Y-m-d')); // 获取当天日期
    //     $today_sql = "select count(id) as count from " . $COM_TablePrefix . "work_order where created_at > '" . date('Y-m-d H:i:s', ($today_time - 1)) . 
    //     "' and created_at < '" . date('Y-m-d H:i:s', ($today_time + 86400)) . "'";
    //     $today_work = DB::select($today_sql);
    //     $toDayWork = $today_work[0]->count;
        
    //     // 昨日工单总数
    //     $yesterday_time = strtotime(date('Y-m-d', strtotime('-1 day'))); // 获取昨天日期
    //     $yesterday_sql = "select count(id) as count from " . $COM_TablePrefix . "work_order where created_at > '" . date('Y-m-d H:i:s', ($yesterday_time - 1)) . 
    //     "' and created_at < '" . date('Y-m-d H:i:s', ($yesterday_time + 86400)) . "'";
    //     $yesterday_work = DB::select($yesterday_sql);
    //     $yesterdayWork = $yesterday_work[0]->count;
        
    //     // 上周工单总数
    //     $this_monday = $this->this_monday(time(), true); // 获取本周周一的日期
    //     $last_monday = $this->last_monday(time(), true); // 获取上周周一的日期
    //     $week_sql = "select count(id) as count from " . $COM_TablePrefix . "work_order where created_at > '" . date('Y-m-d H:i:s', ($last_monday - 1)) . 
    //     "' and created_at < '" . date('Y-m-d H:i:s', ($this_monday - 1)) . "'";
    //     $week_work = DB::select($week_sql);
    //     $weekWork = $week_work[0]->count;
        
    //     // 本月工单总数
    //     $this_month = strtotime(date("Y") . "-" . date("m") . "-1"); // 获取本月第一天
    //     $last_month = $this->month_lastday(time(), true); // 获取本月最后一天
    //     $month_sql = "select count(id) as count from " . $COM_TablePrefix . "work_order where created_at > '" . date('Y-m-d H:i:s', ($this_month - 1)) . 
    //     "' and created_at < '" . date('Y-m-d H:i:s', ($last_month + 86400 - 1)) . "'";
    //     $month_work = DB::select($month_sql);
    //     $monthWork = $month_work[0]->count;

    //     // 当前工单总数
    //     $workCount = DB::table($com->id . '_work_order')->count();

    //     /* 频率变化统计 */
    //     // 本日新增工单
    //     $newToDayWork = $toDayWork;

    //     // 本周新增工单
    //     $this_sunday = $this->this_sunday(time(), $is_return_timestamp = true); // 获取本周星期天
    //     $new_week_sql = "select count(id) as count from " . $COM_TablePrefix . "work_order where created_at > '" . date('Y-m-d H:i:s', ($this_monday - 1)) . 
    //     "' and created_at < '" . date('Y-m-d H:i:s', ($this_sunday + 86400 - 1)) . "'";
    //     $new_week = DB::select($new_week_sql);
    //     $newWeek = $new_week[0]->count;
        
    //     // 本月新增工单
    //     $newMonthWork = $monthWork;

    //     /* 工单质量统计 */
    //     // 未受理工单总数
    //     $statusOne = DB::table($com->id . '_work_order')->where('work_status', '=', '1')->count();
    //     // 处理中工单总数
    //     $statusTwo = DB::table($com->id . '_work_order')->where('work_status', '=', '2')->count();
    //     // 已处理工单总数
    //     $statusThree = DB::table($com->id . '_work_order')->where('work_status', '=', '3')->count();
    //     // 已关闭工单总数
    //     $statusFour = DB::table($com->id . '_work_order')->where('work_status', '=', '4')->count();

    //     // 加载模板
    //     return View::make('com/admin/report/index')->with('com', $com)->with(compact('toDayWork'))->with(compact('yesterdayWork'))->with(compact('workCount'))
    //     ->with(compact('weekWork'))->with(compact('monthWork'))->with(compact('newToDayWork'))->with(compact('newWeek'))->with(compact('newMonthWork'))
    //     ->with(compact('statusOne'))->with(compact('statusTwo'))->with(compact('statusThree'))->with(compact('statusFour'));
    // }

    public function index($com)
    {
        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $COM_TablePrefix = $TablePrefix . $com->id . '_'; // 公司表前缀
        
        /* 工单总数统计 */
        // 今日工单总数
        $today_time = strtotime(date('Y-m-d')); // 获取当天日期
        $today_sql = "select count(id) as count from " . $COM_TablePrefix . "order where created_at > '" . date('Y-m-d H:i:s', ($today_time - 1)) . 
        "' and created_at < '" . date('Y-m-d H:i:s', ($today_time + 86400)) . "'";
        $today_work = DB::select($today_sql);
        $toDayWork = $today_work[0]->count;
        
        // 昨日工单总数
        $yesterday_time = strtotime(date('Y-m-d', strtotime('-1 day'))); // 获取昨天日期
        $yesterday_sql = "select count(id) as count from " . $COM_TablePrefix . "order where created_at > '" . date('Y-m-d H:i:s', ($yesterday_time - 1)) . 
        "' and created_at < '" . date('Y-m-d H:i:s', ($yesterday_time + 86400)) . "'";
        $yesterday_work = DB::select($yesterday_sql);
        $yesterdayWork = $yesterday_work[0]->count;
        
        // 上周工单总数
        $this_monday = $this->this_monday(time(), true); // 获取本周周一的日期
        $last_monday = $this->last_monday(time(), true); // 获取上周周一的日期
        $week_sql = "select count(id) as count from " . $COM_TablePrefix . "order where created_at > '" . date('Y-m-d H:i:s', ($last_monday - 1)) . 
        "' and created_at < '" . date('Y-m-d H:i:s', ($this_monday - 1)) . "'";
        $week_work = DB::select($week_sql);
        $weekWork = $week_work[0]->count;
        
        // 本月工单总数
        $this_month = strtotime(date("Y") . "-" . date("m") . "-1"); // 获取本月第一天
        $last_month = $this->month_lastday(time(), true); // 获取本月最后一天
        $month_sql = "select count(id) as count from " . $COM_TablePrefix . "order where created_at > '" . date('Y-m-d H:i:s', ($this_month - 1)) . 
        "' and created_at < '" . date('Y-m-d H:i:s', ($last_month + 86400 - 1)) . "'";
        $month_work = DB::select($month_sql);
        $monthWork = $month_work[0]->count;

        // 当前工单总数
        $workCount = DB::table($com->id . '_work_order')->count();

        /* 频率变化统计 */
        // 本日新增工单
        $newToDayWork = $toDayWork;

        // 本周新增工单
        $this_sunday = $this->this_sunday(time(), $is_return_timestamp = true); // 获取本周星期天
        $new_week_sql = "select count(id) as count from " . $COM_TablePrefix . "order where created_at > '" . date('Y-m-d H:i:s', ($this_monday - 1)) . 
        "' and created_at < '" . date('Y-m-d H:i:s', ($this_sunday + 86400 - 1)) . "'";
        $new_week = DB::select($new_week_sql);
        $newWeek = $new_week[0]->count;
        
        // 本月新增工单
        $newMonthWork = $monthWork;

        /* 工单质量统计 */
        // 未受理工单总数
        $statusOne = DB::table($com->id . '_order')->where('status', '=', '0')->count();
        // 处理中工单总数
        $statusTwo = DB::table($com->id . '_order')->where('status', '=', '1')->count();
        // 已处理工单总数
        $statusThree = DB::table($com->id . '_order')->where('status', '=', '2')->count();
        // 已关闭工单总数
        $statusFour = DB::table($com->id . '_order')->where('disabled', '=', 'true')->count();
        
        // 加载模板
        return View::make('com/admin/report/index')->with('com', $com)->with(compact('toDayWork'))->with(compact('yesterdayWork'))->with(compact('workCount'))
        ->with(compact('weekWork'))->with(compact('monthWork'))->with(compact('newToDayWork'))->with(compact('newWeek'))->with(compact('newMonthWork'))
        ->with(compact('statusOne'))->with(compact('statusTwo'))->with(compact('statusThree'))->with(compact('statusFour'));
    }

    // 某周周一的日期
    private function this_monday($timestamp = 0, $is_return_timestamp = true)
    {
        static $cache;
        $id = $timestamp . $is_return_timestamp;
        if (!isset($cache[$id])) {
            if (!$timestamp) $timestamp = time();
            $monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));
            if($is_return_timestamp) {
                $cache[$id] = strtotime($monday_date);
            } else {
                $cache[$id] = $monday_date;
            }
        }

        return $cache[$id];
    }

    // 上周周一的日期
    private function last_monday($timestamp = 0, $is_return_timestamp = true)
    {
        static $cache;
        $id = $timestamp . $is_return_timestamp;
        if (!isset($cache[$id])) {
            if (!$timestamp) $timestamp = time();
            $thismonday = $this->this_monday($timestamp) - /*7*86400*/604800;
            if($is_return_timestamp) {
                $cache[$id] = $thismonday;
            } else {
                $cache[$id] = date('Y-m-d', $thismonday);
            }
        }

        return $cache[$id];
    }

    // 这个星期的星期天
    private function this_sunday($timestamp = 0, $is_return_timestamp = true)
    {  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if (!isset($cache[$id])) {  
        if (!$timestamp) $timestamp = time();  
            $sunday = $this->this_monday($timestamp) + /*6*86400*/518400;  
            if ($is_return_timestamp){  
                $cache[$id] = $sunday;  
            } else {  
                $cache[$id] = date('Y-m-d',$sunday);  
            }  
        }  

        return $cache[$id];  
    }  

    // 某月最后一天  
    private function month_lastday($timestamp = 0, $is_return_timestamp = true)
    {  
        static $cache ;  
        $id = $timestamp . $is_return_timestamp;  
        if (!isset($cache[$id])) {  
            if(!$timestamp) $timestamp = time();  
            $lastday = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),date('t',$timestamp),date('Y',$timestamp)));  
            if ($is_return_timestamp) {  
                $cache[$id] = strtotime($lastday);  
            } else {  
                $cache[$id] = $lastday;  
            }  
        } 

        return $cache[$id];  
    }  

     /**
     * 客户报表
     *
     * @param $com
     *
     * @return mixed
     */
    public function user($com)
    {
        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $COM_TablePrefix = $TablePrefix . $com->id . '_'; // 公司表前缀
        $workCount = DB::table($com->id . '_work_order')->count(); // 获取工单总数
        
        /* 客户统计 */
        // 当前客户总数
        $userCount = DB::table('site_user')->where('company_id', '=', $com->id)->count();

        if ($workCount > 0) {
            // 提交工单最多的人
            $many_wrok_sql = "SELECT user_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_order GROUP BY user_id ORDER BY count DESC";
            $many_work = DB::select($many_wrok_sql);
            // 提交工单最多数量
            $manyWorkCount = $many_work[0]->count;
            // 提交工单最多客户
            $manyWorkUser = DB::table('users')->where('id', $many_work[0]->user_id)->pluck('name');
            
            // 提交工单最少的人
            $less_work_sql = "SELECT user_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_order GROUP BY user_id ORDER BY count ASC";
            $less_work = DB::select($less_work_sql);
            // 提交工单最少数量
            $lessWorkCount = $less_work[0]->count;
            // 提交工单最少客户
            $lessWorkUser = DB::table('users')->where('id', $less_work[0]->user_id)->pluck('name');

            // 工单提问最多的人
            $many_ask_sql = "SELECT user_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_ask GROUP BY user_id ORDER BY count DESC";
            $many_ask = DB::select($many_ask_sql);
            // 工单提问最多数量
            $manyAskCount = $many_ask[0]->count;
            // 工单提问最多客户
            $manyAskUser = DB::table('users')->where('id', $many_ask[0]->user_id)->pluck('name');

            // 工单提问最少的人
            $less_ask_sql = "SELECT user_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_ask GROUP BY user_id ORDER BY count ASC";
            $less_ask = DB::select($less_ask_sql);
            // 工单提问最少数量
            $lessAskCount = $less_ask[0]->count;
            // 工单提问最少客户
            $lessAskUser = DB::table('users')->where('id', $less_ask[0]->user_id)->pluck('name');

            /* 排行统计 */
            // 客户问题排行(提问从多到少的客户排名)
            $rank_user = $many_ask;
            foreach ($rank_user as $k => $v) {
                $rank_user[$k]->user_name = DB::table('users')->where('id', $v->user_id)->pluck('name');
                $rank_user[$k]->rank_id = $k + 1;
            }

            $rankUser = $rank_user; // 客户提问排名
        } else {
            // 提交工单最多的人
            // 提交工单最多数量
            $manyWorkCount = 0;
            // 提交工单最多客户
            $manyWorkUser = '';
            
            // 提交工单最少的人
            // 提交工单最多数量
            $lessWorkCount = 0;
            // 提交工单最多客户
            $lessWorkUser = '';
            
            // 工单提问最多的人
            // 工单提问最多数量
            $manyAskCount = 0;
            // 工单提问最多客户
            $manyAskUser = '';

            // 工单提问最少的人
            // 工单提问最少数量
            $lessAskCount = 0;
            // 工单提问最少客户
            $lessAskUser = '';

            /* 排行统计 */
            // 客户问题排行(提问从多到少的客户排名)
            $rankUser = 0;
        }

        // 加载模板
        return View::make('com/admin/report/user')->with('com', $com)
        ->with(compact('userCount'))->with(compact('rankUser'))
        ->with(compact('manyWorkCount'))->with(compact('manyWorkUser'))
        ->with(compact('lessWorkCount'))->with(compact('lessWorkUser'))
        ->with(compact('manyAskCount'))->with(compact('manyAskUser'))
        ->with(compact('lessAskCount'))->with(compact('lessAskUser'));
    }

     /**
     * 用户报表
     *
     * @param $com
     *
     * @return mixed
     */
    public function admin($com)
    {
        $TablePrefix = Schema::getConnection()->getTablePrefix(); // 获取表前缀
        $COM_TablePrefix = $TablePrefix . $com->id . '_'; // 公司表前缀
        $workCount = DB::table($com->id . '_work_order')->where('admin_id', '!=', 0)->count(); // 获取工单总数

        /* 用户统计 */
        // 当前用户总数
        $adminCount = DB::table('admin_user')->where('company_id', '=', $com->id)->count();
        // 等级2总数
        $adminTwo = DB::table('admin_user')->where('company_id', '=', $com->id)->where('level', '2')->count();
        // 等级3总数
        $adminThree = DB::table('admin_user')->where('company_id', '=', $com->id)->where('level', '3')->count();
        // 等级4总数
        $adminFour = DB::table('admin_user')->where('company_id', '=', $com->id)->where('level', '4')->count();

        if ($workCount > 0) {
            // 处理工单最多的人
            $many_wrok_sql = "SELECT admin_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_order where admin_id != 0 GROUP BY admin_id ORDER BY count DESC";
            $many_work = DB::select($many_wrok_sql);
            // 处理工单最多数量
            $manyWorkCount = $many_work[0]->count;
            // 处理工单最多用户
            $manyWorkUser = DB::table('users')->where('id', $many_work[0]->admin_id)->pluck('name');

            // 处理工单最少的人
            $less_work_sql = "SELECT admin_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_order where admin_id != 0 GROUP BY admin_id ORDER BY count ASC";
            $less_work = DB::select($less_work_sql);
            // 处理工单最少数量
            $lessWorkCount = $less_work[0]->count;
            // 处理工单最少用户
            $lessWorkUser = DB::table('users')->where('id', $less_work[0]->admin_id)->pluck('name');

            // 工单回复最多的人
            $many_reply_sql = "SELECT admin_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_reply GROUP BY admin_id ORDER BY count DESC";
            $many_reply = DB::select($many_reply_sql);
            // 工单回复最多数量
            $manyAskCount = $many_reply[0]->count;
            // 工单回复最多用户
            $manyAskUser = DB::table('users')->where('id', $many_reply[0]->admin_id)->pluck('name');

            // 工单回复最少的人
            $less_reply_sql = "SELECT admin_id, COUNT(id) AS count FROM " . $COM_TablePrefix . "work_reply GROUP BY admin_id ORDER BY count ASC";
            $less_reply = DB::select($less_reply_sql);
            // 工单回复最少数量
            $lessAskCount = $less_reply[0]->count;
            // 工单回复最少用户
            $lessAskUser = DB::table('users')->where('id', $less_reply[0]->admin_id)->pluck('name');

            /* 排行统计 */
            // 用户处理排行(回答从多到少的用户排名)
            $rank_user = $many_reply;
            foreach ($rank_user as $k => $v) {
                $rank_user[$k]->user_name = DB::table('users')->where('id', $v->admin_id)->pluck('name');
                $rank_user[$k]->rank_id = $k + 1;
            }

            $rankUser = $rank_user; // 用户回复排名
        } else {
            // 处理工单最多的人
            // 处理工单最多数量
            $manyWorkCount = 0;
            // 处理工单最多客户
            $manyWorkUser = '';

            // 处理工单最少的人
            // 处理工单最多数量
            $lessWorkCount = 0;
            // 处理工单最多客户
            $lessWorkUser = '';

            // 工单回复最多的人
            // 工单回复最多数量
            $manyAskCount = 0;
            // 工单回复最多客户
            $manyAskUser = '';

            // 工单回复最少的人
            // 工单回复最少数量
            $lessAskCount = 0;
            // 工单回复最少客户
            $lessAskUser = '';

            /* 排行统计 */
            // 用户处理排行(回答从多到少的用户排名)
            $rankUser = 0;
        }

        // 加载模板
        return View::make('com/admin/report/admin')->with('com', $com)->with(compact('adminCount'))
        ->with(compact('adminTwo'))->with(compact('adminThree'))->with(compact('adminFour'))->with(compact('rankUser'))
        ->with(compact('manyWorkCount'))->with(compact('manyWorkUser'))
        ->with(compact('lessWorkCount'))->with(compact('lessWorkUser'))
        ->with(compact('manyAskCount'))->with(compact('manyAskUser'))
        ->with(compact('lessAskCount'))->with(compact('lessAskUser'));
    }
}
