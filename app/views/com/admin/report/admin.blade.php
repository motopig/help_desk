@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
.pull-right {
    float: right;
}
</style>
@stop

@section('script')
@parent
<script src="{{{ asset('assets/js/Highcharts-3.0.9/js/highcharts.js') }}}"></script>
<script src="{{{ asset('assets/js/Highcharts-3.0.9/js/modules/exporting.js') }}}"></script>

<script>
$(document).ready(function() {
    // 处理工单最多的人
    var manyWorkUser;
    if ('{{$manyWorkUser}}') {
        manyWorkUser = '({{$manyWorkUser}})';
    } else {
        manyWorkUser = '';
    }

    // 处理工单最少的人
    var lessWorkUser;
    if ('{{$lessWorkUser}}') {
        lessWorkUser = '({{$lessWorkUser}})';
    } else {
        lessWorkUser = '';
    }

    // 工单回复最多的人
    var manyAskUser;
    if ('{{$manyAskUser}}') {
        manyAskUser = '({{$manyAskUser}})';
    } else {
        manyAskUser = '';
    }

    // 工单回复最少的人
    var lessAskUser;
    if ('{{$lessAskUser}}') {
        lessAskUser = '({{$lessAskUser}})';
    } else {
        lessAskUser = '';
    }
    
    // 工单总数统计
    $('#container').highcharts({ // 图表展示容器，与div的id保持一致
        chart: {
            type: 'column' // 指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '用户统计报表' // 指定图表标题
        },
        xAxis: {
            categories: ['用户统计', '用户统计', '用户统计', '用户统计', '用户统计', '用户统计', '用户统计', '用户统计', '用户统计'] // 指定x轴分组
        },
        yAxis: {
            title: {
                text: '数量' // 指定y轴的标题
            }
        },
        series: [{ // 指定数据列
            name: '当前用户总数', // 数据列名
            data: [{{$adminCount}}], // 数据
        }, {
            name: 'CEO',
            data: [1],
        }, {
            name: 'Manager',
            data: [{{$adminTwo}}],
        }, {
            name: 'Leader',
            data: [{{$adminThree}}],
        }, {
            name: 'Staff',
            data: [{{$adminFour}}],
        }, {
            name: '处理工单最多的人 '+manyWorkUser,
            data: [{{$manyWorkCount}}],
        }, {
            name: '处理工单最少的人 '+lessWorkUser,
            data: [{{$lessWorkCount}}],
        }, {
            name: '工单回复最多的人 '+manyAskUser,
            data: [{{$manyAskCount}}],
        }, {
            name: '工单回复最少的人 '+lessAskUser,
            data: [{{$lessAskCount}}]
        }]
    });
});

// 图表切换
function tubiao() {
    $("#container").show();
    $("#datatable").hide();
    $("#tubiao").addClass("btn-primary").removeClass("btn-default");
    $("#biaoge").addClass("btn-default").removeClass("btn-primary");
}

// 表格切换
function biaoge() {
    $("#datatable").show();
    $("#container").hide();
    $("#tubiao").addClass("btn-default").removeClass("btn-primary");
    $("#biaoge").addClass("btn-primary").removeClass("btn-default");
}
</script>
@stop

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li class="active">报表统计</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->
    </div>
</div><!-- /.row -->

<ul class="nav nav-tabs" id="myTab">

<li>
<a href="{{ URL::to($com->path.'/admin/report/') }}">
工单报表
</a>
</li>

<li>
<a href="{{ URL::to($com->path.'/admin/report/user/') }}">
客户报表
</a>
</li>

<li class="active">
<a href="#admin">
用户报表
</a>
</li>

</ul>
<br /><br />

<div class="tab-content">

<div class="tab-pane fade in active" id="admin">
<div>
    <a href="javascript:void(0)" onclick="tubiao()" id="tubiao" class="btn btn-xs btn-primary">图表</a> | 
    <a href="javascript:void(0)" onclick="biaoge()" id="biaoge" class="btn btn-xs btn-default">表格</a>
</div>

<!-- 客户统计图表 -->
<div id="container" style="min-width:800px;height:400px"></div>

<!-- 客户统计表格 -->
<table id="datatable" style="display:none;" class="table table-bordered table-striped">
    <br />
    <thead>
      <tr>
        <th></th>
        <th><font color="#274b6d">总数</font></th>
        <th><font color="#274b6d">用户</font></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>当前用户总数</th>
         <td><font color="#2f7ed8">{{$adminCount}}</font></td>
         <td><font color="#2f7ed8">-</font></td>
      </tr>
      <tr>
        <th>CEO总数</th>
         <td><font color="#0d233a">1</font></td>
         <td><font color="#0d233a">-</font></td>
      </tr>
      <tr>
        <th>Manager总数</th>
         <td><font color="#8bbc21">{{$adminTwo}}</font></td>
         <td><font color="#8bbc21">-</font></td>
      </tr>
      <tr>
        <th>Leader总数</th>
         <td><font color="#910000">{{$adminThree}}</font></td>
         <td><font color="#910000">-</font></td>
      </tr>
      <tr>
        <th>Staff总数</th>
         <td><font color="#1aadce">{{$adminFour}}</font></td>
         <td><font color="#1aadce">-</font></td>
      </tr>
      <tr>
        <th>处理工单最多的人</th>
        <td><font color="#492970">{{$manyWorkCount}}</font></td>
        <td>
            <font color="#492970">
            @if ($manyWorkUser)
            {{$manyWorkUser}}
            @else
            -
            @endif
            </font>
        </td>
      </tr>
      <tr>
        <th>处理工单最少的人</th>
        <td><font color="#f28f43">{{$lessWorkCount}}</font></td>
        <td>
            <font color="#f28f43">
                @if ($lessWorkUser)
                {{$lessWorkUser}}
                @else
                -
                @endif
            </font>
        </td>
      </tr>
      <tr>
        <th>工单回复最多的人</th>
        <td><font color="#77a1e5">{{$manyAskCount}}</font></td>
        <td>
            <font color="#77a1e5">
                @if ($manyAskUser)
                {{$manyAskUser}}
                @else
                -
                @endif
            </font>
        </td>
      </tr>
      <tr>
        <th>工单回复最少的人</th>
        <td><font color="#c42525">{{$manyAskCount}}</font></td>
        <td>
            <font color="#c42525">
                @if ($lessAskUser)
                {{$lessAskUser}}
                @else
                -
                @endif
            </font>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<hr />

<!-- 排行统计表格 -->
<table id="datatableRank" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th><font color="#274b6d">工单回复排名</font></th>
        <th><font color="#274b6d">用户</font></th>
        <th><font color="#274b6d">工单回复数</font></th>
      </tr>
    </thead>
    <tbody>
      @if ($rankUser != 0)
      @foreach ($rankUser as $r)
      <tr>
        <td>#{{$r->rank_id}}</td>
        <td>{{$r->user_name}}</td>
        <td>{{$r->count}}</td>
      </tr>
      @endforeach
      @else
      <tr>
        <td>-</td>
        <td>-</td>
        <td>0</td>
      </tr>
      @endif
    </tbody>
  </table>
</div>

</div>
@stop
