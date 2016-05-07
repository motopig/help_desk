<link rel="stylesheet" href="{{{ asset('assets/css/jquery.lighter.css') }}}" />
<script src="{{{ asset('assets/js/jquery.lighter.js') }}}"></script>
<style>
.table {
    font-size: 1em;
}
.image-enclosureall {
    float:left; 
    width:50px;
    height:50px;
}
</style>

<div class="row" style="overflow-y:auto; overflow-x:auto; height:240px;">
    <div class="col-lg-12">
        <form>
            @if (count($enclosure) == 0)
            <font color="red">暂无工单附件！</font>
            @else
            @foreach ($enclosure as $e)
            <div class="media">
                <span class="pull-left">
                    @if (isset($e->head_path))
                    <img class="media-object media-border-radius" src="{{{asset($e->head_path)}}}">
                    @else
                    @if ($e->admin == 0)
                    <img class="media-object media-border-radius" src="{{{asset('assets/img/avataru.png')}}}">
                    @elseif ($e->admin == 1)
                    <img class="media-object media-border-radius" src="{{{asset('assets/img/avatara.png')}}}">
                    @endif
                    @endif
                </span>
                <div class="heading-media">
                <a class="name-body-title" title="{{$e->user_name}}" href="#">{{$e->name}}</a> 
                <span class="time">{{$e->created_at}}</span>
                @if ($e->admin == 0)
                <span class="sphtml glyphicon glyphicon-user" title="客户"></span>
                @endif
                </div>
                <span class="body-media">
                    @if ($e->type == 'image')
                    <a title="点击预览" href="{{ asset($e->path) }}" data-lighter>
                    <img class="image-enclosureall" src="{{ asset($e->path) }}">
                    </a>
                    @else
                    <span class="glyphicon glyphicon-file" title="文件下载"></span>
                    <a title="点击下载" href="{{ asset($e->path) }}" target="_blank">
                    {{$e->enclosure_name}}
                    </a>
                    @endif
                </span>
            </div>
            <hr class="hr-middle" />
            @endforeach
            @endif
        </form>
    </div>
</div>

<hr class="hr-middle" />
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
