<style>
.image-enclosureall {
    float:left; width:25%;height:50px;
}
.file-enclosureall {
	float:left; width:15%;height:40px;
}
.con{
	margin-left: 5px;
}
</style>
<div class="row" style="overflow-y:auto; overflow-x:auto; height:420px;">
    <div class="col-lg-12">
        <ul>
        	@if ($enclosureAll == '0')
            <font color="red">此工单暂无附件！</font>
            @else
        	@foreach ($enclosureAll as $e)
        	@if ($e['enclosure_type'] == 'image')
        	<a title="点击预览" href="{{ asset($e['enclosure_path']) }}" data-lighter>
            <img class="image-enclosureall" src="{{ asset($e['enclosure_path']) }}">
        	<span class="con">
        		{{$e['enclosure_name']}} <br />
            	&nbsp;{{$e['enclosure_created_at']}}
        	</span>
        	</a>
        	@else
        	<a title="点击下载" href="{{ asset($e['enclosure_path']) }}">
        	@if ($e['enclosure_suffix'] == 'docx')
            <img class="file-enclosureall" src="{{ asset('assets/img/file_docx.png') }}">
            @elseif ($e['enclosure_suffix'] == 'xlsx')
            <img class="file-enclosureall" src="{{ asset('assets/img/file_xlsx.png') }}">
            @elseif ($e['enclosure_suffix'] == 'txt')
            <img class="file-enclosureall" src="{{ asset('assets/img/file_txt.png') }}">
            @endif
        	<span class="con">
        		{{$e['enclosure_name']}} <br />
            	&nbsp;{{$e['enclosure_created_at']}}
        	</span>
        	</a>
        	@endif
        	<hr />
        	@endforeach
        	@endif
		</ul>
    </div>
</div>