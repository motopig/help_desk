<style>
.table {
    font-size: 1em;
}
</style>

<div class="row" style="overflow-y:auto; overflow-x:auto; height:250px;">
    <div class="col-lg-12">
        <form>
            @if ($log == 0)
            <font color="red">暂无工单日志记录！</font>
            @else
            <div class="order-title-alert order-title-right">
                #{{$order->id}} {{$order->title}}
            </div>
            @foreach ($log as $r)
            <div class="media">
                <span class="pull-left">
                    @if (isset($r->head_path))
                    <img class="media-object media-border-radius" src="{{{asset($r->head_path)}}}">
                    @else
                    <img class="media-object media-border-radius" src="{{{asset('assets/img/avatara.png')}}}">
                    @endif
                </span>
                <div class="heading-media">
                <a class="name-body-title" title="{{$r->user_name}}" href="#">{{$r->name}}</a> 
                <span class="time">{{$r->updated_at}}</span>
                </div>
                <span class="body-media">{{$r->log_content}}</span>
            </div>
            <hr class="hr-middle" />
            @endforeach
            @endif
        </form>
    </div>
</div>

<hr class="hr-middle" />
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
