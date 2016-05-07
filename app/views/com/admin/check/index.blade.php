@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
.label {
    border-radius: 10px;
    color: #FFFFFF;
    display: inline;
    font-size: 75%;
    font-weight: bold;
    line-height: 1;
    padding: 0.2em 0.6em 0.3em;
    text-align: center;
    vertical-align: baseline;
    white-space: nowrap;
}
</style>
@stop

@section('script')
@parent
<script>
// 工单详情
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

// 工单审核
$(function(){
    $('.view-link-check').click(function(){

        $('#viewClientModalCheck').modal();

        $.ajax({
            type: "GET",
            url: $(this).attr('href')
        }).done(function(html_form) {
                $('#viewClientModalbodyCheck').html(html_form);
                $('#viewClientModalCheck').show();
            });

        return false;
    });
});

// 审核建议
$(function(){
    $('.view-link-log').click(function(){

        $('#viewClientModalLog').modal();

        $.ajax({
            type: "GET",
            url: $(this).attr('href')
        }).done(function(html_form) {
                $('#viewClientModalbodyLog').html(html_form);
                $('#viewClientModalLog').show();
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
        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li class="active">工单审核 ( 第 {{ $check->getCurrentPage() }} 页 / 共 {{ $check->getTotal() }}  条 )</li>
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
                    <th>回复ID <i class="fa fa-sort"></i></th>
                    <th>工单号 <i class="fa fa-sort"></i></th>
                    <th>工单级别</th>
                    <th>工单所属组</th>
                    <th>工单发起人</th>
                    <th>回复人员</th>
                    <th>工单创建时间 <i class="fa fa-sort"></i></th>
                    <th>工单回复时间 <i class="fa fa-sort"></i></th>
                    <th>附带附件</th>
                    <th>审核结果</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                @if ($check->count() >= 1)
                @foreach ($check as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>#{{ $c->work_order_id }}</td>
                    <td>
                        @if ($c->work_level == '3')
                        <span class="label label-danger">紧急</span>
                        @elseif ($c->work_level == '2')
                        <span class="label label-warning">中等</span>
                        @else
                        <span class="label label-info">一般</span>
                        @endif
                    </td>
                    <td>{{ $c->group_name }}</td>
                    <td>{{ $c->user }}</td>
                    <td>{{ $c->admin }}</td>
                    <td>{{ $c->w_created_at }}</td>
                    <td>{{ $c->created_at }}</td>
                    <td>
                        @if ($c->enclosure_id > 0)
                        是
                        @else
                        否
                        @endif
                    </td>
                    <td>
                        @if ($c->check == '1' && $c->check_status == '0')
                        <b><font color="#c09853">待审核</font></b>
                        @elseif ($c->check == '1' && $c->check_status == '1')
                        <b><font color="#b94a48">未通过</font></b>
                        @elseif ($c->check == '0' && $c->check_status == '2')
                        <b><font color="#3a87ad">已通过</font></b>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-xs btn-info view-link" 
                        href="{{ URL::to($com->path.'/admin/check/checkdetail/'.$c->work_order_id.'/'.$c->id) }}">
                            查看
                        </a>
                        @if ($level < 4 && $c->check == '1' && $c->check_status == '0')
                        <a class="btn btn-xs btn-primary view-link-check" 
                        href="{{ URL::to($com->path.'/admin/check/checkreply/'.$c->work_order_id.'/'.$c->id) }}">
                            审核
                        </a>
                        @endif
                        @if ($c->check_log != 'false')
                        <a class="btn btn-xs btn-success view-link-log" 
                        href="{{ URL::to($com->path.'/admin/check/checklog/'.$c->work_order_id.'/'.$c->id) }}">
                            建议
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">暂无记录！</td>
                </tr>
                @endif

                </tbody>
            </table>
        </div>

        <!--分页-->
        {{ $check->links() }}

    </div>
</div><!-- /.row -->

<!-- 工单详情 -->
<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">工单详情</h4>
            </div>
            <div class="modal-body" id="viewClientModalbody" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<!-- 工单详情 -->

<!-- 工单审核 -->
<div class="modal fade" id="viewClientModalCheck" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">工单审核</h4>
            </div>
            <div class="modal-body" id="viewClientModalbodyCheck" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<!-- 工单审核 -->

<!-- 审核建议 -->
<div class="modal fade" id="viewClientModalLog" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">审核建议</h4>
            </div>
            <div class="modal-body" id="viewClientModalbodyLog" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<!-- 审核建议 -->
@stop