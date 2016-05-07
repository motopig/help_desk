<style>
.table {
    font-size: 1em;
}
</style>

<form method="get">
<div class="row" style="overflow-y:auto; overflow-x:auto; height:350px;">
    <div class="col-lg-12">
        <!-- <form method="get"> -->
        <input type="hidden" id="ids" value="{{$ids}}" />
        <table class="table">
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
        </table>
        <!-- <hr class="hr-middle" />
        <a class="view-migrate-all"> 
            <button type="button" class="btn btn-primary">批量转移</button>
        </a>
    </form> -->
    </div>
</div>

<hr class="hr-middle" />
<button type="button" class="btn btn-primary view-migrate-all">批量转移</button>
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</form>

<script>
$(function() {
    $('.view-migrate-all').click(function() {
        var val = $('input:radio:checked').val();

        if (val) {
            vals = $('#ids').val();
            status = {{$status}};
        } else {
            alert('请选择用户！');
            return false;
        }
        
        window.location.href = "{{ URL::to($com->path.'/admin/order/ordermigrateall/create?admin_id="+val+"&status="+status+"&ids=') }}"+vals;
    });
});
</script>