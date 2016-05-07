@extends('com.admin.layouts.default')

@section('script')
@parent
<script>
    $(function(){

        function check_user(){
            if ($('#check_user').is(':checked')) {
                $('#create_new_user').removeClass('hidden');
                $('#select_new_user').addClass('hidden');
            } else {
                $('#select_new_user').removeClass('hidden');
                $('#create_new_user').addClass('hidden');
            }
        }

        // 初始化
        //
        check_user();

        // 绑定 check 事件
        //
        $('#check_user').bind('click',function(){
            check_user();
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

        <!-- <h1>修改组
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li> <a href="{{ URL::to($com->path.'/admin/group') }}">组别管理</a></li>
            <li class="active">修改组</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">

    @if ($work_types->count() >= 1)
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post">

            <legend>基本信息</legend>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="form-group clearfix {{{ $errors->has('group_name') ? 'has-error' : '' }}}">
                <label for="group_name" class="col-sm-2 control-label">组别名称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Group Name" name="group_name" id="group_name" value="{{{ Input::old('group_name', $group->group_name) }}}" />

                    <span class="help-block">{{{ $errors->first('group_name') }}}</span>
                </div>
            </div>

            <!-- <div class="form-group clearfix {{{ $errors->has('mobile') ? 'has-error' : '' }}}">
                <label for="mobile" class="col-sm-2 control-label">联系电话</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" value="{{{ Input::old('mobile', $group->mobile) }}}" />

                    <span class="help-block">{{{ $errors->first('mobile') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('address') ? 'has-error' : '' }}}">
                <label for="address" class="col-sm-2 control-label">联系地址</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Address" name="address" id="address" value="{{{ Input::old('address', $group->address) }}}" />

                    <span class="help-block">{{{ $errors->first('address') }}}</span>
                </div>
            </div> -->

            <div class="form-group clearfix {{{ $errors->has('work_type_id') ? 'has-error' : '' }}}">
                <label for="work_type_id" class="col-sm-2 control-label">业务形态</label>
                <div class="col-sm-10">

                    <select name="work_type_id" id="work_type_id" class="form-control">
                        @if ($work_types->count() >= 1)
                        @foreach ($work_types as $t)
                        <option value="{{ $t->id }}"
                            @if ($group->work_type_id == $t->id)
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

            <!-- <div class="form-group clearfix">
                <label for="email_id" class="col-sm-2 control-label">绑定邮箱</label>
                <div class="col-sm-10">
                    <select name="email_id" id="email_id" class="form-control">
                    @if ($group_email == 'true')
                    @foreach ($company_email as $ce)
                    @if ($ce->id == $group->email_id)
                    <option value="{{$ce->id}}" selected="selected">{{$ce->email}}</option>
                    @else
                    <option value="{{$ce->id}}">{{$ce->email}}</option>
                    @endif
                    @endforeach
                    <option value="0">请选择...</option>
                    @elseif ($group_email == 'false')
                    <option value="0" selected="selected">请选择...</option>
                    @foreach ($company_email as $ce)
                    <option value="{{$ce->id}}">{{$ce->email}}</option>
                    @endforeach
                    @endif
                    </select>
                </div>
            </div> -->

            <div class="form-group clearfix {{{ $errors->has('brief') ? 'has-error' : '' }}}">
                <label for="brief" class="col-sm-2 control-label">业务简介</label>
                <div class="col-sm-10">

                    <textarea class="form-control" placeholder="Brief" name="brief" id="brief" rows="3">{{{ Input::old('brief', $group->brief) }}}</textarea>

                    <span class="help-block">{{{ $errors->first('brief') }}}</span>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('brief') ? 'has-error' : '' }}}">
                <label for="brief" class="col-sm-2 control-label">工单审核</label>
                <div class="col-sm-10">
                    @if ($group->check == '0')
                    <input type="radio" name="check" value="1" /> <span class="label label-danger">开启</span>
                    <input type="radio" name="check" value="0" checked="checked" /> <span class="label label-info">关闭</span>
                    @elseif ($group->check == '1')
                    <input type="radio" name="check" value="1" checked="checked" /> <span class="label label-danger">开启</span>
                    <input type="radio" name="check" value="0" /> <span class="label label-info">关闭</span>
                    @endif
                </div>
            </div>
            <br />

            <legend>更改组长</legend>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="check_user" id="check_user" value="1"
                                @if (Input::old('check_user'))
                                checked
                                @endif
                                > 选择创建新组长
                        </label>
                    </div>
                </div>
            </div>

            <div id="create_new_user" class="hidden">

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

                <div class="form-group clearfix {{{ $errors->has('mobile_u') ? 'has-error' : '' }}}">
                    <label for="mobile_u" class="col-sm-2 control-label">电话</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" placeholder="Mobile" name="mobile_u" id="mobile_u" value="{{{ Input::old('mobile_u') }}}" />

                        <span class="help-block">{{{ $errors->first('mobile_u') }}}</span>
                    </div>
                </div>

                <div class="form-group clearfix {{{ $errors->has('password') ? 'has-error' : '' }}}">

                    <label for="password" class="col-sm-2 control-label">密码</label>

                    <div class="col-sm-10">
                        <input type="password" class="form-control"  placeholder="Password" name="password" id="password" value="" />

                        <span class="help-block">{{{ $errors->first('password') }}}</span>
                    </div>
                </div>

            </div>

            <div id="select_new_user">
                <div class="form-group clearfix {{{ $errors->has('user_id') ? 'has-error' : '' }}}">
                    <label for="user_id" class="col-sm-2 control-label">选择客服</label>
                    <div class="col-sm-10">

                        <select name="user_id" id="user_id" class="form-control">
                            @if ($user)
                            @foreach ($user as $u)
                            <option value="{{ $u->id }}"
                                @if ($u->id == $now_user->user_id && $now_user->grade == 1)
                            selected
                                @endif
                                >{{ $u->user_name }}</option>
                            @endforeach
                            @else
                            <option value="0">还未创建过客服，请创建新组长选项！</option>
                            @endif
                        </select>

                        <span class="help-block">{{{ $errors->first('user_id') }}}</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="{{ URL::to($com->path.'/admin/group') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>

    @else

    <div class="col-lg-12">
        <div class="alert alert-warning">提示：没有可用业务形态，请先
            <a href="{{ URL::to($com->path.'/admin/config/type/create') }}">创建业务形态</a> ！
        </div>
    </div>

    @endif

</div><!-- /.row -->

@stop