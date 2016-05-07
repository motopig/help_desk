@extends('com.site.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('styles')
@parent
<style>
.pull-right {
    float: right;
}
.alert-infos {
    background-color: #F4F8FA;
    border-color: #F4F8FA;
    color: #3A87AD;
}
.alert-dangers {
    background-color: #FDF7F7;
    border-color: #FDF7F7;
    color: #B94A48;
}
.alert-warnings {
    background-color: #FAF8F0;
    border-color: #FAF8F0;
    color: #C09853;
}
.alerts {
    border: 1px solid rgba(0, 0, 0, 0);
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 15px;
}
a {
    blr:expression(this.onFocus=this.blur())
}
/*userlogo*/
.avatar {
    border-radius: 50%;
    height: 26px;
    width: 26px;
    display: inline-block;
    line-height: 1;
}
/*搜索框*/
#txt-search {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #A7C0B9;
    border-radius: 16px;
    font-size: 12px;
    /*line-height: 12px;*/
    padding: 4px 10px 3px 28px;
    transition: width 150ms ease-in-out 0s;
    float: right;
    margin-top: 7px;
    display: block;
}
.link-search {
    float: right;
    right: -22px;
    top: 12px;
}
.keyword ::-moz-placeholder {
    color: #999999;
    outline: 0;
}
.keyword-focus {
    outline: 0;
}
</style>
@stop

@section('script')
@parent
<script>
// 工具提示
function toolTip(tid) {
    $('#'+tid+'l').tooltip();
    $('#'+tid+'f').tooltip();
}

// 工单标题搜索
function txtSearch() {
    var vals = $("#txt-search").val();
    if (vals == '') {
        alert("请输入工单标题！");
        return false;
    } else {
        window.location.href = "{{ URL::to($com->path.'/site/work/searchwork/1/"+vals+"') }}";
    }
}

// 动态添加一行最新回复
function showTime(tid, head, name, time, content, work_level) {
    var str = '';

    str += '<tr id="showtrtime'+tid+'">';
    if (work_level == '3') {
        str += '<td colspan="8" style="background-color:#FFFFFF">';
    } else if (work_level == '2') {
        str += '<td colspan="8" style="background-color:#FFFFFF">';
    } else {
        str += '<td colspan="8" style="background-color:#FFFFFF">';
    }

    if (head == '0') {
        str += '&nbsp;<span class="glyphicon glyphicon-user"></span>';
    } else {
        str += '&nbsp;<img class="avatar" src="{{ asset("'+head+'") }}">';
    }

    str += '&nbsp;<b>'+name+' · '+'</b>'+'<font color="#6A8B86">'+time+'</font>';
    if (work_level == '3') {
        str += '<div class="alerts alert-dangers">';
    } else if (work_level == '2') {
        str += '<div class="alerts alert-warnings">';
    } else {
        str += '<div class="alerts alert-infos">';
    }
    str += content+'</div>';
    str += '</td></tr>';

    $("#showtr"+tid).after(str);
    
    $("#showa"+tid).hide();
    $("#hidea"+tid).show();
}

// 动态删除一行最新回复
function hideTime(tid) {
    $("tr[id=showtrtime"+tid+"]").remove();

    $("#showa"+tid).show();
    $("#hidea"+tid).hide();
}
</script>
@stop

@section('content')
<div class="row">
<div class="col-lg-12">
<!-- <h1>工单管理<small>欢迎</small></h1> -->
<ol class="breadcrumb">
    <li><span class="glyphicon glyphicon-home"></span> <a href="{{ URL::to($com->path.'/') }}">首页</a></li>
    <li class="active">待受理工单</li>
</ol>
</div>
</div>
<div class="row">
<div class="col-lg-12">

<ul class="nav nav-tabs" id="myTab">
<li class="active">
<a href="#index" data-toggle="tab">
待受理
@if ($workCount['1'] > 0)
&nbsp;<span class="badge">{{$workCount['1']}}</span>
@endif
</a>
</li>

