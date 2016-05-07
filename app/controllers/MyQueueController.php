<?php

// 邮件队列
class MyQueueController extends BaseController
{
	// 邮件队列数据
    private $queue;
    private $byname;

    // __construct 构造方法
    public function __construct()
    {
        parent::__construct();

        // 接口抽象化
        $this->queue = new QuereEmailController();

        // 别名定义
        $this->byname = 'Xab7mo7E';
    }

    public function oneEmail()
    {
        if (CompanyEmail::where('id', 1)->where('disabled', 'false')->first()) {
            $this->queue->emailResponse($this->byname, 1);
        }
    }

    public function twoEmail()
    {
        if (CompanyEmail::where('id', 2)->where('disabled', 'false')->first()) {
            $this->queue->emailResponse($this->byname, 2);
        }
    }

    public function threeEmail()
    {
        if (CompanyEmail::where('id', 3)->where('disabled', 'false')->first()) {
            $this->queue->emailResponse($this->byname, 3);
        }
    }

    public function fourEmail()
    {
        if (CompanyEmail::where('id', 4)->where('disabled', 'false')->first()) {
            $this->queue->emailResponse($this->byname, 4);
        }
    }

    public function fiveEmail()
    {
        if (CompanyEmail::where('id', 5)->where('disabled', 'false')->first()) {
            $this->queue->emailResponse($this->byname, 5);
        }
    }

    public function sixEmail()
    {
        if (CompanyEmail::where('id', 6)->where('disabled', 'false')->first()) {
            $this->queue->emailResponse($this->byname, 6);
        }
    }
}
