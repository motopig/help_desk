<?php

/**
 * Class SiteHomeController
 *
 * 平台首页控制器 by cooper
 */
class SiteHomeController extends BaseController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        //parent::__construct();
    }

    /**
     * 首页
     */
    public function index(){
        // 暂时转跳到 no
        //
        // return Redirect::to('no');

        return View::make('site/index');
    }

}
