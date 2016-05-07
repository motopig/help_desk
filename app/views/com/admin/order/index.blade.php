@extends('com.admin.layouts.default')

@section('title')
{{ $com->company_name }} :: @parent
@stop

<!-- binway样式引入 - start -->
<link href="{{{ asset('assets/css/order.css') }}}" rel="stylesheet">
<!-- binway样式引入 - end -->

@section('content')
<div class="row">
<div class="col-lg-12">
<!-- <ol class="breadcrumb">
    <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
    <li class="active">工单管理</li>
</ol> -->
<!-- Notifications -->
@include('root.notifications')
<!-- ./ notifications -->
</div>
</div>

<!-- 工单左栏 - start -->
@include('com.admin.order.order_left')
<!-- 工单左栏 - ned -->

<!-- 工单中栏 - start -->
<div id="order-middle">
	<!-- 工单中栏标题区域 - start -->
	@include('com.admin.order.order_title')
	<!-- 工单中栏标题区域 - end -->

	<!-- 工单标题 - start -->
	@if (count($title) == 0)
		<div>&nbsp;</div>
	@else
		<div id="title_divs">
			@foreach ($title as $t)
			<div id="title_{{$t->id}}">
				<div id="tasklistshow_{{$t->id}}" class="media tasklist">
				  <div class="heading-media">
				  	<span class="order-title-filter">
				  	<input type="checkbox" value="{{$t->id}}" name="title_filter" id="idcheck" />
				  	</span>
				  	<a class="order_title" href="javascript:void(0)" onclick="orderAjax({{$t->id}}, {{$os}}), tasklistshow({{$t->id}})">
				  	<span class="order_title_name" title="{{$t->user_name}}">{{$t->name}}</span>
				  	</a>
				  	@if (isset($t->level_name))
				  		<span class="labels-sms">{{$t->level_name}}</span>
				  	@endif

				  	@if ($t->type == 'admin')
				  		&nbsp;<span class="glyphicon glyphicon-eye-close" title="内部工单"></span>
				  	@endif

				  	<!-- <abbr class="order_title_time timeago" title="{{$t->updated_at}}">{{$t->updated_at}}</abbr> -->
				  	<br />
				  	<span class="order_title_time" title="{{$t->updated_at}}"><b>{{$t->updated_at}}</b></span>
				  	<br />
				  </div>
				  @if ($t->mark != '')
				  <div class="mark-align">
				  	<span class="mark-sms">{{$t->mark}}</span>
				  </div>
				  @endif
				  <a class="order_title" href="javascript:void(0)" onclick="orderAjax({{$t->id}}, {{$os}}), tasklistshow({{$t->id}})">
				  <span class="body-media">{{$t->title}}</span>
				  </a>
				</div>
				@if (isset($t->admin_name))
				<div class="l11" title="{{$t->admin_user_name}}">
					受理人：<a class="order_title" href="#">{{$t->admin_name}}</a>
				</div>
				@endif
				<hr class="hr-middle" />
			</div>
			@endforeach
		</div>

		<!-- 点击显示更多 - start -->
		<!-- <input type="hidden" id="skip" value="{{count($title)}}" />
		@if (!isset($search_count))
		<div id="more" style="display:none;">
			<a class="order_title mores" href="javascript:void(0)" onclick="orderMore()">
				<span class="glyphicon glyphicon-hand-right"></span>&nbsp;
				点击显示更多
			</a>
			<hr class="hr-middle" />
		</div>
		@endif -->
		<!-- 点击显示更多 - end -->

		<!-- 分页显示 -->
		@if (isset($search_count))
            {{ $title->appends(array('status' => $os, 'search_content' => $search_content, 'search_type' => $search_type, 'search_count' => $search_count))->links(); }}
        @else
            {{ $title->appends(array('status' => $os))->links(); }}
        @endif
        <!-- 分页显示 -->
	@endif
	<!-- 工单标题 - end -->
</div>
<!-- 工单中栏 - ned -->

