<?php

/**
 * Class ComAdminDashboardController
 *
 * 公司后台首页控制器 by cooper
 */
class ComAdminDashboardController extends ComAdminController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
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
        return View::make('com/admin/dashboard/index')->with('com', $com);
    }

}