<li>
<a href="{{ URL::to($com->path.'/site/work/untreated/') }}">
处理中
@if ($workCount['2'] > 0)
&nbsp;<span class="badge">{{$workCount['2']}}</span>
@endif
</a>
</li>

<li>
<a href="{{ URL::to($com->path.'/site/work/processed/') }}">
已处理
@if ($workCount['3'] > 0)
&nbsp;<span class="badge">{{$workCount['3']}}</span>
@endif
</a>
</li>

<li>
<a href="{{ URL::to($com->path.'/site/work/closed/') }}">
已关闭
@if ($workCount['4'] > 0)
&nbsp;<span class="badge">{{$workCount['4']}}</span>
@endif
</a>
</li>

<span class="keyword">
<input id="txt-search" class="keyword-focus" type="text" name="keyword" placeholder="搜索工单标题" onBlur="searchClean(this)" />
<a href="javascript:void(0)" onclick="txtSearch()">
<span class="glyphicon glyphicon-search link-search"></span>
</a>
</span>
</ul>

<br />
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive clearfix">

            <table class="table table-hover table-striped  tablesorter">
                <thead>
                <tr>
                    <th>工单号 <i class="fa fa-sort"></i></th>
                    <th>标题 (级别) <i class="fa fa-sort"></i></th>
                    <th>创建时间 <i class="fa fa-sort"></i></th>
                    <th>最新回复 <i class="fa fa-sort"></i></th>
                    <th>发起人</th>
                    <th>受理组</th>
                    <th>受理人</th>
                    <th>操作</th>
                </tr>
                </thead>

                    <tbody>
                    @if ($workerOrders)
                    @foreach ($workerOrders as $wo)
                    <tr id="showtr{{$wo->id}}">
                    <td>
                        <a href="{{ URL::to($com->path.'/site/work/workdetail/'.$wo->id) }}">#{{$wo->id}}</a>
                    </td>
                    <td>
                        <a href="{{ URL::to($com->path.'/site/work/workdetail/'.$wo->id) }}">
                        @if ($wo->work_level == '3')
                        <font color="#d9534f">{{$wo->title}}</font>
                        @elseif ($wo->work_level == '2')
                        <font color="#f0ad4e">{{$wo->title}}</font>
                        @else
                        {{$wo->title}}
                        @endif
                        </a>
                    </td>
                    <td>{{$wo->created_at}}</td>
                    <td>
                        {{$wo->time}}
                        <a id="showa{{$wo->id}}" title="pass me" href="javascript:void(0)" 
                        onclick="showTime({{$wo->id}},'{{$wo->head}}','{{$wo->name}}','{{$wo->time}}','{{$wo->content}}','{{$wo->work_level}}')">
                        <span class="glyphicon glyphicon-hand-down"></span>
                        </a>
                        <a id="hidea{{$wo->id}}" title="pass me" style="display:none" href="javascript:void(0)" onclick="hideTime({{$wo->id}})">
                        <span class="glyphicon glyphicon-hand-up"></span>
                        </a>
                    </td>
                    <td>{{$wo->name}}</td>
                    <td>{{$wo->group_name}}</td>
                    <td>-</td>
                    <td>
                        <a class="btn btn-xs btn-primary" 
                        href="{{ URL::to($com->path.'/site/work/workdetail/'.$wo->id) }}">
                        回复
                        </a>
                        <a class="btn btn-xs btn-danger" 
                        href="{{ URL::to($com->path.'/site/work/workdetail/'.$wo->id) }}">
                        关闭
                        </a>
                    </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                    <td>
                    <h4 class="panel-title">暂无工单记录!</h4>
                    </td>
                    </tr>
                    @endif
                    </tbody>
            </table>

        </div>
    </div>
</div><!-- /.row -->

@if ($workerOrder)
{{$workerOrder->links()}}
@endif

@stop