@extends('com.admin.layouts.default')

@section('script')
@parent
<script>
// 工具提示
function toolTip('three') {
    $('#three').tooltip();
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
            <li><a href="{{ URL::to($com->path.'/admin/config') }}">系统设置</a></li>
            <li class="active">邮箱列表</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

         <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive clearfix">
            <table class="table table-hover table-striped  tablesorter">
                <thead>
                <tr>
                    <th>企业邮箱</th>
                    <th>服务器</th>
                    <th>端口号</th>
                    <!-- <th>邮箱密码</th> -->
                    <!-- <th>发送总数</th> -->
                    <th>绑定组别</th>
                    <th>状态</th>
                    <th>创建时间 <i class="fa fa-sort"></i></th>
                    <th>接收时间 <i class="fa fa-sort"></i></th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                @if ($company_email->count() > 0)
                @foreach ($company_email as $c)
                <tr>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->host }}</td>
                    <td>{{ $c->port }}</td>
                    <!-- <td>{{ md5($c->pass) }}</td> -->
                    <!-- <td><font color="red">{{ $c->nums }}</font></td> -->
                    <td>
                        @if ($c->group_id == 0)
                        -
                        @else
                        {{ $c->group_name }}
                        @endif
                    </td>
                    <td>
                        @if ($c->disabled == 'false')
                        <span class="label label-success">已启用</span>
                        @elseif ($c->disabled == 'true')
                        <span class="label label-danger">已禁用</span>
                        @endif
                    </td>
                    <td>{{ $c->created_at }}</td>
                    <td>{{ $c->validation_time }}</td>
                    <td>
                        <a href="{{ URL::to($com->path.'/admin/config/mail/view/'.$c->id) }}" class="btn btn-xs btn-info view-link">配置</a>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">没记录</td>
                </tr>
                @endif

                </tbody>
            </table>
        </div>

        <!--分页-->
        {{ $company_email->links() }}

    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <hr >

        <a href="{{ URL::to($com->path.'/admin/config/mail/create') }}">
            <button class="btn btn-primary">创建邮箱</button>
        </a>

        <a href="{{ URL::to($com->path.'/admin/config/mail/group') }}">
            <button class="btn btn-success">组别绑定</button>
        </a>
    </div>
</div>

<!-- <div class="row">
    <div class="col-lg-12">
        <hr />
        <div class="alert alert-warning fade in">
            请正确设置上面所输入的企业邮箱以及 <strong>IMAP (非SSL)</strong>协议邮箱服务器：<br />
            1、用于邮件自动转发至 <strong>no</strong>，开启邮件生产工单功能；<br />
            2、用于工单邮件队列推送功能，借助 <strong>no</strong>匿名转发实现；<br />
            3、系统默认赠予每个入驻企业 <strong>10000</strong>封的邮件发送额度，请合理分配使用；<br />
            4、每个入驻企业最多只能设立3个企业邮箱。
            1、每个入驻企业最多只能设立3个企业邮箱。
        </div>
    </div>
</div> -->
@stop