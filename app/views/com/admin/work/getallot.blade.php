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
<div class="row" style="overflow-y:auto; overflow-x:auto; height:420px;">
    <div class="col-lg-12">
        <form method="GET">
        <table class="table">
            <thead>
            <tr>
                <th>选择</th>
                <th>姓名</th>
                <th>所属组</th>
                <th>权限</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($user as $u)
            <tr>
                <td>
                    @if ($workStatus == '1' && $adminID == $u->user_id)
                    <input type="radio" name="user_id" value="{{$u->user_id}}" />
                    @elseif ($adminID == '0' || $adminID != $u->user_id || $manageoriginal == 'true')
                    <input type="radio" name="user_id" value="{{$u->user_id}}" />
                    @elseif ($adminID == $u->user_id && $manageoriginal == 'false')
                    <input type="radio" name="user_id" value="{{$u->user_id}}" disabled="disabled" />
                    @endif
                </td>
                <td>
                    @if ($adminID == $u->user_id)
                    {{$u->name}} 
                    <font color="red">
                        @if ($workStatus == '1' && $adminID == $u->user_id)
                        (当前用户绑定人)
                        @elseif ($manageoriginal == 'false')
                        (当前工单受理人)
                        @elseif ($manageoriginal == 'true')
                        (原工单受理人)
                        @endif
                    </font>
                    @else
                    {{$u->name}}
                    @endif
                </td>
                <td>
                    <span class="label label-primary">{{$u->group_name}}</span>
                </td>
                <td>
                    @if ($u->grade == '1')
                    <span class="label label-success">组长</span>
                    @else
                    <span class="label label-primary">组员</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <hr >
        <a class="view-allotmanage"> 
            <button type="button" class="btn btn-primary">确认分配</button>
        </a>
    </form>
    </div>
</div>

<script>
$(function() {
    $('.view-allotmanage').click(function() {
        var val = $('input:radio:checked').val();
        if (val == null) {
            alert("请选择需要分配的用户！");
            return false;
        } else {
            window.location.href = "{{ URL::to($com->path.'/admin/work/allotmanage/'.$workID.'/?userID=') }}"+val;
        } 
    });
});
</script>
