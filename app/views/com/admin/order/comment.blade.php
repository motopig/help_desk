<style>
.table {
    font-size: 1em;
}
</style>

<div class="row" style="overflow-y:auto; overflow-x:auto; height:240px;">
    <div class="col-lg-12">
        <form>
            @if (isset($content))
            <div class="media media-ask-reply-bgc">
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
            @endif

            @foreach ($remark as $r)
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
                <span class="time">{{$r->created_at}}</span>
                </div>
                <span class="body-media">{{$r->content}}</span>
            </div>
            <hr class="hr-middle" />
            @endforeach
        </form>
    </div>
</div>

<!-- <hr class="hr-middle" /> -->
<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
