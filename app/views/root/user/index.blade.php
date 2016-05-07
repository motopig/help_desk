@extends('root.layouts.default')

@section('script')
@parent
<script>
    $(function(){
        $('.view-link').click(function(){

            $('#viewClientModal').modal();

            $.ajax({
                type: "GET",
                url: $(this).attr('href')
            }).done(function(html_form) {
                    $('#viewClientModalbody').html(html_form);
                    $('#viewClientModal').show();
                });

            return false;
        });

    });
</script>
@stop

@section('content')
<div class="row">

    <div class="col-lg-12">

        <h1>用户管理
            <small>简介</small>
        </h1>

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to('root') }}">首页</a></li>
            <li class="active"> 用户管理 ( 第 {{ $users->getCurrentPage() }} 页 / 共 {{ $users->getTotal() }}  条 )</li>
        </ol>

        <!-- Notifications -->

        @include('root.notifications')

        <!-- ./ notifications -->
    </div>
</div><!-- /.row -->

<!--<div class="row">-->
<!--    <div class="col-sm-3">-->
<!--        <div class="input-group">-->
<!--            <input type="text" class="form-control">-->
<!--            <div class="input-group-btn">-->
<!--                <button type="button" class="btn btn-default" tabindex="-1">搜索</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="row">
    <div class="col-lg-12">

        <hr />

        <div class="table-responsive clearfix">
            <table class="table table-hover table-striped  tablesorter">
                <thead>
                <tr>
                    <th>用户ID <i class="fa fa-sort"></i></th>
                    <th>邮箱 <i class="fa fa-sort"></i></th>
                    <th>姓名 <i class="fa fa-sort"></i></th>
                    <th>联系电话 <i class="fa fa-sort"></i></th>
                    <th width="20%">操作</th>
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
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->user_name }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->mobile }}</td>
                    <td>
                        <a href="{{ URL::to('root/user/view/'.$u->id) }}" class="btn btn-xs btn-info view-link">查看</a>

                        @if ($u->id != Auth::user()->id)
                        <a href="{{ URL::to('root/user/changepassword/'.$u->id) }}" class="btn btn-xs btn-primary">修改密码</a>

                        @if ($u->disabled == 'true')
                        <a href="{{ URL::to('root/user/enable/'.$u->id) }}" class="btn btn-xs btn-danger">启用</a>
                        @else
                        <a href="{{ URL::to('root/user/disabled/'.$u->id) }}" class="btn btn-xs btn-warning" >停用</a>
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

<!-- Modal -->
<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">查看用户信息</h4>
            </div>
            <div class="modal-body" id="viewClientModalbody" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@stop