<!-- 工单右栏 - start -->
<div id="order-right">
	<!-- 工单右栏编辑器 - start-->
	<div class="media">
		<!-- 当前登入用户头像 - start -->
	    <a class="pull-left" href="#">
		  	@if (Auth::user()->head > 0)
		    <img class="media-object media-border-radius" src="{{ asset(Auth::user()->enclosures_path) }}" />
		    @else
		    <img class="media-object media-border-radius" src="{{ asset('assets/img/avatara.png') }}" />
		    @endif
	    </a>
	    <!-- 当前登入用户头像 - end -->
	  
	  	<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" onsubmit="return orderSubmitClick()">
		<input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />
		<input type="hidden" name="status" id="status" value="{{$os}}" />
		<input type="hidden" value="" id="button-order" name="id" />
		
	  	<!-- 按钮组 - start -->
	  	<div class="heading-media title-media">
	  		@if ($os == 0 || $os == 1 || $os == 2 || $os == 5 || $os == 9)
	  		<!-- <a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(5)">
			<button type="button" class="btn-sms btn-primary" title="工单回复">
				<span class="glyphicon glyphicon-ok"></span>
			</button>
			</a> -->
			@if ($os == 0)
			<button type="submit" class="btn-sms btn-primary" title="工单池回复">
			@else
			<button type="submit" class="btn-sms btn-primary" title="工单回复">
			@endif
				<span class="glyphicon glyphicon-ok"></span>
			</button>
			@endif

			@if (($os != 7) && $level < 4)
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(0)">
			<button type="button" class="btn-sms btn-info" title="工单分配">
				<span class="glyphicon glyphicon-share-alt"></span>
			</button>
			</a>
			@endif

			@if ($os != 7)
			<!-- <a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(10)">
			<button type="button" class="btn-sms btn-pink" title="工单转移">
				<span class="glyphicon glyphicon-random"></span>
			</button>
			</a> -->

			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(12)">
			<button type="button" class="btn-sms btn-pupre" title="工单邀请">
				<span class="glyphicon glyphicon-earphone"></span>
			</button>
			</a>

			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(1)">
			<button type="button" class="btn-sms btn-warning" title="扔进废纸篓">
				<span class="glyphicon glyphicon-trash"></span>
			</button>
			</a>
			@endif

			@if ($os == 7)
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(4)">
			<button type="button" class="btn-sms btn-success" title="移出废纸篓">
				<span class="glyphicon glyphicon-repeat"></span>
			</button>
			</a>
			@if ($level < 4)
			<button type="button" class="btn-sms btn-danger" title="彻底删除">
				<span class="glyphicon glyphicon-trash"></span>
			</button>
			@endif
			@endif

			&nbsp;<span class="l1">|</span>&nbsp; 
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(2)">
			<button type="button" class="btn-sms btn-default" title="自定义标签">
				<span class="glyphicon glyphicon-tags"></span>
			</button>
			</a>

			@if ($level < 4)
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(3)">
			<button type="button" class="btn-sms btn-default" title="变更工单级别">
				<span class="glyphicon glyphicon-sort"></span>
			</button>
			</a>
			@endif

			@if ($os == 1 || $os == 2 || $os == 5 || $os == 9)
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(6)">
			<button type="button" class="btn-sms btn-default" title="存储草稿">
				<span class="glyphicon glyphicon-pencil"></span>
			</button>
			</a>
			@endif

			@if ($os == 5)
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(7)">
			<button type="button" class="btn-sms btn-default" title="删除草稿">
				<span class="glyphicon glyphicon-trash"></span>
			</button>
			</a>
			@endif

			@if ($os != 0 && $os != 6 && $os != 7 && $os != 9)
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(11)">
			<button type="button" class="btn-sms btn-default" title="标记银行凭证">
				<span class="glyphicon glyphicon-credit-card"></span>
			</button>
			</a>
			@endif

			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(9)">
			<button type="button" class="btn-sms btn-default" title="工单附件">
				<span class="glyphicon glyphicon-paperclip"></span>
			</button>
			</a>

			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(8)">
			<button type="button" class="btn-sms btn-default" title="工单日志">
				<span class="glyphicon glyphicon-time"></span>
			</button>
			</a>

			@if ($os == 0 || $os == 1 || $os == 2 || $os == 5)
			&nbsp;<span class="l1">|</span>&nbsp; 
			<a class="order_title" href="javascript:void(0)" onfocus="this.blur()" onclick="ccEmail()">
			<button class="btn-sms btn-success" title="邮件抄送 (格式：邮箱,邮箱...)" data-toggle="modal" data-target="#myModal">
				<span class="glyphicon glyphicon-subtitles"></span>
			</button>
			</a>
			<span id="show-cc" style="display:none;">
				&nbsp;<input type="text" id="order-text-cc" name="cc" style="font-size:10px;" />
				<a href="javascript:void(0)" onfocus="this.blur()" onclick="unccEmail()">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
			</span>
			@endif
	  	</div>
		<!-- 按钮组 - end -->

		<!-- 输入框 - start -->
		@if ($os == 0 || $os == 1 || $os == 2 || $os == 5 || $os == 9)
		<!-- <span class="order-text-media"><textarea id="order-text-reply" name="content" class="order-text" placeholder="请输入内容..."></textarea></span> -->
		<!-- 副文本编辑器 -->
		<script id="order-text-reply" class="editor-text" name="content" type="text/plain">
		</script>
		
		<span class="fileinput-button-text">
			<a class="order_title" href="javascript:void(0)" onclick="replyRemark()">追加备注</a>
		</span>

		<!-- accept:支持的上传格式, maxlength:支持的上传文件个数 -->
		<!-- <input class="multi" accept="gif|jpg" maxlength="3"/> -->
		&nbsp;<span class="file_l1" id="file_l1_show" style="display:none;">|</span>&nbsp; 
		<span class="filemultipart-button-text" id="reply_file_show" style="display:none;">
		<input type="file" id="reply_file" name="file[]" class="multi"/>
		</span>
		@else
		<span class="order-text-media"><textarea class="order-text-hide" disabled="disabled"></textarea></span>
		@endif
		<!-- 输入框 - end -->

		<!-- 工单回复备注交互页面 － start -->
		<div class="modal fade" id="viewReplyRemark">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
		        href="javascript:void(0)" onfocus="this.blur()" onclick="unReplyRemark()">&times;</button>
		        <h5 class="modal-title">工单回复并备注</h5>
		      </div>
		      <div class="modal-body">
		        <textarea class="order-reply-remark" id="replyremark_content" name="replyremark_content" 
		        rows="3" placeholder="请输入内容..." ></textarea>
		      </div>
		      <div class="modal-footer">
		      	<button type="submit" onfocus="this.blur()" class="btn btn-primary">确认</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal" 
		        href="javascript:void(0)" onfocus="this.blur()" onclick="unReplyRemark()">关闭</button>
				<!-- <button href="javascript:void(0)" onfocus="this.blur()" onclick="orderButtonClick(5)" 
				type="button" class="btn btn-primary">确认</button> -->
		      </div>
		    </div>
		  </div>
		</div>
		<!-- 工单回复备注交互页面 － end -->
		</form>
	</div>
	<hr class="hr-middle" />
	<!-- 工单右栏编辑器 - end -->
	
	<!-- 工单正文标题详情 - start -->
	<div id="order-title-alert-show">
	</div>
	<!-- 工单正文标题详情 - end -->

	<!-- 工单正文 - start -->
	<div class="order-body" id="order-body-content">
		<!-- 默认不显示工单内容，点击标题后展示工单内容 - start -->
		<div class="orderimage" id="no-order-content">
		<img style="height:150px;" src="{{ asset('assets/img/orderemail.png') }}">
		</div>
		<!-- 默认不显示工单内容，点击标题后展示工单内容 - end -->
	</div>
	<!-- 工单正文 - end -->
