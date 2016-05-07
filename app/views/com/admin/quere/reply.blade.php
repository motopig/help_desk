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
            <li class="active">用户接收队列 ( 第 {{ $quere->getCurrentPage() }} 页 / 共 {{ $quere->getTotal() }}  条 )</li>
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

                @if ($quere->count() >= 1)
                @foreach ($quere as $q)
                <tr>
                    <td>{{ $q->from_email }}</td>
                    <td>{{ $q->to_email }}</td>
                    
                    <td>{{ $q->created_at }}</td>
                    @if ($q->updated_at > 0)
                    <td>{{ $q->updated_at }}</td>
                    @else
                    <td>-</td>
                    @endif
                    @if ($q->execute == 0 && $q->status == 0)
                    <td><span class="label label-warning">等待执行</span></td>
                    @elseif ($q->execute == 1 && $q->status == 0)
                    <td><span class="label label-danger">执行失败</span></td>
                    @elseif ($q->execute == 1 && $q->status == 1)
                    <td><span class="label label-success">执行成功</span></td>
                    @else
                    <td><span class="label label-success">-</span></td>
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">暂无记录！</td>
                </tr>
                @endif

                </tbody>
            </table>
        </div>

        <!--分页-->
        {{ $quere->links() }}

    </div>
</div><!-- /.row -->
@stop
