<style>
.table {
    font-size: 1em;
}
#remarks_content {
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
}
</style>

<form method="get">
@if (isset($content))
<div class="row" style="overflow-y:auto; overflow-x:auto; height:200px;">
    <div class="col-lg-12">
        <!-- <form method="get"> -->
            <div class="media">
                <span class="pull-left">
                    @if (isset($content->head_path))
                    <img class="media-object media-border-radius" src="{{{asset($content->head_path)}}}">
                    @elseif ($type == 0)
                    <img class="media-object media-border-radius" src="{{{asset('assets/img/avatara.png')}}}">
                    @elseif ($type == 1)
                    <img class="media-object media-border-radius" src="{{{asset('assets/img/avataru.png')}}}">
                    @endif
                </span>
                <div class="heading-media">
                <a class="name-body-title" title="{{$content->user_name}}" href="#">{{$content->name}}</a> 
                <span class="time">{{$content->created_at}} #{{$content->id}}</span>
                </div>
                <span class="body-media">{{$content->content}}</span>
            </div>
            <hr class="hr-middle" />
            <!-- <div class="col-lg-12">
                <textarea class="form-control" id="remarks_content" rows="3" placeholder="请输入内容..." ></textarea>
                <br />
                <a class="view-remarks-tab"> 
                    <button type="button" class="btn btn-primary">确认</button>
                </a>
            </div>
    </form> -->
    </div>
</div>
@endif

备注编号：#{{$id}}
<hr class="hr-middle" />
<div class="col-lg-12">
    <textarea class="form-control" id="remarks_content" rows="3" placeholder="请输入内容..." ></textarea>
    <br />
    <button type="button" class="btn btn-primary view-remarks-tab">确认</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</div>
</form>

<script>
$("a,button").focus(function() {
    this.blur()
});

$(function() {
    $('.view-remarks-tab').click(function() {
        // 获取备注内容
        var vals = $('#remarks_content').val();
        if (vals == '') {
            alert('请输入内容!');
            return false;
        }
        
        // 换行符编码限制URL过滤
        vals = escape(vals);
        var status = {{$status}};
        var type = {{$type}};
        var id = {{$id}};

        window.location.href = "{{ URL::to($com->path.'/admin/order/remark/create?type="+type+"&content="+vals+"&status="+status+"&id=') }}"+id;
    });
});
</script>