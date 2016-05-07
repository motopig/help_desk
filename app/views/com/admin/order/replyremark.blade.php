<style>
.table {
    font-size: 1em;
}
#replyremark_content {
    width: 455px;
    height: 100px;
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

<div class="row" style="overflow-y:auto; overflow-x:auto; height:350px;">
    <div class="col-lg-12">
        <form method="get">
            <div class="order-title-alert order-title-right">
                #{{$order}} {{$title}}
            </div>
            <div class="media-ask-reply-bgc">
                {{$content}}
            </div>
            <hr class="hr-middle" />

            <div class="col-lg-12">
                <textarea class="form-control" id="replyremark_content" rows="3" placeholder="请输入内容..." ></textarea>
                <br />
                <a class="view-replyremark-tab"> 
                    <button type="button" class="btn btn-primary">回复并备注</button>
                </a>
            </div>
    </form>
    </div>
</div>

<script>
$("a,button").focus(function() {
    this.blur()
});

$(function() {
    $('.view-replyremark-tab').click(function() {
        // 获取备注内容
        var vals = $('#replyremark_content').val();
        if (vals == '') {
            alert('请输入内容!');
            return false;
        }
        
        // 换行符编码限制URL过滤
        vals = escape(vals);
        var status = {{$status}};
        var id = {{$order}};
        var val = escape('{{$content}}');
        
        window.location.href = "{{ URL::to($com->path.'/admin/order/replyremark/create?status="+status+"&replyremark="+vals+"&content="+val+"&id=') }}"+id;
    });
});
</script>