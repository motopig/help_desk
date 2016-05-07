<?php

class ComBaseModel extends Eloquent {

	protected $guarded = array();

	public static $rules = array();

    public function __construct(){

        // 根据 app.com_id 切换表

        if (Config::get('app.com_id'))
        {
            $this->table = Config::get('app.com_id') . '_' . $this->table;
        }
    }
}
