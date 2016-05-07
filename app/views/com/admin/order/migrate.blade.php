<style>
.table {
    font-size: 1em;
}
.marks {
    font-size: 1em;
}
</style>

<form method="get">
<div class="row" style="overflow-y:auto; overflow-x:auto; height:350px;">
    <div class="col-lg-12">
        <!-- <form method="get"> -->
        <table class="table">
            <div class="marks">
            <input id="marks" type="text" class="form-control" placeholder="工单标记 (默认为空)" value="{{$order->mark}}" />
            </div>
            <hr class="hr-middle" />

            <thead>
            <tr>
                <th>请选择</th>
                <th>用户名称</th>
                <th>用户组别</th>
                <th>用户职称</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($admin as $u)
                @if ($u->grade == '1')
                <tr class="success">
                @elseif ($u->id == $order->reply && $u->group_id == $group->id)
                <tr class="danger">
                @elseif ($bind_user == $u->id)
                <tr class="warning">
                @else
                <tr>
                @endif
                    <td>
                        @if ($u->id == $order->reply && $u->group_id == $group->id)
                        <input type="radio" name="uid" value="{{$u->id}}" checked="checked" />
                        @else
                        <input type="radio" name="uid" value="{{$u->id}}" />
                        @endif
                    </td>
                    <td>
                        <span title="{{$u->user_name}}">{{$u->name}}</span>
                    </td>
                    <td>
                        {{$u->group_name}}
                    </td>
                    <td>
                        @if ($u->grade == '1')
                         组长
                        @else
                         组员
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <!-- <hr class="hr-middle" />
        <a class="view-migrate"> 
            <button type="button" class="btn btn-primary">转移</button>
        </a>
    </form> -->
    </div>
</div>

<hr class="hr-middle" />
<button type="button" class="btn btn-primary view-migrate">转移</button>
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</form>

<script>
$(function() {
    $('.view-migrate').click(function() {
        var val = $('input:radio:checked').val();
        var mark = $('#marks').val();

        // if (mark != '' && mark.length > 12) {
        //     alert('最多只能输入12个字符!');
        //     return false;
        // }
        
        if (val) {
            vals = {{$order->id}};
            status = {{$status}};
        } else {
            alert('请选择用户！');
            return false;
        }
        
        window.location.href = "{{ URL::to($com->path.'/admin/order/ordermigrate/create?mark="+mark+"&admin_id="+val+"&status="+status+"&order_id=') }}"+vals;
    });
});
</script>