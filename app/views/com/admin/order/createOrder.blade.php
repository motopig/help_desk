@extends('com.admin.layouts.default')

@section('title')
{{ $com->company_name }} :: @parent
@stop

<!-- binway样式交互引入 - start -->
<link href="{{{ asset('assets/css/order.css') }}}" rel="stylesheet">
<script charset="utf-8" src="{{{ asset('assets/js/order.js') }}}"></script>
<!-- binway样式交互引入 - end -->

<input type="hidden" id="checktype_user_admin" value="user" />
@section('script')
@parent
<script>
function askreply(type) {
	if (type == 1) {
		$("#replyuser").show();
		$("#askuser").hide();
        $("#select_new_group").hide();
        $("#checktype_user_admin").val("admin");
	} else if (type == 0) {
		$("#askuser").show();
        $("#select_new_group").show();
		$("#replyuser").hide();
        $("#checktype_user_admin").val("user");
	}
}
</script>
@stop

@section('content')
<div class="row">
<div class="col-lg-12">
<!-- <ol class="breadcrumb">
    <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
    <li class="active">工单管理</li>
</ol> -->
<!-- Notifications -->
@include('root.notifications')
<!-- ./ notifications -->
</div>
</div>

<!-- 工单左栏 - start -->
@include('com.admin.order.order_left')
<!-- 工单左栏 - ned -->

<!-- 工单右栏 - start -->
<div id="create-order-right">
	<div class="col-lg-12">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active"><a href="#">创建工单</a></li>
          <li><a href="{{ URL::to($com->path.'/admin/order/createTask') }}">发送邮件</a></li>
        </ul>
        <br />

        <form class="col-sm-12" role="form" method="post" enctype="multipart/form-data" onsubmit="return check()">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="form-group clearfix {{{ $errors->has('title') ? 'has-error' : '' }}}">
                <label for="title" class="col-sm-2 control-label">工单标题</label>
                <div class="col-sm-10">
                	<input type="text" class="form-control" id="title" name="title" value="{{{ Input::old('title') }}}" />
                    <span class="help-block">{{{ $errors->first('title') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <label for="order_level" class="col-sm-2 control-label">工单级别</label>
                <div class="col-sm-10">
                	<select name="level" class="form-control">
                    	<option value="0">默认为空</option>
                    	@if (isset($order_level))
                        @foreach ($order_level as $o)
                        <option value="{{ $o->id }}" >{{ $o->name }}</option>
                        @endforeach
	                    @endif
                    </select>
                </div>
                <br /><br />
            </div>

            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">工单类型</label>
                <div class="col-sm-10">
                	<input type="radio" onclick="askreply(0)" id="checktype" name="type" value="user" checked> 外部
					&nbsp;<input type="radio" onclick="askreply(1)" id="checktype" name="type" value="admin"> 内部
                </div>
                <br />
            </div>

            <div id="askuser" class="form-group clearfix {{{ $errors->has('user') ? 'has-error' : '' }}}">
                <label for="user" class="col-sm-2 control-label">申请人</label>
                <div class="col-sm-10">
                    <input type="email" id="check_user_name" class="form-control" placeholder="客户邮箱" name="user_name" />
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

            <div id="replyuser" style="display:none;" class="form-group clearfix {{{ $errors->has('user') ? 'has-error' : '' }}}">
                <label for="user" class="col-sm-2 control-label">申请人</label>
                <div class="col-sm-10">
                    <input type="radio" name="adminuser" value="{{Auth::user()->id}}" checked> <font color="red">{{Auth::user()->user_name}} ({{Auth::user()->name}})</font>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('admin') ? 'has-error' : '' }}}">
                <label for="admin" class="col-sm-2 control-label">受理人</label>
                <div class="col-sm-10">
                    @if ($admin->count() >= 1)
                    <select name="admin" class="form-control">
                        @foreach ($admin as $a)
                        <option value="{{ $a->id }}" >{{ $a->user_name }} ({{ $a->name }})</option>
                        @endforeach
                    </select>
                    @else
                    <div class="alert alert-warning">
                        提示：没有可用用户，请先 <a href="{{ URL::to($com->path.'/admin/user') }}">创建用户</a> ！
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="remark_content" class="col-sm-2 control-label">备注内容</label>
                <div class="col-sm-10">
                    <textarea class="task-form-control" id="remark_content" name="remark_content" rows="3" placeholder="不填请留空"></textarea>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('content') ? 'has-error' : '' }}}">
                <label for="content" class="col-sm-2 control-label">工单内容</label>
                <div class="col-sm-10">
                    <!-- <textarea class="task-form-control" name="content" rows="3" placeholder="请输入内容..."></textarea>
                    <span class="help-block">{{{ $errors->first('content') }}}</span> -->
                    <script id="order-text" name="content" type="text/plain"></script>
                    <span class="task-button-text">
                    <input type="file" id="reply_file" name="file[]" class="multi"/>
                    </span>
                </div>
            </div>
            <br />

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">确认</button>
                    <a href="{{ URL::to($com->path.'/admin/order') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>
        </form>
    </div>

</div>
<!-- 工单右栏 - ned -->

<script type="text/javascript">
var editors = new UE.ui.Editor({
    initialFrameHeight:150,
    initialFrameWidth:435,
});
editors.render("order-text");

// 表单验证
function check() {
    if ($('#title').val() == '') {
        alert('请输入标题!');
        $('#title').focus();
        return false;
    }

    if ($('#checktype_user_admin').val() == 'user' && $('#check_user_name').val() == '') {
        alert('请输入收件人!');
        $('#check_user_name').focus();
        return false;
    }

    // if ($('#content').val() == '') {
    var vals = editors.getContent();
    if (vals == '') {
        alert('请输入工单内容!');
        return false;
    }

    return true;
}
</script>
@stop