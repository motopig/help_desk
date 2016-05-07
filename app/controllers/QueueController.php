<?php

// 队列脚本
class QueueController extends BaseController
{
	public function add()
	{
		Queue::push('MyQueueController@oneEmail', array());
		Queue::push('MyQueueController@twoEmail', array());
		Queue::push('MyQueueController@threeEmail', array());
		Queue::push('MyQueueController@fourEmail', array());
		Queue::push('MyQueueController@fiveEmail', array());
		Queue::push('MyQueueController@sixEmail', array());
	}
}
