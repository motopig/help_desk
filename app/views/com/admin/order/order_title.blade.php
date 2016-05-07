<!-- 搜索框 - start -->
<style>
.table {
    font-size: 1em;
}
</style>

<div class="row">
	<div class="col-lg-3">
	<div class="input-group">
	<div class="input-group-btn">
	<button type="button" title="点击搜索" class="btn btn-default" tabindex="-1" onclick="searchFilter()" 
	style="height:30px;background-color:#428bca;opacity:1.0;color:#fff;line-height:3px;font-size:10px;">
	<span class="search-like" id="search-like-text">标题</span> <span class="glyphicon glyphicon-search"></span>
	</button>
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height:30px;" tabindex="-1">
	<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">
	<li><a href="javascript:void(0)" onclick="searchSelect(1)"><font size="1">标题</font></a></li>
  <li><a href="javascript:void(0)" onclick="searchSelect(3)"><font size="1">工单号</font></a></li>
	@if ($os != 7)
	<li><a href="javascript:void(0)" onclick="searchSelect(2)"><font size="1">申请人邮箱</font></a></li>
  <li><a href="javascript:void(0)" onclick="searchSelect(6)"><font size="1">受理人邮箱</font></a></li>
  <li><a href="javascript:void(0)" onclick="searchSelect(7)"><font size="1">申请人姓名</font></a></li>
  <li><a href="javascript:void(0)" onclick="searchSelect(8)"><font size="1">受理人姓名</font></a></li>
	<li><a href="javascript:void(0)" onclick="searchSelect(4)"><font size="1">接收内容</font></a></li>
  <li><a href="javascript:void(0)" onclick="searchSelect(5)"><font size="1">回复内容</font></a></li>
	@endif
	</ul>
	</div>
	<input type="text" id="search-content-data" class="form-control-search" placeholder="搜索" style="height:30px;width:80px;font-size:10px;">
	<input type="hidden" id="search-like-data" value="1" />
	</div>
	</div>
</div>
<!-- 搜索框 - end -->

<!-- 选择框 - start -->
<div class="order-title-filters">
	<span class="order-title-filter">
  	<a class="order_title_name" style="text-decoration:none;" href='javascript:void(0);' 
  	id='selectAll' onclick="selectAll()">^_^</a>
  	</span>
	<div class="btn-group">
    <button type="button" title="点击操作" class="btn-filters btn-default dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-cog"></span>
    <span class="caret"></span></button>
    <ul class="dropdown-menu menus" role="menu">
      @if ($level < 4 && $os != 7)
      <li><a href="javascript:void(0)" onclick="selectFilter(0)"><font size="1">工单分配</font></a></li>
      @endif
      @if ($os != 7)
      <!-- <li><a href="javascript:void(0)" onclick="selectFilter(4)"><font size="1">工单转移</font></a></li> -->
      @if ($os != 9)
      <li><a href="javascript:void(0)" onclick="selectFilter(3)"><font size="1">移入已处理</font></a></li>
      @elseif ($os == 9)
      <li><a href="javascript:void(0)" onclick="selectFilter(5)"><font size="1">取消银行凭证</font></a></li>
      @endif
      <li><a href="javascript:void(0)" onclick="selectFilter(1)"><font size="1">扔进废纸篓</font></a></li>
      @endif
      @if ($os == 7)
      <li><a href="javascript:void(0)" onclick="selectFilter(2)"><font size="1">移出废纸篓</font></a></li>
      @endif
    </ul>
   </div>

   <!-- 筛选结果 - start -->
   @if (isset($search_count))
   <span class="filter-end">
   共检索到 <font color="red">{{$search_count}}</font> 条 
   <a href="{{ URL::to($com->path.'/admin/order?status='.$os) }}"><span class="glyphicon glyphicon-remove"></span></a>
   </span>
   @endif
   <!-- 筛选结果 - end -->
</div>
<!-- 选择框 - end -->
<hr class="hr-middle" />
