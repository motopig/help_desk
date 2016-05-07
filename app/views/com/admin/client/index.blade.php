@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
a {
    blr:expression(this.onFocus=this.blur())
}

a:hover{text-decoration:none;}

.btnimport {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.428571429;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    -o-user-select: none;
    user-select: none;
}

.filter-client {
    padding-left: 17px;
    color: #999;
    font-size: 13px;
}
</style>
@stop

@section('script')
@parent
<script>
$(function(){
    // 分页配置默认选中
    if ({{$page}} == 20) {
        $('.op20').attr("selected",true);
    } else if ({{$page}} == 50) {
        $('.op50').attr("selected",true);
    } else if ({{$page}} == 100) {
        $('.op100').attr("selected",true);
    } else if ({{$page}} == 500) {
        $('.op500').attr("selected",true);
    }

    // 分页配置
    $('.osp').change(function() {
        var page = $(this).children('option:selected').val();
        $.ajax({ // 一个Ajax过程
            type: "post", // 以post方式与后台沟通
            url: "<?php echo URL::to($com->path.'/admin/client/page'); ?>", // 提交地址
            dataType: "json", // 从后台返回的值以JSON方式解释
            data: {page: page, csrf_token:'{{{ Session::getToken() }}}' }, // 发给后台的数据项
            // 调用ajax返回成功配置新内容
            success: function(json) {
                window.location.href = "{{ URL::to($com->path.'/admin/client') }}";
            }
        });     
    });

    $('.view-link-show').click(function(){

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

    // 绑定客户
    $('.view-link-bind').click(function(){

        $('#viewClientModalBind').modal();

        $.ajax({
            type: "GET",
            url: $(this).attr('href')
        }).done(function(html_form) {
                $('#viewClientModalbodybind').html(html_form);
                $('#viewClientModalbind').show();
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

        <!-- <h1>客户管理
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li class="active">客户管理 ( 第 {{ $users->getCurrentPage() }} 页 / 共 {{ $users->getTotal() }}  条 )</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <!-- 客户搜索 -->
        <div class="row">
            <div class="col-lg-3">
                每页显示：
                <select name="page" class="osp">
                    <option class="op20" value="20">20</option>
                    <option class="op50" value="50">50</option>
                    <option class="op100" value="100">100</option>
                    <option class="op500" value="500">500</option>
                </select>
                @if (isset($count))
                    <span class="filter-client">
                       共检索到 <font color="red">{{$users->getTotal()}}</font> 条 
                       <a href="{{ URL::to($com->path.'/admin/client') }}"><span class="glyphicon glyphicon-remove"></span></a>
                    </span>
                @endif

                &nbsp;&nbsp;
                <a href="{{ URL::to($com->path.'/admin/client/groupsending') }}">
                    <button class="btn btn-xs btn-primary">
                        <span class="glyphicon glyphicon-envelope" title="群发邮件"></span>&nbsp;
                        群发邮件
                    </button>
                </a>
            </div>

            <div class="col-lg-3" style="float:right;">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" title="点击搜索" class="btn btn-default" tabindex="-1" onclick="searchFilter()" 
                        style="height:30px;background-color:#428bca;opacity:1.0;color:#fff;line-height:3px;font-size:10px;">
                        <span class="search-like" id="search-like-text">邮箱</span> <span class="glyphicon glyphicon-search"></span>
                        </button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height:30px;" tabindex="-1">
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                        <li><a href="javascript:void(0)" onclick="searchSelect(1)"><font size="1">邮箱</font></a></li>
                        <li><a href="javascript:void(0)" onclick="searchSelect(2)"><font size="1">姓名</font></a></li>
                        </ul>
                    </div>
                    <input type="text" id="search-content-data" class="form-control-search" placeholder="搜索" style="height:30px;width:130px;font-size:10px;">
                    <input type="hidden" id="search-like-data" value="1" />
                </div>
            </div>
        </div>
        <!-- 客户搜索 -->

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
                    <th>国家 <i class="fa fa-sort"></i></th>
                    <!-- <th>电话 <i class="fa fa-sort"></i></th>
                    <th>公司 <i class="fa fa-sort"></i></th>
                    <th>业务形态 <i class="fa fa-sort"></i></th>
                    <th>创建时间 <i class="fa fa-sort"></i></th> -->
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
                    <td>{{ $u->user_name }}</td>
                    <td>{{ $u->name }}</td>
                    <td>
                        @if ($u->country == '')
                        -
                        @else
                        {{ $u->country }}
                        @endif
                    </td>
                    <!-- <td>{{ $u->mobile }}</td>
                    <td>{{ $u->firm }}</td>
                    <td>{{ $u->work_type_name }}</td>
                    <td>{{ $u->created_at }}</td> -->
                    <td>
                        <a href="{{ URL::to($com->path.'/admin/client/view/'.$u->id) }}" class="btn btn-xs btn-info view-link-show">查看</a>

                        <a href="{{ URL::to($com->path.'/admin/client/changepassword/'.$u->id) }}" class="btn btn-xs btn-primary">修改</a>
                        @if ($level < 4)

                        <!-- 绑定客户 -->
                        <a href="{{ URL::to($com->path.'/admin/client/bind/'.$u->id) }}" class="btn btn-xs btn-success view-link-bind">绑定</a>

                        @if ($u->disabled == 'true')
                        <a href="{{ URL::to($com->path.'/admin/client/enable/'.$u->id) }}" class="btn btn-xs btn-danger">启用</a>
                        @else
                        <a href="{{ URL::to($com->path.'/admin/client/disabled/'.$u->id) }}" class="btn btn-xs btn-warning" >停用</a>
                        @endif
                        @endif

                        <a href="{{ URL::to($com->path.'/admin/client/email/'.$u->id) }}" class="btn btn-xs btn-primary">
                            <span class="glyphicon glyphicon-envelope" title="发送邮件"></span>
                        </a>
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
        @if (isset($count))
            {{ $users->appends(array('search_content' => $search_content, 'search_type' => $search_type, 'count' => $count))->links(); }}
        @else
            {{ $users->links(); }}
        @endif
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <hr >

        <a href="{{ URL::to($com->path.'/admin/client/create') }}">
            <button class="btn btn-primary">创建客户</button>
        </a>

        <!-- <a href="{{ URL::to($com->path.'/admin/client/importclient') }}">
            <button class="btn btn-success">导入客户</button>
        </a> -->
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">查看客户信息</h4>
            </div>
            <div class="modal-body" id="viewClientModalbody" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- 绑定客户 -->
<div class="modal fade" id="viewClientModalBind" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">客户绑定信息</h4>
            </div>
            <div class="modal-body" id="viewClientModalbodybind" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// 客户搜索条件筛选
function searchSelect(type) {
    if (type == 1) {
        $('#search-like-text').html('邮箱');
        $('#search-like-data').val(1);
    } else if (type == 2) {
        $('#search-like-text').html('姓名');
        $('#search-like-data').val(2);
    }
}

// 客户搜索处理
function searchFilter() {
    var data = $('#search-content-data').val();
    if (data == '') {
        alert('请先输入搜索内容！');
        return false;
    } else {
        var search_content = data;
        var search_type = $('#search-like-data').val();

        window.location.href = "{{ URL::to($com->path.'/admin/client?search_content="+search_content+"&search_type=') }}"+search_type;
    }
}
</script>
@stop