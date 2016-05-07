<?php

class Order extends ComBaseModel {

    // 对应数据库
    protected $table = 'order';

	protected $guarded = array();

	public static $rules = array();
}
