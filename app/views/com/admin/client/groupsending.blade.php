@extends('com.admin.layouts.default')

<link href="{{{ asset('assets/css/order.css') }}}" rel="stylesheet">
<script charset="utf-8" src="{{{ asset('assets/js/order.js') }}}"></script>

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>客户管理
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/client') }}">客户管理</a></li>
            <li class="active">群发邮件</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post" enctype="multipart/form-data" onsubmit="return check()">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <div class="form-group clearfix {{{ $errors->has('title') ? 'has-error' : '' }}}">
                <label for="title" class="col-sm-2 control-label">邮件标题</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" value="" />
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="email_id" class="col-sm-2 control-label">发件箱</label>
                <div class="col-sm-10">
                    <select name="email_id" id="email_id" class="form-control">
                        @foreach ($company_email as $ce)
                            <option value="{{$ce->id}}">{{$ce->email}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="bcc" class="col-sm-2 control-label">收件人</label>
                <div class="col-sm-10">
                    <textarea class="task-form-control" id="bcc" name="bcc" rows="3" placeholder="每次最多50个邮箱"></textarea>
                </div>
            </div>

            <div class="form-group clearfix {{{ $errors->has('content') ? 'has-error' : '' }}}">
                <label for="content" class="col-sm-2 control-label">邮件内容</label>
                <div class="col-sm-10">
                    <script id="email-text" name="content" type="text/plain"></script>
                    <span class="task-button-text">
                    <input type="file" id="reply_file" name="file[]" class="multi"/>
                    </span>
                </div>
            </div>
            <br />

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">确认</button>
                    <a href="{{ URL::to($com->path.'/admin/client') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>
</div><!-- /.row -->

<script type="text/javascript">
var editors = new UE.ui.Editor({
    initialFrameHeight:150,
    initialFrameWidth:415,
});
editors.render("email-text");

// 表单验证
function check() {
    if ($('#title').val() == '') {
        alert('请输入标题!');
        $('#title').focus();
        return false;
    }

    if ($('#email_id').val() == null) {
        alert('请选择发件箱!');
        return false;
    }

    var bcc = $('#bcc').val();
    if (bcc == '') {
        alert('请输入收件人!');
        $('#bcc').focus();
        return false;
    } else {
        if ((bcc.split('@').length-1) == 0) {
            alert('收件人邮箱格式不正确！');
            $('#bcc').focus();
            return false;
        } else {
            if ((bcc.split('@').length-1) > 1 && (bcc.split(',').length-1) == 0) {
                alert('收件人邮箱格式不正确！');
                $('#bcc').focus();
                return false;
            }
        }

        if ((bcc.split(',').length-1) > 49) {
            alert('每次最多50个邮箱！');
            $('#bcc').focus();
            return false;
        }
    }

    var vals = editors.getContent();
    if (vals == '') {
        alert('请输入邮件内容!');
        return false;
    }

    return true;
}
</script>
@stop