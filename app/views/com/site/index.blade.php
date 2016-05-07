@extends('com.site.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('styles')
@parent
<style>
a {
    blr:expression(this.onFocus=this.blur())
}
/*提示信息*/
.alert {
    padding: 8px 35px 8px 14px;
    margin-bottom: 20px;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
    background: none repeat scroll 0 0 #E3E5CF;
}
.alert, .alert h4 {
    color: #456B6A;
}
/*图片高宽*/
.carousel-inner .item {
    height: 350px;
    /*width: 1140px;*/
}

.carousel-inner .item img {
    height: 100%;
    width: 100%;
}
.navbar-brand-logo {
    position: absolute;
    top: 65px;
    right: 180px;
}
/*logo高宽*/
.navbar-brand-image-logo {
    height: 85px;
    /*width: 200px;*/
    border-radius: 50%;
}
.carousel-caption {
    background: none repeat scroll 0 0 rgba(0, 0, 0, 0.75);
    bottom: 0;
    left: 0;
    padding: 7px;
    position: absolute;
    right: 0;
}
.carousel-indicators {
    list-style: none outside none;
    position: absolute;
    top: 15px;
    z-index: 5;
}
</style>
@stop

@section('script')
@parent
<script>
    $('.carousel').carousel({
        interval: 5000
    });

    // 工具提示
    function toolTip() {
        $('#logo-image').tooltip();
    }
</script>
@stop

@section('content')

@if ($pendantData['count'] == 0)
<div class="jumbotron hero-spacer">
    <h1>{{ $com->company_name }} <b>客户支持系统</b></h1>

    <p>edesk</p>

    <p>
        <!-- <a class="btn btn-primary btn-large" href="{{ URL::to($com->path.'/site/work/job') }}">提交工单</a> -->
        <!-- <a class="btn btn-success btn-large" href="{{ URL::to($com->path.'/site/work') }}">查看工单</a> -->

        <!-- ECDO前台暂时邮件发送 - start -->
        @if ($com->path == 'no')
        <a class="btn btn-primary btn-large" href="mailto:service@no">提交工单</a>
        @else
        <a class="btn btn-primary btn-large" href="#">提交工单</a>
        @endif
        <!-- ECDO前台暂时邮件发送 - end -->
    </p>
</div>
@else
<div class="row-fluid">
    <div class="span12">
        <div class="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span class="glyphicon glyphicon-remove-circle"></span>
            </button>
            <h4>
                {{ $logos['company_name'] }} <b>客户支持系统</b>
            </h4>
            <p>{{ $logos['brief'] }}</p>
            <p>
                <!-- <a class="btn btn-primary btn-large" href="{{ URL::to($com->path.'/site/work/job') }}">提交工单</a> -->
                <!-- <a class="btn btn-success btn-large" href="{{ URL::to($com->path.'/site/work') }}">查看工单</a> -->

                <!-- ECDO前台暂时邮件发送 - start -->
                @if ($com->path == 'no')
                <a class="btn btn-primary btn-large" href="mailto:service@no">提交工单</a>
                @else
                <a class="btn btn-primary btn-large" href="#">提交工单</a>
                @endif
                <!-- ECDO前台暂时邮件发送 - end -->
            </p>
            @if ($logos)
            <p class="navbar-brand-logo">
            <img class="navbar-brand-image-logo" id="logo-image" src="{{ URL::to($logos['url']) }}"
            data-original-title="{{$com->company_name}}" data-placement="bottom" onmouseover="toolTip()">
            </p>
            @endif
        </div>
    </div>
</div>

<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        @if ($pendantData['count'] == 3)
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
        @elseif ($pendantData['count'] == 2)
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        @else
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        @endif
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
        @if ($pendantData['count'] == 3)
        <div class="item active">
            <img alt="" src="{{ URL::to($pendantData['one']->paths) }}">
            
            <a target="_blank" href="{{$pendantData['one']->link}}">
            <div class="carousel-caption">
                <p>
                    @if ($pendantData['one']->brief)
                    {{$pendantData['one']->brief}}
                    @else
                    <br />
                    @endif
                </p>
            </div>
            </a>
        </div>
        <div class="item">
            <img alt="" src="{{ URL::to($pendantData['two']->paths) }}">
            
            <a target="_blank" href="{{$pendantData['two']->link}}">
            <div class="carousel-caption">
                <p>
                    @if ($pendantData['two']->brief)
                    {{$pendantData['two']->brief}}
                    @else
                    <br />
                    @endif
                </p>
            </div>
            </a>
        </div>
        <div class="item">
            <img alt="" src="{{ URL::to($pendantData['three']->paths) }}">
            
            <a target="_blank" href="{{$pendantData['three']->link}}">
            <div class="carousel-caption">
                <p>
                    @if ($pendantData['three']->brief)
                    {{$pendantData['three']->brief}}
                    @else
                    <br />
                    @endif
                </p>
            </div>
            </a>
        </div>
        @elseif ($pendantData['count'] == 2)
        <div class="item active">
            <img alt="" src="{{ URL::to($pendantData['one']->paths) }}">
            
            <a target="_blank" href="{{$pendantData['one']->link}}">
            <div class="carousel-caption">
                <p>
                    @if ($pendantData['one']->brief)
                    {{$pendantData['one']->brief}}
                    @else
                    <br />
                    @endif
                </p>
            </div>
            </a>
        </div>
        <div class="item">
            <img alt="" class="img-responsive" src="{{ URL::to($pendantData['two']->paths) }}">
            
            <a target="_blank" href="{{$pendantData['two']->link}}">
            <div class="carousel-caption">
                <p>
                    @if ($pendantData['two']->brief)
                    {{$pendantData['two']->brief}}
                    @else
                    <br />
                    @endif
                </p>
            </div>
            </a>
        </div>
        @else
        <div class="item active">
            <img alt="" src="{{ URL::to($pendantData['one']->paths) }}">

            <a target="_blank" href="{{$pendantData['one']->link}}">
            <div class="carousel-caption">
                <p>
                    @if ($pendantData['one']->brief)
                    {{$pendantData['one']->brief}}
                    @else
                    <br />
                    @endif
                </p>
            </div>
            </a>
        </div>
        @endif
    </div>
</div>
@endif
@stop
