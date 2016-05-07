<div class="row" style="overflow-y:auto; overflow-x:auto; height:420px;">
    <div class="col-lg-12">
        <b>当前回复：</b><br />
        @foreach ($reply as $r)
        <div class="alert alert-success">
        {{$r->admin}} ({{$r->position}}) 在 {{$r->created_at}} 说：<br />
        {{$r->reply}}
        </div>
        @endforeach
        <hr />
        @if ($upside != 'false')
        <b>此回复前还有<font color="red">{{$count}}</font>条回复记录未审核：</b><br />
        @foreach ($upside as $u)
        <div class="alert alert-danger">
        回复ID：{{$u->id}}，回复时间：{{$u->created_at}}<br />
        </div>
        @endforeach
        <hr />
        @endif
    </div>
    <form method="GET">
    <div class="col-lg-12">
        <input type="radio" name="check" value="2" /> <span class="label label-success">通过审核</span>&nbsp;&nbsp;
        <input type="radio" name="check" value="1" /> <span class="label label-danger">未通过审核</span>
        <br /><br />
        <textarea class="form-control" name="content" id="content" rows="3" placeholder="审核建议，不填请留空！" ></textarea>
        <br />
        <a class="view-clientmanage"> 
            <button type="button" class="btn btn-primary">确认提交</button>
        </a>
    </div>
    </form>
</div>

<script>
$(function() {
    $('.view-clientmanage').click(function() {
        // 判断审核条件
        var val = $('input:radio:checked').val();
        if (val == null) {
            alert("什么都没选择！");
            return false;
        }

        // 获取审核建议
        var vals = $('#content').val();
        if (vals == '') {
            vals = '0';
        }

        // 换行符编码限制URL过滤
        vals = escape(vals);

        // 提交地址
        window.location.href = "{{ URL::to($com->path.'/admin/check/checkprocess/'.$work_id.'/'.$reply_id.'/'.$admin_id.'/?check="+val
            +"&content=') }}"+vals;
    });
});
</script>