@extends('com.admin.layouts.default')

<link href="{{{ asset('assets/css/order.css') }}}" rel="stylesheet">
<script charset="utf-8" src="{{{ asset('assets/js/order.js') }}}"></script>

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>客户管理
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/client') }}">客户管理</a></li>
            <li class="active">发送邮件</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post" enctype="multipart/form-data" onsubmit="return check()">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="form-group clearfix {{{ $errors->has('title') ? 'has-error' : '' }}}">
                <label for="title" class="col-sm-2 control-label">邮件标题</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" value="" />
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('email') ? 'has-error' : '' }}}">
                <label for="email" class="col-sm-2 control-label">收件人</label>
                <div class="col-sm-10">
                    <input type="radio" name="member" value="{{$user_info->id}}" checked> {{$user_info->user_name}} 
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="email" class="col-sm-2 control-label">邮件抄送</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="不填请留空" name="cc" id="cc" value="" />
                </div>
            </div>

            <div id="select_new_group" class="form-group clearfix {{{ $errors->has('group') ? 'has-error' : '' }}}">
                <label for="user" class="col-sm-2 control-label">所属组</label>
                <div class="col-sm-10">
                    <select name="group" class="form-control">
                        @foreach ($groups as $g)
                        <option value="{{ $g->id }}" >{{ $g->group_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('admin') ? 'has-error' : '' }}}">
                <label for="admin" class="col-sm-2 control-label">发件人</label>
                <div class="col-sm-10">
                    <input type="radio" name="admin" value="{{Auth::user()->id}}" checked> <font color="red">{{Auth::user()->user_name}} ({{Auth::user()->name}})</font>
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="remark_content" class="col-sm-2 control-label">备注内容</label>
                <div class="col-sm-10">
                    <textarea class="task-form-control" id="remark_content" name="remark_content" rows="3" placeholder="不填请留空"></textarea>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('content') ? 'has-error' : '' }}}">
                <label for="content" class="col-sm-2 control-label">邮件内容</label>
                <div class="col-sm-10">
                    <!-- <textarea class="task-form-control" id="content" name="content" rows="3" placeholder="请输入内容..."></textarea>
                    <span class="task-button-text">
                    <input type="file" id="reply_file" name="file[]" class="multi"/>
                    </span>
                    
                    <span class="help-block">{{{ $errors->first('content') }}}</span> -->
                    <script id="email-text" name="content" type="text/plain"></script>
                    <span class="task-button-text">
                    <input type="file" id="reply_file" name="file[]" class="multi"/>
                    </span>
                </div>
            </div>
            <br />

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">确认</button>
                    <a href="{{ URL::to($com->path.'/admin/client') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>
</div><!-- /.row -->

<script type="text/javascript">
var editors = new UE.ui.Editor({
    initialFrameHeight:150,
    initialFrameWidth:415,
});
editors.render("email-text");

// 表单验证
function check() {
    if ($('#title').val() == '') {
        alert('请输入标题!');
        $('#title').focus();
        return false;
    }

    var cc = $('#cc').val();
    if (cc != '') {
        if ((cc.split('@').length-1) == 0) {
            alert('抄送邮件格式不正确！');
            $('#cc').focus();
            return false;
        } else {
            if ((cc.split('@').length-1) > 1 && (cc.split(',').length-1) == 0) {
                alert('抄送邮件格式不正确！');
                $('#cc').focus();
                return false;
            }
        }
    }

    var vals = editors.getContent();
    if (vals == '') {
        alert('请输入邮件内容!');
        return false;
    }

    return true;
}
</script>
@stop