<div id="order-left">
@if ($level < 4)
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order') }}" onclick="clickCaret()" class='hover-order-left'>
		工单池
	</a>
	@if ($count['0'] > 0)
	&nbsp;<span class="badges">{{$count['0']}}</span>
	@endif

	@if ($os == 0)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
@endif
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=1') }}" class='hover-order-left'>
		待处理
	</a>
	@if ($count['1'] > 0)
	&nbsp;<span class="badges">{{$count['1']}}</span>
	@endif

	@if ($os == 1)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=2') }}" class='hover-order-left'>
		已处理
	</a>
	@if ($count['2'] > 0)
	&nbsp;<span class="badges">{{$count['2']}}</span>
	@endif

	@if ($os == 2)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=3') }}" class='hover-order-left'>
		审核池
	</a>
	@if ($count['3'] > 0)
	&nbsp;<span class="badges">{{$count['3']}}</span>
	@endif

	@if ($os == 3)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=4') }}" class='hover-order-left'>
		驳回池
	</a>
	@if ($count['4'] > 0)
	&nbsp;<span class="badges">{{$count['4']}}</span>
	@endif

	@if ($os == 4)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=5') }}" class='hover-order-left'>
		草稿箱
	</a>
	@if ($count['5'] > 0)
	&nbsp;<span class="badges">{{$count['5']}}</span>
	@endif

	@if ($os == 5)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=9') }}" class='hover-order-left'>
		银行凭证
	</a>
	@if ($count['9'] > 0)
	&nbsp;<span class="badges">{{$count['9']}}</span>
	@endif

	@if ($os == 9)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
@if ($level < 4)
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=6') }}" class='hover-order-left'>
		垃圾箱
	</a>
	@if ($count['6'] > 0)
	&nbsp;<span class="badges">{{$count['6']}}</span>
	@endif

	@if ($os == 6)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
@endif
<div class="lev">
	<a href="{{ URL::to($com->path.'/admin/order?status=7') }}" class='hover-order-left'>
		废纸篓
	</a>
	@if ($count['7'] > 0)
	&nbsp;<span class="badges">{{$count['7']}}</span>
	@endif

	@if ($os == 7)
	&nbsp;<span class="caret-desktop"></span>
	@endif

	<hr class="hr-middle" />
</div>
<div class="levs">
	<a href="{{ URL::to($com->path.'/admin/order/createOrder') }}" class='hover-order-left'>
		新建工单
	</a>

	@if ($os == 8)
	&nbsp;<span class="caret-desktop"></span>
	@endif
</div>
</div>
