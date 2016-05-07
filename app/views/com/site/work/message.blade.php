<style>
.pull-right {
    float: right;
}
.alert-infos {
    background-color: #F4F8FA;
    border-color: #F4F8FA;
    color: #3A87AD;
}
.alert-dangers {
    background-color: #FDF7F7;
    border-color: #FDF7F7;
    color: #B94A48;
}
.alert-warnings {
    background-color: #FAF8F0;
    border-color: #FAF8F0;
    color: #C09853;
}
.alerts {
    border: 1px solid rgba(0, 0, 0, 0);
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 15px;
}
a {
    blr:expression(this.onFocus=this.blur())
}
/*userlogo*/
.avatar {
    border-radius: 50%;
    height: 26px;
    width: 26px;
    display: inline-block;
    line-height: 1;
}
/*搜索框*/
#txt-search {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #A7C0B9;
    border-radius: 16px;
    font-size: 12px;
    /*line-height: 12px;*/
    padding: 4px 10px 3px 28px;
    transition: width 150ms ease-in-out 0s;
    float: right;
    margin-top: 7px;
    display: block;
}
.link-search {
    float: right;
    right: -22px;
    top: 12px;
}
.keyword ::-moz-placeholder {
    color: #999999;
    outline: 0;
}
.keyword-focus {
    outline: 0;
}
/*工单消息弹窗自定义样式*/
.modal-content {
    position: relative;
    background-color: #fff;
    border: 1px solid #999;
    border: 1px solid rgba(0,0,0,0.2);
    border-radius: 6px;
    outline: 0;
    -webkit-box-shadow: 0 3px 9px rgba(0,0,0,0.5);
    box-shadow: 0 3px 9px rgba(0,0,0,0.5);
    background-clip: padding-box;
    width: 700px;
    height: 500px;
}
</style>

<!-- 表格排序JS － no -->
<script src="{{{ asset('assets/js/tablesorter/tables.js') }}}"></script>

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive clearfix">
            <!-- 设定溢出滚动条 - no -->
            <div style="overflow-y:auto;height:300px;">
            <table class="table table-hover table-striped tablesorter scrollable">
                    <thead>
                    <tr>
                        <th>工单号 <i class="fa fa-sort"></i></th>
                        <th>工单状态 <i class="fa fa-sort"></i></th>
                        <th>最新回复 <i class="fa fa-sort"></i></th>
                        <th>发起人</th>
                        <th>受理人</th>
                        <th>处理人</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if ($message)
                    @foreach ($message as $m)
                    <tr>
                    <td>
                        <a href="{{ URL::to($com->path.'/site/work/workdetail/'.$m->work_order_id.'?message_id='.$m->id) }}">
                            #{{$m->work_order_id}}</a>
                    </td>
                    <td>
                        @if ($m->work_status == '4')
                        <font color="#d9534f">已关闭</font>
                        @elseif ($m->work_status == '3')
                        <font color="#3c763d">已处理</font>
                        @elseif ($m->work_status == '2')
                        <font color="#31708f">处理中</font>
                        @elseif ($m->work_status == '1')
                        <font color="#f0ad4e">待受理</font>
                        @endif
                    </td>
                    <td>{{$m->updated_at}}</td>
                    <td>
                        {{$user_name}}
                    </td>
                    <td>{{$m->admin_name}}</td>
                    <td>{{$m->reply_admin_name}}</td>
                    <td>
                        <a class="btn btn-xs btn-info" 
                        href="{{ URL::to($com->path.'/site/work/workdetail/'.$m->work_order_id.'?message_id='.$m->id) }}">查看</a>
                    </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                    <td>
                    <h4 class="panel-title">暂无工单消息通知!</h4>
                    </td>
                    </tr>
                    @endif
                    </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
