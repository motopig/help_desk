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
        <b>历史记录：</b><br />
        @foreach ($content as $w)
        @if (isset($w->ask))
        <div 
        @if ($work_level == '3')
        class="alert alert-danger"
        @elseif ($work_level == '2')
        class="alert alert-warning"
        @else
        class="alert alert-info"
        @endif
        >
        {{$w->name}} (客户) 在 {{$w->created_at}} 说：<br />
        {{$w->ask}}
        </div>
        @elseif (isset($w->reply))
        @if ($w->system == 1)
        <div class="alert alert-success">
        系统于 {{$w->created_at}} 回复：<br />
        {{$w->reply}}
        </div>
        @else
        <div class="alert alert-success">
        {{$w->name}} ({{$w->position}}) 在 {{$w->created_at}} 说：<br />
        {{$w->reply}}
        </div>
        @endif
        @endif
        @endforeach
    </div>
</div>
