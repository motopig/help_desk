<?php

class WorkOrder extends ComBaseModel {

    // 对应数据库
    protected $table = 'work_order';

	protected $guarded = array();

	public static $rules = array();
}