</div>
<!-- 工单右栏 - ned -->

<!-- 弹窗交互页面 － start -->
<div class="modal fade" id="viewButtonModal" tabindex="-1" role="dialog" aria-labelledby="viewButtonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title" id="viewButtonModalLabel"></h5>
            </div>
            <div class="modal-body" id="viewButtonModalbody">
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div> -->
        </div>
    </div>
</div>
<!-- 弹窗交互页面 － end -->

<!-- binway数据交互 - start -->
<script type="text/javascript">
// 去掉超链接或按钮点击时出现的虚线边框
$("a,button").focus(function() {
	this.blur()
});

// timeago时间显示
jQuery(document).ready(function() {
	$("abbr.timeago").timeago();
});

// 工单标题样式选中风格
function tasklistshow(id) {
	$(".tasklist.media-active").removeClass("media-active");
	$('#tasklistshow_'+id).addClass("media-active");
}

// 点击显示更多显示
var number = 0;
jQuery(document).ready(function() {
	var num = $('#skip').val();
	var count = {{$count[$os]}};
	if (num < count) {
		$('#more').show();
	} else if (num >= count) {
		$('#more').hide();
	}
});

// 点击显示更多方法
function orderMore() {
	var level = {{$level}};
	var status = {{$os}};
	var skip = number;
	var more = 1;

	$.ajax({ // 一个Ajax过程
        type: "post", // 以post方式与后台沟通
        url: "<?php echo URL::to($com->path.'/admin/order/orderMore'); ?>", // 提交地址
        dataType: "json", // 从后台返回的值以JSON方式解释
        data: { level: level, status: status, skip: skip, more: more, number: number, csrf_token:'{{{ Session::getToken() }}}' }, // 发给后台的数据项
        // 调用ajax返回成功配置新内容
        success: function(json) {
            if (json.more_id > 0) {
            	var count = {{$count[$os]}};
                var more_num = json.more_num;
                var title_data = json.html;

                var arr = new Array();
                var len = $("#title_divs >div").length - 1;
				$("#title_divs >div").each(function(i) {
					arr.push($(this).attr("id"));
				});

				var show = arr.pop();
				$('#'+show).after(title_data);
				$("abbr.timeago").timeago();
				$('#skip').val(more_num);
				number = more_num;
				
				var lens = $("#title_divs >div").length;
                if (lens < count) {
					$('#more').show();
				} else if (lens >= count) {
					$('#more').hide();
				}
            }
        }
    });
}

