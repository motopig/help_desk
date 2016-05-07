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
            <h5><font color="#B94A48">工单当前参与人</font></h5>
            <tr>
                <th>选择</th>
                <th>姓名</th>
                <th>所属组</th>
                <th>权限</th>
            </tr>
            </thead>
            @if ($adminuser == 'false')
            <tbody>
            <tr>
                <td>
                    <font color="red">暂无人员列表！</font>
                </td>
            </tr>
            </tbody>
            @else
            <tbody style="background-color:#FDF7F7;">
            @foreach ($adminuser as $au)
            <tr>
                <td>
                    <input type="checkbox" name="admincheck-{{$au->user_id}}" id="admincheck" value="{{$au->user_id}}" 
                    checked="checked" />
                </td>
                <td>
                    {{$au->name}}
                </td>
                <td>
                    <span class="label label-primary">{{$au->group_name}}</span>
                </td>
                <td>
                    @if ($au->grade == '1')
                    <span class="label label-success">组长</span>
                    @else
                    <span class="label label-primary">组员</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
            @endif
        </table>
        <hr >
        <table class="table">
            <thead>
            <h5><font color="#6A8B82">邀请其他人参与</font></h5>
            <tr>
                <th>选择</th>
                <th>姓名</th>
                <th>所属组</th>
                <th>权限</th>
            </tr>
            </thead>
            <tbody>
            @if ($user == 'false')
            <tr>
                <td>
                    <font color="red">暂无人员列表！</font>
                </td>
            </tr>
            @else
            @foreach ($user as $u)
            <tr>
                <td>
                    <input type="checkbox" id='usercheck' name='usercheck-{{$u->user_id}}' value="{{$u->user_id}}" />
                </td>
                <td>
                    {{$u->name}}
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
            @endif
            </tbody>
        </table>
        <hr >
        <a class="view-allotmanage"> 
            <button type="button" class="btn btn-primary">确认邀请</button>
        </a>
    </form>
    </div>
</div>

<script>
$(function() {
    var aval = new Array(); // 参与人
    var uval = new Array(); // 邀请人
    var auval = new Array(); // 合并参与人和邀请人 

    $('.view-allotmanage').click(function() {
        // 循环获取选中的参与人
        $.each($('input[id="admincheck"]'),function(i,e){
            if($(e).prop('checked')==true){
                aval.push($(e).val()); // push索引到数组中
            }
        });
        if (aval == '') {
            aval = 0;
        }
        
        // 循环获取选中的邀请人
        $.each($('input[id="usercheck"]'),function(i,e){
            if($(e).prop('checked')==true){
                uval.push($(e).val()); // push索引到数组中
            }
        });
        if (uval == '') {
            uval = 0;
        }
        
        auval = aval + ':' + uval; // 合并数组内区分参与人和邀请人
        window.location.href = "{{ URL::to($com->path.'/admin/work/dispatchmanage/'.$workID.'/?adminuser=') }}"+auval;
    });

});
</script>
