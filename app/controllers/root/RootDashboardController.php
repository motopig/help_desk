<?php

/**
 * Class RootDashboardController
 *
 * 超级管理员后台首页控制器 by cooper
 */
class RootDashboardController extends RootController {

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
		return View::make('root/dashboard/index');
	}

}
