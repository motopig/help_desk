@extends('com.admin.layouts.default')

@section('title')
{{ $com->company_name }} :: @parent
@stop

<!-- binway样式交互引入 - start -->
<link href="{{{ asset('assets/css/order.css') }}}" rel="stylesheet">
<script charset="utf-8" src="{{{ asset('assets/js/order.js') }}}"></script>
<!-- binway样式交互引入 - end -->

@section('script')
@parent
<script>
$(function(){
        function check_firm(){
            if ($('#check_firm').is(':checked')) {
                $('#select_new_firm').removeClass('hidden');
                $('#select_new_group').addClass('hidden');
                $('#create_new_firm').addClass('hidden');
                $('#email').val('');
            } else {
                $('#create_new_firm').removeClass('hidden');
                $('#select_new_group').removeClass('hidden');
                $('#select_new_firm').addClass('hidden');
            }
        }

        // 初始化
        //
        check_firm();

        // 绑定 check 事件
        //
        $('#check_firm').bind('click',function(){
            check_firm();
        })
    });
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
          <li><a href="{{ URL::to($com->path.'/admin/order/createOrder') }}">创建工单</a></li>
          <li class="active"><a href="#">发送邮件</a></li>
        </ul>
        <br />

        <form class="col-sm-12" role="form" method="post" enctype="multipart/form-data" onsubmit="return check()">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="form-group clearfix {{{ $errors->has('title') ? 'has-error' : '' }}}">
                <label for="title" class="col-sm-2 control-label">邮件标题</label>
                <div class="col-sm-10">
                	<input type="text" class="form-control" id="title" name="title" value="" />
                    <span class="help-block">{{{ $errors->first('title') }}}</span>
                </div>
            </div>

            <!-- <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="check_firm" id="check_firm" value="1"
                            @if (Input::old('check_firm'))
                            checked
                            @endif
                            > 选择已有客户
                        </label>
                    </div>
                </div>
            </div> -->

            <div id="create_new_firm" class="clearfix">

            <div class="form-group clearfix {{{ $errors->has('email') ? 'has-error' : '' }}}">
                <label for="email" class="col-sm-2 control-label">收件人</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" placeholder="客户邮箱" name="email" id="email" value="" />

                    <span class="help-block">{{{ $errors->first('firm') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="email" class="col-sm-2 control-label">邮件抄送</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="不填请留空" name="cc" id="cc" value="" />
                </div>
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
                    <script id="task-text" name="content" type="text/plain"></script>
                    <span class="task-button-text">
                    <input type="file" id="reply_file" name="file[]" class="multi"/>
                    </span>
                </div>
            </div>
            <br />

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">确认</button>
                    @if ($level < 4)
                    <a href="{{ URL::to($com->path.'/admin/order') }}">
                    @else
                    <a href="{{ URL::to($com->path.'/admin/order?status=1') }}">
                    @endif
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
editors.render("task-text");

// 表单验证
function check() {
    if ($('#title').val() == '') {
        alert('请输入标题!');
        $('#title').focus();
        return false;
    }

    if (!$('#check_firm').is(':checked') && $('#email').val() == '') {
        alert('请输入收件人!');
        $('#email').focus();
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

    // if ($('#content').val() == '') {
    var vals = editors.getContent();
    if (vals == '') {
        alert('请输入邮件内容!');
        return false;
    }

    return true;
}
</script>
@stop