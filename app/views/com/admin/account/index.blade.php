@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
.headp {
    color: #999999;
    font-size: 13px;
}
.avatar {
    border-radius: 50%;
    display: block;
    height: 50px;
    width: 50px;
}
</style>
@stop

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>个人资料
            <small>你好，{{{Auth::user()->name }}} !</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li class="active">个人资料</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">

    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post" enctype="multipart/form-data">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <legend>基本信息</legend>

            <!-- 获取头像 - no -->
            <div class="form-group clearfix {{{ $errors->has('head') ? 'has-error' : '' }}}">
                <label for="head" class="col-sm-2 control-label">
                    @if ($user->head == 0)
                    <img title="{{{Auth::user()->name }}}" src="{{{ asset('assets/img/engineer.png') }}}" class="avatar" />
                    @else
                    <img title="{{{Auth::user()->name }}}" src="{{{ asset($user->enclosures_path) }}}" class="avatar" />
                    @endif
                </label>
                <div class="col-sm-10">
                    <div class="controls">
                    <p class="headp">你可以选择png/jpg/jpeg/gif/bmp图片(100*100)作为新头像</p>
                    <input type="file" name="file[]" id="file" value="" />
                    </div>
                    <span class="help-block">{{{ $errors->first('head') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('email') ? 'has-error' : '' }}}">
                <label for="email" class="col-sm-2 control-label">邮箱</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" placeholder="Email" name="email" id="email" value="{{{ Input::old('email', $user->user_name) }}}" disabled />

                    <span class="help-block">{{{ $errors->first('email') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('name') ? 'has-error' : '' }}}">
                <label for="name" class="col-sm-2 control-label">姓名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Name" name="name" id="name" value="{{{ Input::old('name', $user->name) }}}" />

                    <span class="help-block">{{{ $errors->first('name') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('mobile') ? 'has-error' : '' }}}">
                <label for="mobile" class="col-sm-2 control-label">电话</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" value="{{{ Input::old('mobile', $user->mobile) }}}" />

                    <span class="help-block">{{{ $errors->first('mobile') }}}</span>
                </div>
            </div>

            <legend>修改密码 &nbsp;&nbsp;&nbsp;<small>不修改请为空</small></legend>

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

                    <button type="submit" class="btn btn-primary">修改</button>
                    <a href="{{ URL::to($com->path.'/admin') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>


</div><!-- /.row -->
@stop