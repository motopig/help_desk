@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
    /*.announcement-heading {font-size: 35px;}*/
</style>
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
            <li><a href="{{ URL::to($com->path.'/admin/config') }}">系统设置</a></li>
            <li class="active">工单级别 ( 第 {{ $order_type->getCurrentPage() }} 页 / 共 {{ $order_type->getTotal() }}  条 )</li>
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
                    <th>ID <i class="fa fa-sort"></i></th>
                    <th>级别名称 <i class="fa fa-sort"></i></th>
                    <th>创建时间 <i class="fa fa-sort"></i></th>
                    <th width="10%">操作</th>
                </tr>
                </thead>
                <tbody>

                @if ($order_type->count() >= 1)
                @foreach ($order_type as $t)
                    @if ($t->disabled == 'true')
                    <tr class="danger">
                    @else
                    <tr>
                    @endif
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->created_at }}</td>
                    <td>
                        <a href="{{ URL::to($com->path.'/admin/config/orderLevel/edit/'.$t->id) }}" class="btn btn-xs btn-success">编辑</a>
                        @if ($t->disabled == 'true')
                        <a href="{{ URL::to($com->path.'/admin/config/orderLevel/update/'.$t->id.'/?disabled=false') }}" class="btn btn-xs btn-danger">启用</a>
                        @else
                        <a href="{{ URL::to($com->path.'/admin/config/orderLevel/update/'.$t->id.'/?disabled=true') }}" class="btn btn-xs btn-warning" >停用</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">没记录</td>
                </tr>
                @endif

                </tbody>
            </table>
        </div>

    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <hr >

        <a href="{{ URL::to($com->path.'/admin/config/orderLevel/create') }}">
            <button class="btn btn-primary">创建工单级别</button>
        </a>
    </div>
</div>
@stop