<?php

class OrderDraft extends ComBaseModel {

    // 对应数据库
    protected $table = 'order_draft';

	protected $guarded = array();

	public static $rules = array();
}
