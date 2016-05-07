<?php

/**
 * Class RootController
 *
 * 超级管理员主控制器 by cooper
 */
class RootController extends BaseController {

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        parent::__construct();

        $this->beforeFilter('root.auth', array('except' => $this->whitelist));
    }

}
