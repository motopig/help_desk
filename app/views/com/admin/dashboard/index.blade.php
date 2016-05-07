@extends('com.admin.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>控制面板
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-home"></i> 首页</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <i class="fa fa-tasks fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">工单管理</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <!-- <a href="{{ URL::to($com->path.'/admin/work') }}"> -->
            <!-- binway工单定制 - start -->
            <a href="{{ URL::to($com->path.'/admin/order') }}">
            <!-- binway工单定制 - end -->
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看工单
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <i class="fa fa-male fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">客户管理</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to($com->path.'/admin/client') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看客户
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @if ($level < 5)
    <div class="col-lg-6">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">用户管理</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to($com->path.'/admin/user') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看用户
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endif

    @if ($level < 3)
    <div class="col-lg-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <i class="fa fa-comments fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">组别管理</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to($com->path.'/admin/group') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看组别
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if ($level < 5)
    <div class="col-lg-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <i class="fa fa-bar-chart-o fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">报表统计</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to($com->path.'/admin/report') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看报表
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if ($level < 3)
    <div class="col-lg-6">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-envelope fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <p class="announcement-heading">邮件队列</p>
                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <!-- <a href="{{ URL::to($com->path.'/admin/quere') }}"> -->
            <!-- binway路由控制 - start -->
            <a href="{{ URL::to($com->path.'/admin/quere/order') }}">
            <!-- binway路由控制 - end -->
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            队列管理
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <i class="fa fa-cog fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">系统设置</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to($com->path.'/admin/config') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            前往配置
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if ($level < 5)
    <!-- binway路由控制 - start -->
    <!-- <div class="col-lg-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <i class="fa fa-pencil fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">工单审核</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to($com->path.'/admin/check') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            前往审核
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div> -->
    <!-- binway路由控制 - end -->
    @endif
</div><!-- /.row -->
@stop