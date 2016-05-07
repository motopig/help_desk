<?php

/**
 * Class RootReportController
 *
 * 超级管理员报表统计控制器 by cooper
 */
class RootReportController extends RootController {

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
        // 加载模板
        //
        return View::make('root/report/index');
    }

}
