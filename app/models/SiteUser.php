<?php

class SiteUser extends Eloquent {

    protected $table = 'site_user';

	protected $guarded = array();

    public $timestamps = false;

	public static $rules = array();
}