// 获取工单内容
function orderAjax(id, status) {
	$.ajax({ // 一个Ajax过程
        type: "post", // 以post方式与后台沟通
        url: "<?php echo URL::to($com->path.'/admin/order/orderContent'); ?>", // 提交地址
        dataType: "json", // 从后台返回的值以JSON方式解释
        data: { id: id, order_status: status, csrf_token:'{{{ Session::getToken() }}}' }, // 发给后台的数据项
        // 调用ajax返回成功配置新内容
        success: function(json) {
            if (json.content_id > 0) {
            	$('#no-order-content').hide();
            	
            	// 文本对象
                var content_html_title_data = $(json.html_title);
                var content_data = $(json.html);
                $('#order-title-alert-show').empty().append(content_html_title_data);
                $('#order-body-content').empty().append(content_data);
                $('#button-order').val(json.order_id);
                // $('#order-text-reply').val(json.draft_content);
                editor.setContent(json.draft_content); // 副文本编辑器

                // 抄送对象
                $('#order-text-cc').val('');
				$('#show-cc').hide();

				// 附件对象
				$('.MultiFile-label').remove();
				$('#reply_file_show').show();
				$('#file_l1_show').show();
            }
        }
    });
}

// 工单按钮组操作
function orderButtonClick(config) {
	var status = {{$os}};
	var id = $('#button-order').val();

	// 工单审核客服无权操作
	if (status == '3' && {{$level}} == '4') {
		alert('您无权操作！');
		return false;
	}
	
	if (config == 0) { // 分配工单
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        $('#viewButtonModal').modal();
	        $.ajax({
	            type: "GET",
	            url: "<?php echo URL::to($com->path.'/admin/order/orderallot?id="+id+"&status="+status+"'); ?>"
	        }).done(function(html_form) {
	        	$('#viewButtonModalLabel').html('工单分配');
	            $('#viewButtonModalbody').html(html_form);
	            $('#viewButtonModal').show();
	        });
		}
	} else if (config == 1) { // 将工单扔进废纸篓
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        window.location.href = "{{ URL::to($com->path.'/admin/order/orderdisabled?id="+id+"&status=') }}"+status;
		}
	} else if (config == 2) { // 自定义标签
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
			$('#viewButtonModal').modal();
	        $.ajax({
	            type: "GET",
	            url: "<?php echo URL::to($com->path.'/admin/order/ordertab?id="+id+"&status="+status+"'); ?>"
	        }).done(function(html_form) {
	        	$('#viewButtonModalLabel').html('标签设置');
	            $('#viewButtonModalbody').html(html_form);
	            $('#viewButtonModal').show();
	        });

		}
	} else if (config == 3) { // 变更工单级别
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        $('#viewButtonModal').modal();
	        $.ajax({
	            type: "GET",
	            url: "<?php echo URL::to($com->path.'/admin/order/orderleveledit?id="+id+"&status="+status+"'); ?>"
	        }).done(function(html_form) {
	        	$('#viewButtonModalLabel').html('级别设置');
	            $('#viewButtonModalbody').html(html_form);
	            $('#viewButtonModal').show();
	        });
		}
	} else if (config == 4) { // 将工单移出废纸篓
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        window.location.href = "{{ URL::to($com->path.'/admin/order/orderdisabledout?id="+id+"&status=') }}"+status;
		}
	} else if (config == 5) { // 工单回复处理
		var content = $("#order-text-reply").val();
		var cc = $('#order-text-cc').val();
		var replyremark_content = $('#replyremark_content').val();
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else if (content == '') {
	        alert('请先输入回复内容！');
			return false;
		} else {
			if (cc != '') {
				if ((cc.split('@').length-1) == 0) {
					alert('抄送邮件格式不正确！');
					return false;
				} else {
					if ((cc.split('@').length-1) > 1 && (cc.split(',').length-1) == 0) {
						alert('抄送邮件格式不正确！');
						return false;
					}
				}
			}
		
			$.ajax({ // 一个Ajax过程
				type: "post", // 以post方式与后台沟通
	        	url: "<?php echo URL::to($com->path.'/admin/order/orderReply'); ?>", // 提交地址
	        	dataType: "json", // 从后台返回的值以JSON方式解释
	        	data: { id: id, csrf_token:'{{{ Session::getToken() }}}', status: status, content: content, cc: cc, replyremark_content: replyremark_content }, // 发给后台的数据项
				success: function(json) {
		            if (json.config_id > 0) {
		                alert('回复工单失败！');
		                return false;
		            } else {
		            	var status_id = json.status_id;
		            	if (status_id == 0) {
		            		window.location.href = "{{ URL::to($com->path.'/admin/order') }}";
		            	} else {
		            		window.location.href = "{{ URL::to($com->path.'/admin/order?status=') }}"+status_id;
		            	}
		            }
		        }
	        });
		}
	} else if (config == 6) { // 存储草稿
		// var content = $("#order-text-reply").val();
		var content = editor.getContent(); // 富文本编辑器
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else if (content == '') {
	        alert('请先输入回复内容！');
			return false;
		} else {
			// 换行符编码限制URL过滤
        	var vals = escape(content);
			window.location.href = "{{ URL::to($com->path.'/admin/order/draft/create?id="+id+"&content="+vals+"&status=') }}"+status;
		}
	} else if (config == 7) { // 删除草稿
		var content = $("#order-text-reply").val();
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
			window.location.href = "{{ URL::to($com->path.'/admin/order/draft/delete?id="+id+"&status=') }}"+status;
		}
	} else if (config == 8) { // 工单日志
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        $('#viewButtonModal').modal();
	        $.ajax({
	            type: "GET",
	            url: "<?php echo URL::to($com->path.'/admin/order/orderlog?id="+id+"&status="+status+"'); ?>"
	        }).done(function(html_form) {
	        	$('#viewButtonModalLabel').html('工单日志');
	            $('#viewButtonModalbody').html(html_form);
	            $('#viewButtonModal').show();
	        });
		}
	} else if (config == 9) { // 工单附件
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        $('#viewButtonModal').modal();
	        $.ajax({
	            type: "GET",
	            url: "<?php echo URL::to($com->path.'/admin/order/order_enclosure?id="+id+"&status="+status+"'); ?>"
	        }).done(function(html_form) {
	        	$('#viewButtonModalLabel').html('工单往来附件');
	            $('#viewButtonModalbody').html(html_form);
	            $('#viewButtonModal').show();
	        });
		}
	} else if (config == 10) { // 工单转移
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        $('#viewButtonModal').modal();
	        $.ajax({
	            type: "GET",
	            url: "<?php echo URL::to($com->path.'/admin/order/ordermigrate?id="+id+"&status="+status+"'); ?>"
	        }).done(function(html_form) {
	        	$('#viewButtonModalLabel').html('工单转移');
	            $('#viewButtonModalbody').html(html_form);
	            $('#viewButtonModal').show();
	        });
		}
	} else if (config == 11) { // 移入银行凭证
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        window.location.href = "{{ URL::to($com->path.'/admin/order/orderbank?id="+id+"&status=') }}"+status;
		}
	} else if (config == 12) { // 工单邀请
		if (id == '') {
			alert('请先选择工单！');
			return false;
		} else {
	        $('#viewButtonModal').modal();
	        $.ajax({
	            type: "GET",
	            url: "<?php echo URL::to($com->path.'/admin/order/orderinvite?id="+id+"&status="+status+"'); ?>"
	        }).done(function(html_form) {
	        	$('#viewButtonModalLabel').html('工单邀请');
	            $('#viewButtonModalbody').html(html_form);
	            $('#viewButtonModal').show();
	        });
		}
	} 
}

