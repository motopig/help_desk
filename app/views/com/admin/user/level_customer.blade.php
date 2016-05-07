@extends('com.admin.layouts.default')

@section('script')
@parent
<script>
    $(function(){
        $('.view-link').click(function(){

            $('#viewCustomerModal').modal();

            $.ajax({
                type: "GET",
                url: $(this).attr('href')
            }).done(function(html_form) {
                    $('#viewCustomerModalbody').html(html_form);
                    $('#viewCustomerModal').show();
                });

            return false;
        });
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

        <!-- <h1>客服
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/user') }}">客服管理</a></li>
            <li class="active">客服 ( 第 {{ $users->getCurrentPage() }} 页 / 共 {{ $users->getTotal() }}  条 )</li>
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
                    <th>邮箱 <i class="fa fa-sort"></i></th>
                    <th>姓名 <i class="fa fa-sort"></i></th>
                    <th>职位 <i class="fa fa-sort"></i></th>
                    <th>电话 <i class="fa fa-sort"></i></th>
                    <th>创建时间 <i class="fa fa-sort"></i></th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody>

                @if ($users->count() >= 1)
                @foreach ($users as $u)
                <tr

                @if ($u->disabled == 'true')
                class="danger"
                @else
                    @if ($u->grade == 1)
                    class="success"
                    @endif
                @endif
                    >
                    <td>{{ $u->user_name }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->position }}</td>
                    <td>{{ $u->mobile }}</td>
                    <td>{{ $u->created_at }}</td>
                    <td>
                        <a href="{{ URL::to($com->path.'/admin/user/level/customer/view/'.$u->id) }}" class="btn btn-xs btn-info view-link">查看</a>

                        @if ($level < 3)
                        <a href="{{ URL::to($com->path.'/admin/user/level/customer/edit/'.$u->id) }}" class="btn btn-xs btn-primary">修改</a>

                        @if ($u->disabled == 'true')
                        <a href="{{ URL::to($com->path.'/admin/user/level/customer/enable/'.$u->id) }}" class="btn btn-xs btn-danger">启用</a>
                        @else
                        <a href="{{ URL::to($com->path.'/admin/user/level/customer/disabled/'.$u->id) }}" class="btn btn-xs btn-warning" >停用</a>
                        @endif
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

@if ($level < 4)
<div class="row">
    <div class="col-lg-12">

        <hr >

        <a href="{{ URL::to($com->path.'/admin/user/level/customer/create') }}">
            <button class="btn btn-primary">创建客服</button>
        </a>
    </div>
</div><!-- /.row -->
@endif

<!-- Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewCustomerModalLabel">查看客服信息</h4>
            </div>
            <div class="modal-body" id="viewCustomerModalbody" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop