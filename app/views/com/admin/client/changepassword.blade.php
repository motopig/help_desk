@extends('com.admin.layouts.default')

@section('script')
@parent
<script>
    $(function(){

        function check_firm(){
            if ($('#check_firm').is(':checked')) {
                $('#select_new_firm').removeClass('hidden');
                $('#create_new_firm').addClass('hidden');
            } else {
                $('#create_new_firm').removeClass('hidden');
                $('#select_new_firm').addClass('hidden');
            }
        }

        // 初始化
        //
        check_firm();

        // 绑定 check 事件
        //
        $('#check_firm').bind('click',function(){
            check_firm();
        })
    });
</script>
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
            <li><a href="{{ URL::to($com->path.'/admin/client') }}">客户管理</a></li>
            <li class="active">修改客户密码</li>
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

            <legend>客户：{{ $user_info->user_name }}</legend>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />
            <input type="hidden" name="suid" id="suid" value="{{{ $site_user->id }}}" />

            <div class="form-group clearfix {{{ $errors->has('work_type_id') ? 'has-error' : '' }}}">
                <label for="work_type_id" class="col-sm-2 control-label">业务形态</label>
                <div class="col-sm-10">

                    <select name="work_type_id" id="work_type_id" class="form-control">
                        @if ($work_types->count() >= 1)
                        @foreach ($work_types as $t)
                        <option value="{{ $t->id }}"
                            @if ($site_user->work_type_id == $t->id)
                                selected
                            @endif

                            >{{ $t->work_type_name }}</option>
                        @endforeach
                        @else
                        <option value="0">请先创建业务形态</option>
                        @endif
                    </select>

                    <span class="help-block">{{{ $errors->first('work_type_id') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('name') ? 'has-error' : '' }}}">
                <label for="name" class="col-sm-2 control-label">姓名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Name" name="name" id="name" value="{{{ Input::old('name', $user_info->name) }}}" />

                    <span class="help-block">{{{ $errors->first('name') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('mobile') ? 'has-error' : '' }}}">
                <label for="mobile" class="col-sm-2 control-label">电话</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" value="{{{ Input::old('mobile', $user_info->mobile) }}}" />

                    <span class="help-block">{{{ $errors->first('mobile') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="check_firm" id="check_firm" value="1"
                            @if (Input::old('check_firm'))
                            checked
                            @endif
                            > 选择已有公司
                        </label>
                    </div>
                </div>
            </div>

            <div id="create_new_firm" class="clearfix">

            <div class="form-group clearfix {{{ $errors->has('firm') ? 'has-error' : '' }}}">
                <label for="firm" class="col-sm-2 control-label">公司</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Firm" name="firm" id="firm" value="{{{ Input::old('firm', $site_user->firm) }}}" />

                    <span class="help-block">{{{ $errors->first('firm') }}}</span>
                </div>
            </div>

            </div>

            <div id="select_new_firm" class="hidden clearfix">
                <div class="form-group clearfix {{{ $errors->has('firm_s') ? 'has-error' : '' }}}">
                    <label for="firm_s" class="col-sm-2 control-label">选择公司</label>
                    <div class="col-sm-10">

                        <select name="firm_s" id="firm_s" class="form-control">

                            @if (count($firm) >= 1)
                            @foreach ($firm as $f)
                            <option value="{{ $f }}" >{{ $f }}</option>
                            @endforeach
                            @else
                            <option value="0">还未有公司数据，请取消选择已有公司选项！</option>
                            @endif

                        </select>

                        <span class="help-block">{{{ $errors->first('firm_s') }}}</span>
                    </div>
                </div>
            </div>

            <legend>修改密码 <small>不修改请为空</small></legend>

            <div class="form-group clearfix {{{ $errors->has('password') ? 'has-error' : '' }}}">

                <label for="password" class="col-sm-3 control-label">密码</label>

                <div class="col-sm-9">
                    <input type="password" class="form-control"  placeholder="Password" name="password" id="password" value="" />

                    <span class="help-block">{{{ $errors->first('password') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('password_confirmation') ? 'has-error' : '' }}}">

                <label for="password_confirmation" class="col-sm-3 control-label">确认密码</label>

                <div class="col-sm-9">
                    <input type="password" class="form-control"  placeholder="Password Confirmation" name="password_confirmation" id="password_confirmation" value="" />

                    <span class="help-block">{{{ $errors->first('password_confirmation') }}}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary">修改</button>
                    <a href="{{ URL::to($com->path.'/admin/client') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>
    </div>
</div><!-- /.row -->
@stop

