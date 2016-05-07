<?php

// 向客户群发邮件
Route::get('client/groupsending', 'ComAdminClientController@getGroupSending');
// 向客户发送邮件处理
Route::post('client/groupsending', 'ComAdminClientController@postGroupSending');
