@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
    .announcement-heading {font-size: 10px;}
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

        <!-- <h1>组别管理
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li class="active">组别管理 ( 第 {{ $group->getCurrentPage() }} 页 / 共 {{ $group->getTotal() }}  条 )</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->
        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    @if ($group->count() >= 1)
    @foreach ($group as $g)
    <div class="col-lg-4">
        <div class="panel
        @if ($g->id % 5 == 0)
        panel-info
        @elseif ($g->id % 5 == 1)
        panel-success
        @elseif ($g->id % 5 == 2)
        panel-warning
        @elseif ($g->id % 5 == 3)
        panel-danger
        @else
        panel-primary
        @endif
        ">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa

                        @if ($g->id % 6 == 0)
                        fa-comments
                        @elseif ($g->id % 6 == 1)
                        fa-comment
                        @elseif ($g->id % 6 == 2)
                        fa-comments-o
                        @elseif ($g->id % 6 == 3)
                        fa-comment-o
                        @elseif ($g->id % 6 == 4)
                        fa-desktop
                        @elseif ($g->id % 6 == 5)
                        fa-laptop
                        @else
                        fa-coffee
                        @endif

                        fa-5x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <p class="announcement-heading">{{ $g->group_name }}</p>
                        <p class="announcement-text">
                            {{ $g->work_type_name }}
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">

                    </div>
                </div>

            </div>

            <a href="{{ URL::to($com->path.'/admin/group/edit/'.$g->id) }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看详细信息
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>
    @endforeach
    @else
    <div class="col-lg-12">
        <div class="alert alert-warning">没组记录，请先创建组！</div>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-lg-12">
        <!--分页-->
        {{ $group->links() }}
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <hr >

        <a href="{{ URL::to($com->path.'/admin/group/create') }}">
            <button class="btn btn-primary">创建组</button>
        </a>
    </div>
</div>
@stop