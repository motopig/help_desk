@extends('com.admin.layouts.default')

@section('script')
@parent
<script>
function check() {
    // 获取导入文件名称
    var val = $('input#file').val();
    var vals = 'true'; // 提交开关

    // 验证是否导入csv文件
    if (val == '') {
        vals = 'false';
        alert("请选择需导入的csv文件！");
        return false;
    }

    // 验证文件是否为csv文件
    var suffix = val.substr(val.indexOf("."));
    if (suffix != '.csv') {
        vals = 'false';
        alert("导入的文件必须为csv文件！");
        return false;
    }

    if (vals != 'false') {
        return true;
    } else {
        return false;
    }
}
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
            <li class="active">导入客户</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-8" role="form" method="post" onSubmit="return check()" enctype="multipart/form-data">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="col-lg-12">
            <div class="alert alert-warning">
            <b>请按照以下顺序编写CSV文件数据：</b><br />
            <span title="平台中已录入的业务形态">业务形态</span>，
            <span title="平台中已录入的公司组别">所属组别</span>，
            <span title="Email地址">用户名(邮箱)</span>，
            <span title="大于等于2位">客户姓名</span>，
            <span title="大于等于8位">客户电话</span>，
            <span title="大于等于2位">公司名称</span>，
            <span title="大于等于6位">客户密码</span>
            </div>
            <input type="file" id="file" name="csv" />

            <hr />
            <button type="submit" class="btn btn-primary">导入</button>
            <a href="{{ URL::to($com->path.'/admin/client') }}">
            <button type="button" class="btn btn-default">取消</button>
            </a>
            </div>
        </form>

    </div>
</div><!-- /.row -->
@stop