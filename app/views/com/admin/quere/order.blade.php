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
            <li class="active">邮件队列 ( 第 {{ $quere->getCurrentPage() }} 页 / 共 {{ $quere->getTotal() }}  条 )</li>
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
                    <th>队列修改时间 <i class="fa fa-sort"></i></th>
                    <th>队列状态</th>
                    <th>是否审核</th>
                </tr>
                </thead>
                <tbody>

                @if ($quere->count() >= 1)
                @foreach ($quere as $q)
                <tr>
                    <td>{{ $q->from_email }}</td>
                    <td width="80px;">
                        {{ $q->to_email }}
                        @if ($q->cc != '')
                        ,{{$q->cc}}
                        @endif
                    </td>
                    <td>{{ $q->created_at }}</td>
                    @if ($q->updated_at > 0)
                    <td>{{ $q->updated_at }}</td>
                    @else
                    <td>-</td>
                    @endif
                    @if ($q->execute == 0)
                    <td><span class="label label-warning">等待执行</span></td>
                    @elseif ($q->execute == 1 && $q->system == 1)
                    <td><span class="label label-success">执行成功</span></td>
                    @elseif ($q->execute == 1 && $q->system == 2)
                    <td><span class="label label-danger">执行失败</span></td>
                    @else
                    <td><span class="label label-info">-</span></td>
                    @endif
                    <td>
                        @if ($q->order_disabled == 'true')
                        <span class="label label-danger" title="此条消息的工单在废纸篓中，无法发送！">废纸篓</span>
                        @elseif ($q->process == 0)
                        <span class="label label-primary">已审核</span>
                        @else
                        <span class="label label-info">待审核</span>
                        @endif
                    </td>
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
