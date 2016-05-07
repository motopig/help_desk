@extends('com.admin.layouts.default')

@section('title')
{{ $com->company_name }} :: @parent
@stop

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-warning">提示：没有可用邮箱，请先
  			<a href="{{ URL::to($com->path.'/admin/config/mail') }}">配置启用邮件服务器</a> ！
  		</div>
	</div>
</div>
@stop
