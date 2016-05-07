<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

// root 权限验证 by cooper
//
Route::filter('root.auth', function()
{
    // 用户权限验证
    //
    if (Auth::guest())
    {
        return Redirect::guest('root/login');
    }
    else
    {
        // 获取配置文件
        //
        $sup = explode(',', Config::get('app.superman'));

        // 判断是否是超级管理员
        //
        if (!in_array(Auth::user()->id, $sup))
        {
            return Redirect::guest('root/login')->with('error', '您不是超级管理员');
        }
    }
});

// 公司后台权限验证 by cooper
//
Route::filter('com.admin.auth', function()
{
    // 用户权限验证
    //
    if (Auth::guest())
    {
        return Redirect::guest(Config::get('app.com_path') . '/admin/login');
    }
    else
    {
        // 查询关联表 （查询信息缓存60分钟）
        //
        $admin = AdminUser::where('company_id', Config::get('app.com_id'))->where('user_id', Auth::user()->id)->where('disabled', 'false')->remember(60)->first();

        if (!$admin)
        {
            return Redirect::guest(Config::get('app.com_path') . '/admin/login')->with('error', '权限不够');
        } else {
            // 分配权限
            //
            View::share('level', $admin->level);

            // 设置公司后台权限ID
            //
            Config::set('auth.com_admin_level_id', $admin->level);

            // TODO 公司请求权限处理
        }

    }
});

// 公司前台权限验证 by cooper
//
Route::filter('com.site.auth', function()
{
    // 用户权限验证
    //
    if (Auth::guest())
    {
        return Redirect::guest(Config::get('app.com_path') . '/login');
    }
    else
    {
        // 查询关联表 （查询信息缓存60分钟）
        //
        $user = SiteUser::where('company_id', Config::get('app.com_id'))->where('user_id', Auth::user()->id)->where('disabled', 'false')->remember(60)->first();

        if (!$user)
        {
            return Redirect::guest(Config::get('app.com_path') . '/login')->with('error', '权限不够');
        }

    }
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('csrf_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});