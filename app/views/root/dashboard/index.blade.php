@extends('root.layouts.default')

@section('content')
<div class="row">

    <div class="col-lg-12">

        <h1>控制面板
            <small>欢迎</small>
        </h1>

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
                        <i class="fa fa-circle-o fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">公司管理</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to('root/company') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看列表
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
                        <p class="announcement-heading">用户管理</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to('root/user') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            查看列表
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
                        <i class="fa fa-bar-chart-o fa-5x"></i>
                    </div>
                    <div class="col-xs-6 text-right">
                        <p class="announcement-heading">报表统计</p>

                        <p class="announcement-text"></p>
                    </div>
                </div>
            </div>
            <a href="{{ URL::to('root/report') }}">
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
    <div class="col-lg-6">
        <div class="panel panel-success">
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
            <a href="{{ URL::to('root/config') }}">
                <div class="panel-footer announcement-bottom">
                    <div class="row">
                        <div class="col-xs-6">
                            前往设置
                        </div>
                        <div class="col-xs-6 text-right">
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div><!-- /.row -->
@stop
