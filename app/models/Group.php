<?php

class Group extends ComBaseModel {

    // 对应数据库
    protected $table = 'groups';

	protected $guarded = array();

	public static $rules = array();

    /**
     * 业务形态
     *
     * @return mixed
     */
    public function worktype()
    {
        return $this->belongsTo('WorkType', 'work_type_id');
    }

}
