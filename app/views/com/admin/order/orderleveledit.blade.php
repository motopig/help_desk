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
                    <h5>
                        工单号：#{{$order->id}} &nbsp;<span class="l1">|</span>&nbsp; 当前工单级别：
                        @if (isset($order->level_name))
                        <font color="red">{{$order->level_name}}</font>
                        @else
                        <font color="#999">无</font>
                        @endif
                    </h5>
                    <select id="order_level_edit" class="form-control">
                        <option value="0">默认为空</option>
                        @foreach ($order_level as $u)
                        <option value="{{ $u->id }}" >{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </table>
        <!-- <hr class="hr-middle" />
        <a class="view-order-level-edit"> 
            <button type="button" class="btn btn-primary">确认</button>
        </a>
    </form> -->
    </div>
</div>

<hr class="hr-middle" />
<button type="button" class="btn btn-primary view-order-level-edit">转移</button>
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</form>

<script>
$(function() {
    $('.view-order-level-edit').click(function() {
        var order_level = $('#order_level_edit').val();
        var status = {{$status}};
        var id = {{$order->id}};

        window.location.href = "{{ URL::to($com->path.'/admin/order/orderleveledit/create?order_level="+order_level+"&status="+status+"&id=') }}"+id;
    });
});
</script>