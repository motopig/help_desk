<style>
.table {
    font-size: 1em;
}
#remarks_content {
    width: 300px;
    resize:none;
    padding: 6px 12px;
    font-size: 11px;
    line-height: 1.428571429;
    color: #555;
    vertical-align: middle;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    -webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
</style>
<script src="{{{ asset('assets/js/multiple-file-upload/jquery.MultiFile.js') }}}"></script>

<form role="form" method="post" enctype="multipart/form-data" 
action="{{ url($com->path.'/admin/order/overrule/create') }}" onsubmit="return check()">
<input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />
<input type="hidden" name="id" value="{{{ $id }}}" />
<input type="hidden" name="status" value="{{{ $status }}}" />
<input type="hidden" name="system" value="1" />
<input type="hidden" name="old_enclosure" value="{{{ $reply->enclosure }}}" />

驳回编号：#{{$id}}
<hr class="hr-middle" />
<div class="row" style="overflow-y:auto;overflow-x:auto;height:300px;">
    <div class="col-lg-12">
        <div class="form-group clearfix">
            邮件抄送：
            <input type="text" style="width:300px;" placeholder="不抄送请留空" name="cc" id="cc" value="{{$reply->cc}}" />
        </div>

        <div class="form-group clearfix">
            工单备注：
            <textarea name="remark" id="remarks_content" rows="3" placeholder="不备注留空" ></textarea>
        </div>

        @if (isset($reply->oldenclosure))
        <div class="form-group clearfix">
            取消原附件：
            @foreach ($reply->oldenclosure as $k)
                <input type="checkbox" name="oldenclosure[]" value="{{$k['id']}}" /> {{$k['name']}}
            @endforeach
        </div>
        @endif

        <script id="overrule-text-reply" name="content" type="text/plain">
        {{$reply->content}}
        </script>
        <span class="task-button-text">
        <input type="file" id="reply_file" name="file[]" class="multi"/>
        </span>
    </div>
</div>

<hr class="hr-middle" />
<button type="submit" class="btn btn-primary">确认</button>
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</form>

<script>
var editors = new UE.ui.Editor({
    initialFrameHeight:150,
    initialFrameWidth:500,
    autoHeightEnabled:false,
    zIndex:3000,
});
editors.render("overrule-text-reply");

// 表单验证
function check() {
    var cc = $('#cc').val();
    if (cc != '') {
        if ((cc.split('@').length-1) == 0) {
            alert('抄送邮件格式不正确！');
            $('#cc').focus();
            return false;
        } else {
            if ((cc.split('@').length-1) > 1 && (cc.split(',').length-1) == 0) {
                alert('抄送邮件格式不正确！');
                $('#cc').focus();
                return false;
            }
        }
    }

    var vals = editors.getContent();
    if (vals == '') {
        alert('请输入邮件内容!');
        return false;
    }

    return true;
}
</script>