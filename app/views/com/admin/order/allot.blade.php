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

            @if (count($admin) > 0)
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
                @else
                <tr>
                @endif
                    <td>
                        <input type="radio" name="uid" value="{{$u->id}}" />
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
            @else
            <font color="red">您所在的组别未绑定任何邮箱，无法分配！</font>
            @endif
        </table>
        <!-- <hr class="hr-middle" />
        <a class="view-allot"> 
            <button type="button" class="btn btn-primary">分配</button>
        </a>
    </form> -->
    </div>
</div>

<hr class="hr-middle" />
<button type="button" class="btn btn-primary view-allot">分配</button>
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</form>

<script>
$(function() {
    $('.view-allot').click(function() {
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
        
        window.location.href = "{{ URL::to($com->path.'/admin/order/orderallot/create?mark="+mark+"&admin_id="+val+"&status="+status+"&order_id=') }}"+vals;
    });
});
</script>