@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
    /*.announcement-heading {font-size: 35px;}*/
    pre {
        background: none;
        border: none;
    }
</style>
@stop

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('script')
@parent
<script>
$(document).ready(function() {
    if ('{{{$admin->enable}}}' == 'content') {
        $("#content").attr("checked","checked");
    } else if ('{{{$admin->enable}}}' == 'stop') {
        $("#stop").attr("checked","checked");
    }

    if ('{{{$admin->message}}}' == '0') {
        $("#tmessage").attr("checked","checked");
    } else if ('{{{$admin->message}}}' == '1') {
        $("#fmessage").attr("checked","checked");
    }

    if ('{{{$admin->audio}}}' == '0') {
        $("#taudio").attr("checked","checked");
    } else if ('{{{$admin->audio}}}' == '1') {
        $("#faudio").attr("checked","checked");
    }
});
</script>
@stop

@section('content')
<div class="row">

    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li class="active">应用设置</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />
            <input type="hidden" name="aid" id="aid" value="{{{ $admin->id }}}" />
            <div class="form-group">
                <label for="enable" class="col-sm-2 control-label">邮件推送</label>
                <div class="col-sm-10">
                    <!-- <input id="content" type="radio" name="enable" value="content" /> &nbsp;接收工单内容及统计通知<br /> -->
                    <input id="content" type="radio" name="enable" value="content" /> &nbsp;接收工单内容通知<br />
                    <input id="stop" type="radio" name="enable" value="stop" /> &nbsp;不接收任何工单通知
                    <hr>
                </div>
            </div>

            <div class="form-group">
                <label for="enable" class="col-sm-2 control-label">消息通知</label>
                <div class="col-sm-10">
                    <input id="tmessage" type="radio" name="message" value="0" /> &nbsp;接收消息通知<br />
                    <input id="fmessage" type="radio" name="message" value="1" /> &nbsp;不接收消息通知
                    <hr>
                </div>
            </div>

            <div class="form-group">
                <label for="enable" class="col-sm-2 control-label">提示音</label>
                <div class="col-sm-10">
                    <input id="taudio" type="radio" name="audio" value="0" /> &nbsp;开启<br />
                    <input id="faudio" type="radio" name="audio" value="1" /> &nbsp;关闭
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <hr>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">配置</button>

                    <a href="{{ URL::to($com->path.'/admin') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>


</div><!-- /.row -->
@stop