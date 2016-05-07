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
            <li><a href="{{ URL::to($com->path.'/admin/config/orderLevel') }}">工单级别</a></li>
            <li class="active">编辑工单级别</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />
            <input type="hidden" name="level_id" value="{{{ $order_level->id }}}" />

            <div class="form-group clearfix {{{ $errors->has('name') ? 'has-error' : '' }}}">
                <label for="name" class="col-sm-2 control-label">级别名称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Order Level Name" name="name" id="name" value="{{ Input::old('name', $order_level->name) }}" />

                    <span class="help-block">{{{ $errors->first('name') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">保存</button>
                    <a href="{{ URL::to($com->path.'/admin/config/orderLevel') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>
</div><!-- /.row -->

@stop