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

        <!-- <h1>业务形态修改
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config') }}">系统设置</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config/types') }}">业务形态</a></li>
            <li class="active">修改业务形态</li>
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

            <div class="form-group clearfix {{{ $errors->has('work_type_name') ? 'has-error' : '' }}}">
                <label for="work_type_name" class="col-sm-2 control-label">业务形态</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Work Type Name" name="work_type_name" id="work_type_name" value="{{ Input::old('work_type_name', $worktype->work_type_name) }}" />

                    <span class="help-block">{{{ $errors->first('work_type_name') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">修改</button>
                    <a href="{{ URL::to($com->path.'/admin/config/types') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>
</div><!-- /.row -->

@stop