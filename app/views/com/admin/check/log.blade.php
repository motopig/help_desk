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
        <b>审核建议：</b><br />
        @foreach ($log as $l)
        <div class="alert alert-success">
        {{$l->admin}} ({{$l->position}}) 在 {{$l->created_at}} 给出如下建议：<br />
        {{$l->content}}
        </div>
        @endforeach
    </div>
</div>
