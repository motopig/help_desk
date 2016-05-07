<?php

/**
 * Class BaseController
 *
 * 系统控制器基础类 by cooper
 */
class BaseController extends Controller {

    // 不检查权限控制器方法
    protected $whitelist = array();

    /**
     * __construct 构造方法
     *
     */
    public function __construct(){
        //parent::__construct();

        // csrf 处理
        $this->beforeFilter('csrf', array('on' => 'post'));
    }


    /**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}