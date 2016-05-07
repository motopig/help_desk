@extends('com.admin.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>创建客服
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/user') }}">客服管理</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/user/level/customer') }}">客服</a></li>
            <li class="active">创建客服</li>
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

            <div class="form-group clearfix {{{ $errors->has('group_id') ? 'has-error' : '' }}}">
                <label for="group_id" class="col-sm-2 control-label">所属组</label>
                <div class="col-sm-10">
                    @if ($group->count() >= 1)
                    @foreach ($group as $g)
                    <label class="checkbox-inline">
                        <input type="checkbox" name="group_id[]" id="group_id_{{ $g->id }}" value="{{ $g->id }}"
                        @if (in_array($g->id, (array)Input::old('group_id')))
                        checked
                        @endif
                        > {{ $g->group_name }}
                    </label>
                    @endforeach
                    @else
                    <input type="hidden" id="group_id" value="" >

                    <div class="alert alert-warning">
                        提示：没有可用组，请先 <a href="{{ URL::to($com->path.'/admin/group/create') }}">创建组</a> ！
                    </div>
                    @endif

                    <span class="help-block">{{{ $errors->first('group_id') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('email') ? 'has-error' : '' }}}">
                <label for="email" class="col-sm-2 control-label">邮箱</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" placeholder="Email" name="email" id="email" value="{{{ Input::old('email') }}}" />

                    <span class="help-block">{{{ $errors->first('email') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('name') ? 'has-error' : '' }}}">
                <label for="name" class="col-sm-2 control-label">姓名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Name" name="name" id="name" value="{{{ Input::old('name') }}}" />

                    <span class="help-block">{{{ $errors->first('name') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('position') ? 'has-error' : '' }}}">
                <label for="position" class="col-sm-2 control-label">职位</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Position" name="position" id="position" value="{{{ Input::old('position') }}}" />

                    <span class="help-block">{{{ $errors->first('position') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('mobile') ? 'has-error' : '' }}}">
                <label for="mobile" class="col-sm-2 control-label">电话</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" value="{{{ Input::old('mobile') }}}" />

                    <span class="help-block">{{{ $errors->first('mobile') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('password') ? 'has-error' : '' }}}">

                <label for="password" class="col-sm-2 control-label">密码</label>

                <div class="col-sm-10">
                    <input type="password" class="form-control"  placeholder="Password" name="password" id="password" value="" />

                    <span class="help-block">{{{ $errors->first('password') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">创建</button>
                    <a href="{{ URL::to($com->path.'/admin/user/level/customer') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>
</div><!-- /.row -->
@stop