// 工单审核
function process(id) {
	var status = {{$os}};
	if ({{$level}} == 4) {
		alert('您无权审核！');
		return false;
	}

	if (status == '7') {
		alert('废纸篓中的工单无法操作！');
		return false;
	}
	
	$('#viewButtonModal').modal();
    $.ajax({
        type: "GET",
        url: "<?php echo URL::to($com->path.'/admin/order/process?id="+id+"&status="+status+"'); ?>"
    }).done(function(html_form) {
    	$('#viewButtonModalLabel').html('工单审核');
        $('#viewButtonModalbody').html(html_form);
        $('#viewButtonModal').show();
    });
}

// 工单驳回
function overrule(id) {
	var status = {{$os}};

	if (status == '7') {
		alert('废纸篓中的工单无法操作！');
		return false;
	}

	$('#viewButtonModal').modal();
    $.ajax({
        type: "GET",
        url: "<?php echo URL::to($com->path.'/admin/order/overrule?id="+id+"&status="+status+"'); ?>"
    }).done(function(html_form) {
    	$('#viewButtonModalLabel').html('工单驳回');
        $('#viewButtonModalbody').html(html_form);
        $('#viewButtonModal').show();
    });
}

// 备注信息
function comment(id, type) {
	var status = {{$os}};

	$('#viewButtonModal').modal();
    $.ajax({
        type: "GET",
        url: "<?php echo URL::to($com->path.'/admin/order/comment?id="+id+"&type="+type+"&status="+status+"'); ?>"
    }).done(function(html_form) {
    	$('#viewButtonModalLabel').html('备注信息');
        $('#viewButtonModalbody').html(html_form);
        $('#viewButtonModal').show();
    });
}

