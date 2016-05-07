<?php

class GroupAdminUser extends ComBaseModel {

    // 对应数据库
    protected $table = 'group_admin_user';

	protected $guarded = array();

    public $timestamps = false;

	public static $rules = array();

    /**
     * 用户信息
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }
}
