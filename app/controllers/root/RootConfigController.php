<?php

/**
 * Class RootConfigController
 *
 * 超级管理员系统设置控制器 by cooper
 */
class RootConfigController extends RootController {

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
        return View::make('root/config/index');
    }

}