// 附件信息
function enclosure(id, type) {
	var status = {{$os}};

	$('#viewButtonModal').modal();
    $.ajax({
        type: "GET",
        url: "<?php echo URL::to($com->path.'/admin/order/enclosure?id="+id+"&type="+type+"&status="+status+"'); ?>"
    }).done(function(html_form) {
    	$('#viewButtonModalLabel').html('附件信息');
        $('#viewButtonModalbody').html(html_form);
        $('#viewButtonModal').show();
    });
}

// 添加备注
function remark(id, type) {
	var status = {{$os}};

	$('#viewButtonModal').modal();
    $.ajax({
        type: "GET",
        url: "<?php echo URL::to($com->path.'/admin/order/remark?id="+id+"&type="+type+"&status="+status+"'); ?>"
    }).done(function(html_form) {
    	$('#viewButtonModalLabel').html('工单备注');
        $('#viewButtonModalbody').html(html_form);
        $('#viewButtonModal').show();
    });
}

// 回复内容追加备注
function replyRemark() {
	var status = {{$os}};
	var id = $('#button-order').val();
	// var content = $('#order-text-reply').val();
	var content = editor.getContent(); // 富文本编辑器
	var cc = $('#order-text-cc').val();
	
	if (id == '') {
		alert('请先选择工单！');
		return false;
	} else if (content == '') {
        alert('请先输入回复内容！');
		return false;
	} else {
		if (cc != '') {
			if ((cc.split('@').length-1) == 0) {
				alert('抄送邮件格式不正确！');
				return false;
			} else {
				if ((cc.split('@').length-1) > 1 && (cc.split(',').length-1) == 0) {
					alert('抄送邮件格式不正确！');
					return false;
				}
			}
		}

		// // 换行符编码限制URL过滤
  //       var vals = escape(content);

		// $('#viewButtonModal').modal();
	 //    $.ajax({
	 //        type: "GET",
	 //        url: "<?php echo URL::to($com->path.'/admin/order/replyremark?id="+id+"&content="+vals+"&cc="+cc+"&status="+status+"'); ?>"
	 //    }).done(function(html_form) {
	 //    	$('#viewButtonModalLabel').html('工单回复并备注');
	 //        $('#viewButtonModalbody').html(html_form);
	 //        $('#viewButtonModal').show();
	 //    });

		$('#viewReplyRemark').modal('show');
	}
}

// 关闭工单回复并备注
function unReplyRemark() {
	$('#replyremark_content').val('');
	$('#viewReplyRemark').modal('hide');
}

