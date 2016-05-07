@extends('com.site.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('styles')
<!-- 点击查看大图风格 -->
<link href="{{{ asset('assets/css/jquery.lighter.css') }}}" rel="stylesheet">
@parent
<style>
/*公用样式*/
/*居右显示*/
.pull-right {
    float: right;
}
/*禁止链接虚线框*/
a {
    blr:expression(this.onFocus=this.blur())
}
a:hover{text-decoration:none;}
/*userlogo*/
.avatar {
    border-radius: 50%;
    height: 26px;
    width: 26px;
    display: inline-block;
    line-height: 1;
}
.media-object {
    border-radius: 50%;
    display: block;
    height: 40px;
    width: 40px;
}
/*底部textarae样式*/
.text-danger {
    resize: none;
    overflow: hidden;
    min-height: 100px;
}
/*公用样式*/

/*身体边框颜色*/
.alert-body {
    border-color: #E3E5CF;
    float: right;
    width: 80%;
    overflow:hidden;
}
/*工单身体左边栏*/
.alert-body-left {
    float: left;
    border-color: #A7BBB6;
    /*width: 15%;*/
    overflow:hidden;
    position:fixed;
}
/*工单提问回答内容样式*/
.alerts {
    border: 1px solid rgba(0, 0, 0, 0);
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 5px;
}
.alert-infos {
    background-color: #F4F8FA;
    border-color: #F4F8FA;
    color: #3A87AD;
    width: 330px;
    border-radius: 10px;
}
.alert-warnings {
    background-color: #FAF8F0;
    border-color: #FAF8F0;
    color: #C09853;
    width: 330px;
    border-radius: 10px;
}
.alert-kehu {
    background-color: #faf3be;
    border-color: #f1e9b0;
    border-radius: 10px;
    width: 330px;
}
.alert-yonghu {
    background-color: #c2e8fd;
    border-color: #96c9fc;
    border-radius: 10px;
    width: 330px;
}
.alert-xitong {
    background-color: #f5f4f4;
    border-color: #d7d8d9;
    border-radius: 10px;
    width: 330px;
}
/*工单title样式*/
.epanel-info {
    color: #5BC0DE;
}
.epanel-warning {
    color: #F0AD4E;
}
.epanel-danger {
    color: #D9534F;
}
.panel-default-infos {
    border-color: #5BC0DE;
}
.panel-default-warnings {
    border-color: #F0AD4E;
}
.panel-default-dangers {
    border-color: #D9534F;
}
.panel-heading {
    background-color: #F5F5F5;
}
.panel-group .panel {
    border-radius: 20px;
    margin-bottom: 0;
    overflow: hidden;
}
.thumbnail {
    background-color: #FFFFFF;
    border: 1px solid #DDDDDD;
    border-radius: 4px;
    display: block;
    height: 200px;
    line-height: 1.42857;
    margin-bottom: 20px;
    max-width: 100%;
    padding: 4px;
    transition: all 0.2s ease-in-out 0s;
}
.workorder-height {
    height: 30px;
    text-align:center;
}
.label {
    border-radius: 10px;
}
/*字符超出长度自动隐藏*/
.textOverFlow {
    width:150px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
</style>
@stop

@section('script')
<!-- 点击查看大图效果 -->
<script src="{{{ asset('assets/js/jquery.lighter.js') }}}"></script>
@parent
<script>
// 工具提示
function toolTip(tid) {
    $('#'+tid+'ask').tooltip();
    $('#fbpl').tooltip();
}

// 工单往来附件记录
$(function(){
    $('.view-link-enclosure').click(function(){
        $('#viewCustomerModalEnclosure').modal();
        $.ajax({
            type: "GET",
            url: $(this).attr('href')
        }).done(function(html_form) {
            $('#viewCustomerModalbodyEnclosure').html(html_form);
            $('#viewCustomerModalEnclosure').show();
        });
        return false;
    });
});
</script>
@stop

@section('content')
<div class="row">
<div class="col-lg-12">
<!-- <h1>工单管理<small>欢迎</small></h1> -->
<ol class="breadcrumb">
    <li><span class="glyphicon glyphicon-home"></span> <a href="{{ URL::to($com->path.'/') }}">首页</a></li>
    <!-- <li><a href="{{ URL::to($com->path.'/site/work') }}">工单管理</a></li> -->
    <li><a href="{{ URL::to($com->path.'/site/work/closed/') }}">已关闭工单</a></li>
    <li class="active">工单 #{{$workerOrder['0']->id}} (已关闭)</li>
</ol>
</div>
</div>
<div class="row">
<div class="col-lg-12">
<!-- 工单身体左边栏 -->
<div class="alert alert-body-left">
<div>
<dt>工单信息</dt>
<dd>工单号：#{{$workerOrder['0']->id}}</dd>
<dd class="textOverFlow" title="{{$workerOrder['0']->title}}">标题：{{$workerOrder['0']->title}}</dd>
<dd class="textOverFlow" title="{{$workerOrder['0']->group_name}}">所属组：{{$workerOrder['0']->group_name}}</dd>
<dd>状态：
@if ($workerOrder['0']->work_status == '1')
待认领
@elseif ($workerOrder['0']->work_status == '2')
处理中
@elseif ($workerOrder['0']->work_status == '3')
已处理
@elseif ($workerOrder['0']->work_status == '4')
已关闭
@endif
</dd>
<dd>级别：
@if ($workerOrder['0']->work_level == '1')
<span class="label label-info">一般</span>
@elseif ($workerOrder['0']->work_level == '2')
<span class="label label-warning">中等</span>
@elseif ($workerOrder['0']->work_level == '3')
<span class="label label-danger">紧急</span>
@endif
</dd>
<hr />
</div>
<div>
<dt>发起人信息</dt>
<dd class="textOverFlow" title="{{$workerOrder['0']->name}}">姓名：{{$workerOrder['0']->name}}</dd>
<hr />
</div>
<div>
<dt>受理人信息</dt>
<dd class="textOverFlow" title="{{$workerOrder['0']->admin_name}}">姓名：{{$workerOrder['0']->admin_name}}</dd>
<hr />
</div>
<a class="view-link-enclosure" href="{{ URL::to($com->path.'/site/work/enclosurework/'.$workerOrder['0']->id) }}">
    工单往来附件记录
</a>
</div>
<!-- 工单身体左边栏 -->

<!-- 工单身体信息 -->
<div class="alert alert-body">
    <ul class="media-list">
        @foreach ($content as $con)
        @if (isset($con->reply))
        @if ($con->system == 1)
        <li class="media">
          <a href="http://www.no" target="_blank" onFocus="this.blur()" class="pull-left">
            <img class="media-object" src="{{ asset('assets/img/apple.png') }}" onclick="findHref()">
          </a>
          <div class="media-body">
            <span class="media-heading">
                <b>系统提醒</b> 
                <font color="#6A8B82" size="1px;">{{$con->created_at}}</font>
            </span>
            <div class="alerts alert-xitong">
            <p>
            {{$con->reply}}
            </p>
            </div>
          </div>
        </li>
        @else
        <li class="media">
          <span class="pull-left" title="{{$con->position}}">
            @if ($con->admin_head == 'false')
            <a href="javascript:void(0)" onclick="replyAnchor('{{$con->name}}','{{$con->created_at}}')">
            <img class="media-object" src="{{ asset('assets/img/engineer.png') }}">
            </a>
            @else
            <a href="javascript:void(0)" onclick="replyAnchor('{{$con->name}}','{{$con->created_at}}')">
            <img class="media-object" src="{{ asset($con->admin_head) }}">
            </a>
            @endif
          </span>
          <div class="media-body">
            <span class="media-heading">
                <b>{{$con->name}}</b> 
                <font color="#6A8B82" size="1px;">{{$con->created_at}}</font> 
            </span>
            <div class="alerts alert-yonghu">
            <p id="{{$con->created_at}}">{{$con->reply}}</p>
                @if ($con->enclosure_id != 0)
                @if ($con->enclosure_suffix != 'docx' && $con->enclosure_suffix != 'xlsx' 
                && $con->enclosure_suffix != 'txt')
                <a href="{{ asset($con->enclosure_path) }}" data-lighter>
                <span class="glyphicon glyphicon-picture" title="点击放大：{{$con->enclosure_name}}"></span>
                </a>
                @else
                <a href="{{ asset($con->enclosure_path) }}">
                <span class="glyphicon glyphicon-link" title="点击下载：{{$con->enclosure_name}}"></span>
                </a>
                @endif
                @endif
            </div>
          </div>
        </li>
        @endif
        @elseif (isset($con->ask))
        <li class="media">
          <span class="pull-right" title="{{$workerOrder['0']->firm}}">
            @if ($workerOrder['0']->head == 'false')
            <img class="media-object" src="{{ asset('assets/img/customer.png') }}">
            @else
            <img class="media-object" src="{{ asset($workerOrder['0']->head) }}">
            @endif
          </span>
          <div class="media-body pull-right">
            <span class="media-heading pull-right">
                <font color="#6A8B82" size="1px;">{{$con->created_at}}</font> 
                <b>{{$workerOrder['0']->name}}</b>
            </span>
            <br />
            <div class="alerts alert-kehu">
                <p>{{$con->ask}}</p>
                @if ($con->ask_anchor != '')
                <a href="#{{$con->ask_anchor}}">
                    <span class="glyphicon glyphicon-send" id="{{$con->id}}reply" onmouseover="toolTip({{$con->id}})" 
                        data-placement="bottom" data-original-title="{{$con->ask_anchor}}">
                    </span>
                </a> 
                @endif
                @if ($con->enclosure_id != 0)
                @if ($con->enclosure_suffix != 'docx' && $con->enclosure_suffix != 'xlsx' 
                && $con->enclosure_suffix != 'txt')
                <a href="{{ asset($con->enclosure_path) }}" data-lighter>
                <span class="glyphicon glyphicon-picture" title="点击放大：{{$con->enclosure_name}}"></span>
                </a>
                @else
                <a href="{{ asset($con->enclosure_path) }}">
                <span class="glyphicon glyphicon-link" title="点击下载：{{$con->enclosure_name}}"></span>
                </a>
                @endif
                @endif
            </div>
          </div>
        </li>
        @endif
        @endforeach
      </ul>
</div>
<!-- 工单身体信息 -->
</div>
</div>

<!-- 工单处理信息 -->
<div class="row">
<div class="col-lg-12" style="width:80%;float:right;">
<hr >
<a href="{{ URL::to($com->path.'/site/work/closed/') }}">
<button type="button" class="btn btn-default">取消返回</button>
</a>
</div>
</div>
<!-- 工单处理信息 -->

<!-- 工单往来附件记录 -->
<div class="modal fade" id="viewCustomerModalEnclosure" tabindex="-1" role="dialog" 
aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h4 class="modal-title" id="viewCustomerModalLabel">工单往来附件记录</h4>
</div>
<div class="modal-body" id="viewCustomerModalbodyEnclosure">
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</div>
</div>
</div>
</div>
<!-- 工单往来附件记录 -->
@stop