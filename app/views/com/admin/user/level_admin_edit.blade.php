@extends('com.admin.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>修改管理员
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/user') }}">客服管理</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/user/level/admin') }}">公司管理员</a></li>
            <li class="active">修改管理员</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <legend>基本信息</legend>

            <!-- <div class="form-group clearfix {{{ $errors->has('email') ? 'has-error' : '' }}}">
                <label for="email" class="col-sm-2 control-label">邮箱</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" placeholder="Email" name="email" id="email" value="{{{ Input::old('email', $user_info->user_name) }}}" />

                    <span class="help-block">{{{ $errors->first('email') }}}</span>
                </div>
            </div> -->

            <div class="form-group clearfix">
                <label for="email" class="col-sm-2 control-label">邮箱</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" readonly="readonly" name="email" id="email" value="{{{ $user_info->user_name }}}" />
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('name') ? 'has-error' : '' }}}">
                <label for="name" class="col-sm-2 control-label">姓名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Name" name="name" id="name" value="{{{ Input::old('name', $user_info->name) }}}" />

                    <span class="help-block">{{{ $errors->first('name') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('position') ? 'has-error' : '' }}}">
                <label for="position" class="col-sm-2 control-label">职位</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Position" name="position" id="position" value="{{{ Input::old('position', $admin_user->position) }}}" />

                    <span class="help-block">{{{ $errors->first('position') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('mobile') ? 'has-error' : '' }}}">
                <label for="mobile" class="col-sm-2 control-label">电话</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" value="{{{ Input::old('mobile', $user_info->mobile) }}}" />

                    <span class="help-block">{{{ $errors->first('mobile') }}}</span>
                </div>
            </div>

            <legend>修改密码 <small>不修改请为空</small></legend>

            <div class="form-group clearfix {{{ $errors->has('password') ? 'has-error' : '' }}}">

                <label for="password" class="col-sm-2 control-label">密码</label>

                <div class="col-sm-10">
                    <input type="password" class="form-control"  placeholder="Password" name="password" id="password" value="" />

                    <span class="help-block">{{{ $errors->first('password') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('password_confirmation') ? 'has-error' : '' }}}">

                <label for="password_confirmation" class="col-sm-2 control-label">确认密码</label>

                <div class="col-sm-10">
                    <input type="password" class="form-control"  placeholder="Password Confirmation" name="password_confirmation" id="password_confirmation" value="" />

                    <span class="help-block">{{{ $errors->first('password_confirmation') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="{{ URL::to($com->path.'/admin/user/level/admin') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>
</div><!-- /.row -->
@stop