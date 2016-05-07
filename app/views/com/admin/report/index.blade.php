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
    // 工单总数统计
    $('#container').highcharts({ // 图表展示容器，与div的id保持一致
        chart: {
            type: 'column' // 指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '工单总数统计' // 指定图表标题
        },
        xAxis: {
            categories: ['工单周期', '工单周期', '工单周期', '工单周期', '工单周期'] // 指定x轴分组
        },
        yAxis: {
            title: {
                text: '数量' // 指定y轴的标题
            }
        },
        series: [{ // 指定数据列
            name: '今日工单总数', // 数据列名
            data: [{{$toDayWork}}], // 数据
        }, {
            name: '昨日工单总数',
            data: [{{$yesterdayWork}}],
        }, {
            name: '上周工单总数',
            data: [{{$weekWork}}],
        }, {
            name: '本月工单总数',
            data: [{{$monthWork}}],
        }, {
            name: '当前工单总数',
            data: [{{$workCount}}]
        }]
    });

    // 频率变化统计
    $(function () {
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
            return {
                radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                stops: [
                    [0, color],
                    [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
                ]
            };
        });

        $('#containerNew').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: '频率变化统计'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(1) +' %';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: '百分比',
                data: [
                    ['本日新增工单', {{$newToDayWork}}],
                    ['本周新增工单', {{$newWeek}}],
                    ['本月新增工单', {{$newMonthWork}}]
                ]
            }]
        });
    });

    // 工单质量统计
    $('#containerStatus').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '工单质量统计'
        },
        xAxis: {
            categories: ['工单状态', '工单状态', '工单状态', '工单状态']
        },
        yAxis: {
            title: {
                text: '数量'
            }
        },
        series: [{
            name: '未受理工单总数',
            data: [{{$statusOne}}],
        }, {
            name: '处理中工单总数',
            data: [{{$statusTwo}}],
        }, {
            name: '已处理工单总数',
            data: [{{$statusThree}}],
        }, {
            name: '已关闭工单总数',
            data: [{{$statusFour}}],
        }]
    });
});

// 图表切换
function tubiao() {
    $("#container").show();
    $("#datatable").hide();
    $("#containerNew").show();
    $("#datatableNew").hide();
    $("#containerStatus").show();
    $("#datatableStatus").hide();
    $("#tubiao").addClass("btn-primary").removeClass("btn-default");
    $("#biaoge").addClass("btn-default").removeClass("btn-primary");
}

// 表格切换
function biaoge() {
    $("#datatable").show();
    $("#container").hide();
    $("#datatableNew").show();
    $("#containerNew").hide();
    $("#datatableStatus").show();
    $("#containerStatus").hide();
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

<li class="active">
<a href="#index" data-toggle="tab">
工单报表
</a>
</li>

<li>
<a href="{{ URL::to($com->path.'/admin/report/user/') }}">
客户报表
</a>
</li>

<li>
<a href="{{ URL::to($com->path.'/admin/report/admin/') }}">
用户报表
</a>
</li>

</ul>
<br /><br />

<div class="tab-content">

<div class="tab-pane fade in active" id="index">
<div>
    <a href="javascript:void(0)" onclick="tubiao()" id="tubiao" class="btn btn-xs btn-primary">图表</a> | 
    <a href="javascript:void(0)" onclick="biaoge()" id="biaoge" class="btn btn-xs btn-default">表格</a>
</div>

<!-- 工单总数统计图表 -->
<div id="container" style="min-width:800px;height:400px"></div>

<!-- 工单总数统计表格 -->
<table id="datatable" style="display:none;" class="table table-bordered table-striped">
    <br />
    <thead>
      <tr>
        <th></th>
        <th><font color="#274b6d">工单总数统计</font></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>今日工单总数</th>
        <td><font color="#2f7ed8">{{$toDayWork}}</font></td>
      </tr>
      <tr>
        <th>昨日工单总数</th>
        <td><font color="#0d233a">{{$yesterdayWork}}</font></td>
      </tr>
      <tr>
        <th>上周工单总数</th>
        <td><font color="#8bbc21">{{$weekWork}}</font></td>
      </tr>
      <tr>
        <th>本月工单总数</th>
        <td><font color="#910000">{{$monthWork}}</font></td>
      </tr>
      <tr>
        <th>当前工单总数</th>
        <td><font color="#1aadce">{{$workCount}}</font></td>
      </tr>
    </tbody>
  </table>
</div>

<hr />

<!-- 频率变化统计图表 -->
<div id="containerNew" style="min-width:800px;height:400px"></div>

<!-- 频率变化统计表格 -->
<table id="datatableNew" style="display:none;" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th></th>
        <th><font color="#274b6d">频率变化统计</font></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>本日新增工单</th>
        <td><font color="#2f7ed8">{{$newToDayWork}}</font></td>
      </tr>
      <tr>
        <th>本周新增工单</th>
        <td><font color="#0d233a">{{$newWeek}}</font></td>
      </tr>
      <tr>
        <th>本月新增工单</th>
        <td><font color="#8bbc21">{{$newMonthWork}}</font></td>
      </tr>
    </tbody>
  </table>
</div>

<hr />

<!-- 工单质量统计图表 -->
<div id="containerStatus" style="min-width:800px;height:400px"></div>

<!-- 工单质量统计表格 -->
<table id="datatableStatus" style="display:none;" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th></th>
        <th><font color="#274b6d">工单质量统计</font></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>未受理工单总数</th>
        <td><font color="#2f7ed8">{{$statusOne}}</font></td>
      </tr>
      <tr>
        <th>处理中工单总数</th>
        <td><font color="#0d233a">{{$statusTwo}}</font></td>
      </tr>
      <tr>
        <th>已处理工单总数</th>
        <td><font color="#8bbc21">{{$statusThree}}</font></td>
      </tr>
      <tr>
        <th>已关闭工单总数</th>
        <td><font color="#910000">{{$statusFour}}</font></td>
      </tr>
    </tbody>
  </table>
</div>

</div>
@stop
