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

        <!-- <h1>业务形态
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config') }}">系统设置</a></li>
            <li class="active">业务形态 ( 第 {{ $work_types->getCurrentPage() }} 页 / 共 {{ $work_types->getTotal() }}  条 )</li>
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
                    <th>业务名称 <i class="fa fa-sort"></i></th>
                    <th>创建时间 <i class="fa fa-sort"></i></th>
                    <th width="10%">操作</th>
                </tr>
                </thead>
                <tbody>

                @if ($work_types->count() >= 1)
                @foreach ($work_types as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->work_type_name }}</td>
                    <td>{{ $t->created_at }}</td>
                    <td>
                        <a href="{{ URL::to($com->path.'/admin/config/type/edit/'.$t->id) }}" class="btn btn-xs btn-primary">修改</a>
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

        <a href="{{ URL::to($com->path.'/admin/config/type/create') }}">
            <button class="btn btn-primary">创建业务形态</button>
        </a>
    </div>
</div>
@stop