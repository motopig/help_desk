<style>
.table {
    font-size: 1em;
}
</style>

<form method="get">
<div class="row" style="overflow-y:auto; overflow-x:auto; height:160px;">
    <div class="col-lg-12">
        <!-- <form method="get"> -->
        <table class="table">
            <div class="form-group">
                <div class="col-sm-10">
                    <h5>工单号：#{{$order->id}}</h5>
                    <input id="marks" type="text" class="form-control" placeholder="默认为空" name="mark" value="{{$order->mark}}" />
                </div>
            </div>
        </table>
        <!-- <hr class="hr-middle" />
        <a class="view-tab"> 
            <button type="button" class="btn btn-primary">确认</button>
        </a>
    </form> -->
    </div>
</div>

<hr class="hr-middle" />
<button type="button" class="btn btn-primary view-tab">确认</button>
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</form>

<script>
$(function() {
    $('.view-tab').click(function() {
        var mark = $('#marks').val();
        // if (mark != '' && mark.length > 12) {
        //     alert('最多只能输入12个字符!');
        //     return false;
        // }

        var status = {{$status}};
        var id = {{$order->id}};

        window.location.href = "{{ URL::to($com->path.'/admin/order/ordertab/create?mark="+mark+"&status="+status+"&id=') }}"+id;
    });
});
</script>