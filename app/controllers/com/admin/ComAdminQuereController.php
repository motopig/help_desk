<?php

/**
 * Class ComAdminQuereController
 *
 * 公司邮件队列控制器 by no
 */
class ComAdminQuereController extends ComAdminController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();
    }

    // 设置队列分页数
    private $page = 10;

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
        return View::make('com/admin/quere/index')->with('com', $com);
    }

    /**
     * 用户邮件队列
     *
     * @param $com
     *
     * @return mixed
     */
    public function reply($com)
    {
        // 查询用户接收邮件队列信息
        $quere = DB::table($com->id . '_staff_quere')->paginate($this->page);

        // 加载模板
        return View::make('com/admin/quere/reply')->with('com', $com)->with(compact('quere'));
    }

    /**
     * 客户邮件队列
     *
     * @param $com
     *
     * @return mixed
     */
    public function ask($com)
    {
        // 查询用户接收邮件队列信息
        $quere = DB::table($com->id . '_user_quere')->paginate($this->page);

        // 加载模板
        return View::make('com/admin/quere/ask')->with('com', $com)->with(compact('quere'));
    }

    /**
     * 邮件统计队列
     *
     * @param $com
     *
     * @return mixed
     */
    public function emailCount($com)
    {
        // 待开发
        // 加载模板
        // return View::make('com/admin/quere/count')->with('com', $com)->with(compact('quere'));
         return View::make('com/admin/quere/count')->with('com', $com);
    }

    // binway邮件队列
    public function getOrderQuere($com)
    {
        // 查询邮件队列信息
        $quere = DB::table($com->id . '_order_quere')->orderBy('updated_at', 'desc')->paginate($this->page);
        foreach ($quere as $k => $v) {
            $quere[$k]->order_disabled = DB::table($com->id . '_order')->where('id', $v->order)->pluck('disabled');
        }

        // 加载模板
        return View::make('com/admin/quere/order')->with('com', $com)->with(compact('quere'));
    }
}
