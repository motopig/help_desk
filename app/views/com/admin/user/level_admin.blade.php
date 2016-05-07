@extends('com.admin.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>公司管理员
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/user') }}">客服管理</a></li>
            <li class="active">公司管理员 ( 第 {{ $users->getCurrentPage() }} 页 / 共 {{ $users->getTotal() }}  条 )</li>
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
            <table class="table table-hover table-striped tablesorter">
                <thead>
                <tr>
                    <th>邮箱 <i class="fa fa-sort"></i></th>
                    <th>姓名 <i class="fa fa-sort"></i></th>
                    <th>职位 <i class="fa fa-sort"></i></th>
                    <th>电话 <i class="fa fa-sort"></i></th>
                    <th>创建时间 <i class="fa fa-sort"></i></th>
                    <th width="10%">操作</th>
                </tr>
                </thead>
                <tbody>

                @if ($users->count() >= 1)
                @foreach ($users as $u)
                <tr
                @if ($u->disabled == 'true')
                class="danger"
                @endif
                    >
                    <td>{{ $u->user_name }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->position }}</td>
                    <td>{{ $u->mobile }}</td>
                    <td>{{ $u->created_at }}</td>
                    <td>
                        <a href="{{ URL::to($com->path.'/admin/user/level/admin/edit/'.$u->id) }}" class="btn btn-xs btn-primary">修改</a>

                        @if ($u->disabled == 'true')
                        <a href="{{ URL::to($com->path.'/admin/user/level/admin/enable/'.$u->id) }}" class="btn btn-xs btn-danger">启用</a>
                        @else
                        <a href="{{ URL::to($com->path.'/admin/user/level/admin/disabled/'.$u->id) }}" class="btn btn-xs btn-warning" >停用</a>
                        @endif
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
        {{ $users->links() }}
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <hr >

        <a href="{{ URL::to($com->path.'/admin/user/level/admin/create') }}">
            <button class="btn btn-primary">创建公司管理员</button>
        </a>
    </div>
</div><!-- /.row -->
@stop