// 复选框全选全不选
function selectAll() {
	if ($('#selectAll').html() == '^_^') {
	    $('input[id="idcheck"]').prop('checked',true);
	    $('#selectAll').html('!__!');
	} else if($('#selectAll').html() == '!__!') { 
	    $('input[id="idcheck"]').prop('checked',false);
	    $('#selectAll').html('^_^');
	}
}

// 工单批量操作
function selectFilter(type) {
	var status = {{$os}};

	// 工单审核客服无权操作
	if (status == '3' && {{$level}} == '4') {
		alert('您无权操作！');
		return false;
	}

	// 将选中的对象加入到数组中
	var del_val = new Array();
    $.each($('input[id="idcheck"]'),function(i,e) {
        if ($(e).prop('checked') == true) {
            del_val.push($(e).val());
        }
    });

    // 判断是否选择数据
    if (del_val != '') {
    	if (type == 0 || type == 4) {
    		var msg = '';
    		if (type == 0) {
    			msg = '工单批量分配';
    		} else if (type == 4) {
    			msg = '工单批量转移';
    		}

    		$('#viewButtonModal').modal();
	
		    $.ajax({
		        type: "GET",
		        url: "{{ URL::to($com->path.'/admin/order/order_filter?ids="+del_val+"&type="+type+"&status=') }}"+status,
		    }).done(function(html_form) {
		    	$('#viewButtonModalLabel').html(msg);
		        $('#viewButtonModalbody').html(html_form);
		        $('#viewButtonModal').show();
		    });
    	} else {
    		window.location.href = "{{ URL::to($com->path.'/admin/order/order_filter?ids="+del_val+"&type="+type+"&status=') }}"+status;
    	}
    } else {
    	alert('请勾选需要操作的选项!');
    	return false;
    }
}

// 搜索条件筛选
function searchSelect(type) {
	if (type == 1) {
		$('#search-like-text').html('标题');
		$('#search-like-data').val(1);
	} else if (type == 2) {
		$('#search-like-text').html('申请人邮箱');
		$('#search-like-data').val(2);
	} else if (type == 3) {
		$('#search-like-text').html('工单号');
		$('#search-like-data').val(3);
	} else if (type == 4) {
		$('#search-like-text').html('接收内容');
		$('#search-like-data').val(4);
	} else if (type == 5) {
		$('#search-like-text').html('回复内容');
		$('#search-like-data').val(5);
	} else if (type == 6) {
		$('#search-like-text').html('受理人邮箱');
		$('#search-like-data').val(6);
	} else if (type == 7) {
		$('#search-like-text').html('申请人姓名');
		$('#search-like-data').val(7);
	} else if (type == 8) {
		$('#search-like-text').html('受理人姓名');
		$('#search-like-data').val(8);
	}
}

// 工单搜索
function searchFilter() {
	var data = $('#search-content-data').val();

	if (data == '') {
		alert('请先输入搜索内容！');
		return false;
	} else {
		var status = {{$os}};
		var search_content = data;
		var search_type = $('#search-like-data').val();

		window.location.href = "{{ URL::to($com->path.'/admin/order?search_content="+search_content+"&search_type="+search_type+"&status=') }}"+status;
	}
}

// 打开邮件抄送
function ccEmail() {
	var id = $('#button-order').val();
	if (id == '') {
		alert('请先选择工单！');
		return false;
	}

	$('#show-cc').show();
}

// 关闭邮件抄送
function unccEmail() {
	$('#order-text-cc').val('');
	$('#show-cc').hide();
}

// 副文本编辑器
var editor = new UE.ui.Editor({
	initialFrameHeight:110,
	initialFrameWidth:654,
});
editor.render("order-text-reply");

// 工单回复验证
function orderSubmitClick() {
	var id = $('#button-order').val();
	// var content = $("#order-text-reply").val();
	var content = editor.getContent(); // 副文本编辑器
	var cc = $('#order-text-cc').val();
	if (id == '') {
		alert('请先选择工单！');
		return false;
	} 

	if (content == '') {
        alert('请先输入回复内容！');
		return false;
	}

	if (cc != '') {
		if ((cc.split('@').length-1) == 0) {
			alert('抄送邮件格式不正确！');
			return false;
		} else {
			if ((cc.split('@').length-1) > 1 && (cc.split(',').length-1) == 0) {
				alert('抄送邮件格式不正确！');
				return false;
			}
		}
	}

	return true;
}
</script>
<!-- binway数据交互 - end -->
@stop