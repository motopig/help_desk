@extends('root.layouts.default')

@section('script')
@parent

@stop

@section('content')
<div class="row">

    <div class="col-lg-12">

        <h1>系统设置
            <small>简介</small>
        </h1>

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to('root') }}">首页</a></li>
            <li class="active"> 系统设置</li>
        </ol>

        <!-- Notifications -->

        @include('root.notifications')

        <!-- ./ notifications -->
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">


    </div>
</div><!-- /.row -->

@stop