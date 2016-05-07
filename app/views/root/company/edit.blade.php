@extends('root.layouts.default')

@section('script')
@parent

@stop

@section('content')
<div class="row">

    <div class="col-lg-12">

        <h1>修改公司信息
            <small>简介</small>
        </h1>

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to('root') }}">首页</a></li>
            <li> <a href="{{ URL::to('root/company') }}">公司管理</a></li>
            <li class="active"> 修改公司信息</li>
        </ol>

        <!-- Notifications -->

        @include('root.notifications')

        <!-- ./ notifications -->
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post">

            <legend>基本信息</legend>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="form-group clearfix {{{ $errors->has('company_name') ? 'has-error' : '' }}}">
                <label for="company_name" class="col-sm-2 control-label">公司名称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Company Name" name="company_name" id="company_name" value="{{{ Input::old('company_name', $com->company_name) }}}" />

                    <span class="help-block">{{{ $errors->first('company_name') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('mobile') ? 'has-error' : '' }}}">
                <label for="mobile" class="col-sm-2 control-label">联系电话</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" value="{{{ Input::old('mobile', $com->mobile) }}}" />

                    <span class="help-block">{{{ $errors->first('mobile') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('address') ? 'has-error' : '' }}}">
                <label for="address" class="col-sm-2 control-label">联系地址</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Address" name="address" id="address" value="{{{ Input::old('address', $com->address) }}}" />

                    <span class="help-block">{{{ $errors->first('address') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('path') ? 'has-error' : '' }}}">
                <label for="path" class="col-sm-2 control-label">访问地址</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Path" name="path" id="path" value="{{{ Input::old('path', $com->path) }}}" />

                    <span class="help-block">{{{ $errors->first('path') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('brief') ? 'has-error' : '' }}}">
                <label for="brief" class="col-sm-2 control-label">公司简介</label>
                <div class="col-sm-10">

                    <textarea class="form-control" placeholder="Brief" name="brief" id="brief" rows="3">{{{ Input::old('brief', $com->brief) }}}</textarea>

                    <span class="help-block">{{{ $errors->first('brief') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">修改</button>
                    <a href="{{ URL::to('root/company') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>
    </div>
</div><!-- /.row -->

@stop