@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
    /*.announcement-heading {font-size: 35px;}*/
    pre {
        background: none;
        border: none;
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

        <!-- <h1>邮件设置
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config') }}">系统设置</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config/mail') }}">邮箱列表</a></li>
            <li class="active">邮箱创建</li>
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

            <div class="form-group clearfix {{{ $errors->has('email') ? 'has-error' : '' }}}">
                <label for="email" class="col-sm-2 control-label">企业邮箱</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Email" name="email" id="email" value="{{{ Input::old('email') }}}" />
                    <span class="help-block">{{{ $errors->first('email') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('host') ? 'has-error' : '' }}}">
                <label for="host" class="col-sm-2 control-label">服务器</label>
                <div class="col-sm-10">
                	<input type="text" class="form-control" placeholder="Host (例：imap.163.com)" 
                    name="host" id="host" value="{{{ Input::old('host') }}}" />
                    <span class="help-block">{{{ $errors->first('host') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('port') ? 'has-error' : '' }}}">
                <label for="port" class="col-sm-2 control-label">端口号</label>
                <div class="col-sm-10">
                	<input type="text" class="form-control" placeholder="Port (例：143)" 
                    name="port" id="port" value="{{{ Input::old('port') }}}" />
                    <span class="help-block">{{{ $errors->first('port') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('pass') ? 'has-error' : '' }}}">
                <label for="pass" class="col-sm-2 control-label">邮箱密码</label>
                <div class="col-sm-10">
                	<input type="password" class="form-control" placeholder="Pass" 
                    name="pass" id="pass" value="{{{ Input::old('pass') }}}" />
                    <span class="help-block">{{{ $errors->first('pass') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="email" class="col-sm-2 control-label">收件箱</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="收件箱目录" name="inbox_path" value="{{{ Input::old('inbox_path') }}}" />
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="email" class="col-sm-2 control-label">垃圾箱</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="垃圾箱目录" name="spam_path" value="{{{ Input::old('spam_path') }}}" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">创建</button>

                    <a href="{{ URL::to($com->path.'/admin/config/mail') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>


</div><!-- /.row -->

<!-- <div class="row">
    <div class="col-lg-12">
        <hr />
        <div class="alert alert-warning fade in">
            请正确设置上面所输入的企业邮箱以及 <strong>IMAP (非SSL)</strong>协议邮箱服务器：<br />
            1、用于邮件自动转发至 <strong>no</strong>，开启邮件生产工单功能；<br />
            2、用于工单邮件队列推送功能，借助 <strong>no</strong>匿名转发实现；<br />
            3、系统默认赠予每个入驻企业 <strong>10000</strong>封的邮件发送额度，请合理分配使用。
        </div>
    </div>
</div> -->
@stop