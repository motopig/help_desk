<style>
.table {
    font-size: 1em;
}
/*#process_content {
    width: 455px;
    height: 150px;
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
}*/
</style>

<form method="get">
@if (isset($reply))
<div class="row" style="overflow-y:auto; overflow-x:auto; height:200px;">
    <div class="col-lg-12">
        <!-- <form method="get"> -->
            <div class="media">
                <span class="pull-left">
                    @if (isset($reply->head_path))
                    <img class="media-object media-border-radius" src="{{{asset($reply->head_path)}}}">
                    @else
                    <img class="media-object media-border-radius" src="{{{asset('assets/img/avatara.png')}}}">
                    @endif
                </span>
                <div class="heading-media">
                <a class="name-body-title" title="{{$reply->user_name}}" href="#">{{$reply->name}}</a> 
                <span class="time">{{$reply->created_at}} #{{$reply->id}}</span>
                </div>
                <span class="body-media">{{$reply->content}}</span>
            </div>
            <!-- <hr class="hr-middle" />
        <div class="col-lg-12">
            <input type="radio" name="check" value="0" /> <span class="label label-success">通过</span>&nbsp;&nbsp;
            <input type="radio" name="check" value="2" /> <span class="label label-danger">不通过</span>&nbsp;&nbsp;
            <input type="radio" name="check" value="3" /> <span class="label label-primary">修改</span>
            <br /><br />
            <textarea class="form-control" id="process_content" rows="3" placeholder="审核内容，不填请留空！" ></textarea>
            <br />
            <a class="view-process-tab"> 
                <button type="button" class="btn btn-primary">确认</button>
            </a>
        </div>
    </form> -->
    </div>
</div>
@endif

审核编号：#{{$id}}
<hr class="hr-middle" />
<div class="col-lg-12">
<input type="radio" name="check" value="0" /> <span class="label label-success">通过</span>&nbsp;&nbsp;
<input type="radio" name="check" value="2" /> <span class="label label-danger">不通过</span>&nbsp;&nbsp;
<input type="radio" name="check" value="3" /> <span class="label label-primary">修改</span>
<br /><br />
<!-- <textarea class="form-control" id="process_content" rows="3" placeholder="审核内容，不填请留空！" ></textarea> -->
<!-- 副文本编辑器 -->
<script id="overrule-text-reply" name="content" type="text/plain"></script>
<br />
<button type="button" class="btn btn-primary view-process-tab">确认</button>
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</div>
</form>

<script>
var editors = new UE.ui.Editor({
    initialFrameHeight:200,
    initialFrameWidth:500,
    autoHeightEnabled:false,
    zIndex:3000,
});
editors.render("overrule-text-reply");

$(function() {
    $('.view-process-tab').click(function() {
        // 判断审核条件
        var val = $('input:radio:checked').val();
        if (val == null) {
            alert("什么都没选择！");
            return false;
        }

        // 获取审核建议
        // var vals = $('#process_content').val();
        var vals = editors.getContent();
        if (val == 3 && vals == '') {
            alert("修改前必须输入审核内容！");
            return false;
        }

        // 换行符编码限制URL过滤
        vals = escape(vals);
        var status = {{$status}};
        var id = {{$id}};

        window.location.href = "{{ URL::to($com->path.'/admin/order/process/create?system="+val+"&remark="+vals+"&status="+status+"&id=') }}"+id;
    });
});
</script>