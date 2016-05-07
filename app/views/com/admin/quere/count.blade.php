@extends('com.admin.layouts.default')

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
            <li><a href="{{ URL::to($com->path.'/admin/quere') }}">队列管理</a></li>
            <li class="active">用户统计队列</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

         <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive clearfix">
            <table class="table table-hover table-striped  tablesorter">
                <thead>
                <tr>
                    <th>发件人邮箱</th>
                    <th>收件人邮箱</th>
                    <th>队列生成时间 <i class="fa fa-sort"></i></th>
                    <th>队列完成时间 <i class="fa fa-sort"></i></th>
                    <th>队列状态</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="3">暂无记录！</td>
                </tr>

                </tbody>
            </table>
        </div>

    </div>
</div><!-- /.row -->
@stop
