<style>
.label {
    /*border-radius: 10px;*/
    color: #FFFFFF;
    display: inline;
    font-size: 75%;
    font-weight: bold;
    line-height: 1;
    /*padding: 0.2em 0.6em 0.3em;*/
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
                <th>组别</th>
                <th>所属人</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($adminUsers as $u)
            <tr>
                <td>
                    @if ($u['group_id'] == $bind_info->group_id)
                    <input type="radio" name="group_id" value="{{$u['group_id']}}" checked="checked" />
                    @else
                    <input type="radio" name="group_id" value="{{$u['group_id']}}" />
                    @endif
                </td>
                <td>
                    <span class="label label-primary">{{$u['group_name']}}</span>
                </td>
                <td>
                    <select name="admin_id[{{$u['group_id']}}]" id="admin_id[{{$u['group_id']}}]" class="form-control" 
                    onchange="admin_sub(this.options[this.options.selectedIndex].value)">
                        <option value="0"></option>
                        @foreach ($u['users'] as $us)
                        @if ($us['yes'] == '1')
                        <option value="{{ $us['user_id'] }}" selected="selected">
                            {{ $us['name'] }}
                            @if ($us['grade'] == '1')
                             (组长)
                            @else
                             (组员)
                            @endif
                        </option>
                        @elseif ($us['yes'] == '0')
                        <option value="{{ $us['user_id'] }}">
                        {{ $us['name'] }}
                        @if ($us['grade'] == '1')
                        (组长)
                        @else
                        (组员)
                        @endif
                        </option>
                        @endif
                        @endforeach
                    </select>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <hr >
        <a class="view-clientmanage"> 
            <button type="button" class="btn btn-primary">确认绑定</button>
        </a>
    </form>
    </div>
</div>

<script>
// 获取当前选中的adminID
var vals;
function admin_sub(vs) {
    vals = vs; 
}

$(function() {
    $('.view-clientmanage').click(function() {
        var val = $('input:radio:checked').val();

        if (vals == undefined) {
            vals = {{$bind_info->admin_id}};
        }
        
        window.location.href = "{{ URL::to($com->path.'/admin/client/bindmanage/'.$bind_info->id.'/?group_id="+val
            +"&admin_id=') }}"+vals;
    });
});
</script>