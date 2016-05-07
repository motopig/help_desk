@extends('root.layouts.default')


@section('content')
<div class="row">

    <div class="col-lg-12">

        <h1>修改用户密码
            <small>你好，{{{Auth::user()->name }}} !</small>
        </h1>

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to('root') }}">首页</a></li>
            <li><a href="{{ URL::to('root/user') }}">用户管理</a></li>
            <li class="active">修改用户密码</li>
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

            <legend>用户：{{ $user->user_name }}</legend>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="form-group clearfix {{{ $errors->has('password') ? 'has-error' : '' }}}">

                <label for="password" class="col-sm-3 control-label">密码</label>

                <div class="col-sm-9">
                    <input type="password" class="form-control"  placeholder="Password" name="password" id="password" value="" />

                    <span class="help-block">{{{ $errors->first('password') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('password_confirmation') ? 'has-error' : '' }}}">

                <label for="password_confirmation" class="col-sm-3 control-label">确认密码</label>

                <div class="col-sm-9">
                    <input type="password" class="form-control"  placeholder="Password Confirmation" name="password_confirmation" id="password_confirmation" value="" />

                    <span class="help-block">{{{ $errors->first('password_confirmation') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary">修改</button>
                    <a href="{{ URL::to('root/user') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>
    </div>
</div><!-- /.row -->
@stop

