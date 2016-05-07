@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
    /*.announcement-heading {font-size: 35px;}*/
    .form-control {
        background-color: #FFFFFF;
        background-image: none;
        border: 1px solid #CCCCCC;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
        color: #555555;
        display: block;
        font-size: 14px;
        height: 34px;
        line-height: 1.42857;
        padding: 6px 12px;
        transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
        vertical-align: left;
        width: 50%;
    }
    .col-sm-2 {
        width: 8%;
    }
    .imgdsb {
        padding-top: 5px;
    }
    .job_image img {
        height: 140px;
        width: 460px;
    }
    .job_image_logo img {
        height: 100px;
        width: 100px;
        border-radius: 50%;
    }
    .help-block {
        color: #B94A48;
        display: block;
        margin-bottom: 10px;
        margin-top: 5px;
    }
    a {
        blr:expression(this.onFocus=this.blur())
    }
    .logop {
        color: #999999;
        font-size: 12px;
    }
</style>
@stop

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('content')
<div class="row">

    <div class="col-lg-12">

        <!-- <h1>挂件设置
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config') }}">系统设置</a></li>
            <li class="active">挂件设置</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

    </div>
</div><!-- /.row -->

            <div class="row-fluid">
        <div class="span12">
            <div class="tabbable" id="tabs-729615">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#panel-724481" data-toggle="tab" onFocus="this.blur()">logo配置</a>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        首页配置<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#panel-772035" data-toggle="tab">图片设置 #1</a>
                            </li>
                            <li>
                                <a href="#panel-772036" data-toggle="tab">图片设置 #2</a>
                            </li>
                            <li>
                                 <a href="#panel-772037" data-toggle="tab">图片设置 #3</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="tab-content">
                    <br />

                    <div class="tab-pane fade in active" id="panel-724481">
                    <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />
                    <input type="hidden" name="clogo" value="clogo" />

                    <div class="form-horizontal">
                    <div class="form-group">
                    <label class="col-sm-2 control-label">公司logo</label>
                    <div class="col-sm-10">
                    <!-- <input type="logo" class="form-control" disabled="disabled" placeholder="Upload Image" 
                    name="logo" id="logo" value="{{$comData->enclosure_name}}"> -->
                    @if (!$comData->path)
                    <input type="logo" class="form-control" disabled="disabled" placeholder="Upload Image" 
                    name="logo" id="logo" value="">
                    @else
                    <span class="job_image_logo">
                        <img src="{{{ asset($comData->path) }}}" />
                    </span>
                    <br /><br />
                    @endif
                    <div class="controls">
                    <input type="file" name="file[]" id="file" value="" />
                    <p class="logop">你可以选择png/jpg/jpeg/gif/bmp图片(100*100)作为新logo</p>
                    </div>
                    </div>
                    </div>

                    <div class="form-group">
                    <label class="col-sm-2 control-label">公司简介</label>
                    <div class="col-sm-10">
                    <input type="brief" class="form-control" placeholder="Company Brief (限制30个字符)" 
                    name="brief" id="brief" value="{{$comData->brief}}">
                    <span class="help-block">{{{ $errors->first('brief') }}}</span>
                    </div>
                    </div>
                    </div>
                    
                    <div class="row">
                    <div class="col-lg-12">
                    <hr >
                    <button type="submit" class="btn btn-primary">保存配置</button>
                    </div>
                    </div>
                    </form>
                    </div>
                    
                    <div class="tab-pane fade" id="panel-772035">
                    <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" id="csrf_token" 
                            value="{{{ Session::getToken() }}}" />
                    <input type="hidden" name="imageOne" value="imageOne" />

                    <div class="form-horizontal">
                    <div class="form-group">
                    <label class="col-sm-2 control-label">图片 #1</label>
                    <div class="col-sm-10">
                    @if ($pendantOneData === 0)
                    <input type="hidden" name="pendantID" value="1" />
                    <input type="enclosures_id" class="form-control" placeholder="Upload Image" 
                    name="enclosures_id" id="enclosures_id" disabled="disabled" value="">
                    @else
                    <input type="enclosures_id" class="form-control" placeholder="Upload Image" 
                    name="enclosures_id" id="enclosures_id" disabled="disabled" 
                    value="{{$pendantOneData->onepath->enclosure_name}}">
                    <span class="job_image">
                        <img src="{{{ asset($pendantOneData->onepath->path) }}}" />
                    </span>
                    @endif
                    <div class="controls">
                    <p class="logop">你可以选择png/jpg/jpeg/gif/bmp图片(1140*350)作为首页图片</p>
                    <input type="file" name="file[]" id="file" value="" />
                    </div>
                    </div>
                    
                    </div>
                    <div class="form-group">
                    <label class="col-sm-2 control-label">内容简介</label>
                    <div class="col-sm-10"> 
                    @if ($pendantOneData === 0)
                    <input type="brief" class="form-control" placeholder="Brief (限制80个字符)" name="brief" id="brief" 
                    value="">
                    @else
                    <input type="brief" class="form-control" placeholder="Brief (限制80个字符)" name="brief" id="brief" 
                    value="{{$pendantOneData->brief}}">
                    @endif
                    </div>
                    </div>

                    <div class="form-group">
                    <label class="col-sm-2 control-label">站外链接</label>
                    <div class="col-sm-10">
                    @if ($pendantOneData === 0)
                    <input type="link" class="form-control" placeholder="Link (例：http://www.no)" 
                    name="link" id="link" value="">
                    @else
                    <input type="link" class="form-control" placeholder="Link (例：http://www.no)" 
                    name="link" id="link" value="{{$pendantOneData->link}}">
                    @endif
                    </div>
                    </div>
                    <div class="form-group">
                    <label class="col-sm-2 control-label">启用状态</label>
                    <div class="col-sm-10">
                    <div class="imgdsb">
                    @if ($pendantOneData === 0)
                    <input type="radio" name="disabled" value="false" checked="checked"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true"> 禁用
                    @elseif ($pendantOneData->disabled == 'false')
                    <input type="radio" name="disabled" value="false" checked="checked"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true"> 禁用
                    @else
                    <input type="radio" name="disabled" value="false"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true" checked="checked"> 禁用
                    @endif
                    </div>
                    </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-12">
                    <hr >
                    <button type="submit" class="btn btn-primary">保存配置</button>
                    </div>
                    </div>
                    </form>
                    </div>

                    <div class="tab-pane fade" id="panel-772036">
                    <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" id="csrf_token" 
                            value="{{{ Session::getToken() }}}" />
                    <input type="hidden" name="imageTwo" value="imageTwo" />

                    <div class="form-horizontal">
                    <div class="form-group">
                    <label class="col-sm-2 control-label">图片 #2</label>
                    <div class="col-sm-10">
                    @if ($pendantTwoData === 0)
                    <input type="hidden" name="pendantID" value="2" />
                    <input type="enclosures_id" class="form-control" placeholder="Upload Image" 
                    name="enclosures_id" id="enclosures_id" disabled="disabled" value="">
                    @else
                    <input type="enclosures_id" class="form-control" placeholder="Upload Image" 
                    name="enclosures_id" id="enclosures_id" disabled="disabled" 
                    value="{{$pendantTwoData->twopath->enclosure_name}}">
                    <span class="job_image">
                        <img src="{{{ asset($pendantTwoData->twopath->path) }}}" />
                    </span>
                    @endif
                    <div class="controls">
                    <p class="logop">你可以选择png/jpg/jpeg/gif/bmp图片(1140*350)作为首页图片</p>
                    <input type="file" name="file[]" id="file" value="" />
                    </div>
                    </div>
                    
                    </div>
                    <div class="form-group">
                    <label class="col-sm-2 control-label">内容简介</label>
                    <div class="col-sm-10"> 
                    @if ($pendantTwoData === 0)
                    <input type="brief" class="form-control" placeholder="Brief (限制80个字符)" name="brief" id="brief" 
                    value="">
                    @else
                    <input type="brief" class="form-control" placeholder="Brief (限制80个字符)" name="brief" id="brief" 
                    value="{{$pendantTwoData->brief}}">
                    @endif
                    </div>
                    </div>

                    <div class="form-group">
                    <label class="col-sm-2 control-label">站外链接</label>
                    <div class="col-sm-10">
                    @if ($pendantTwoData === 0)
                    <input type="link" class="form-control" placeholder="Link (例：http://www.no)" 
                    name="link" id="link" value="">
                    @else
                    <input type="link" class="form-control" placeholder="Link (例：http://www.no)" 
                    name="link" id="link" value="{{$pendantTwoData->link}}">
                    @endif
                    </div>
                    </div>
                    <div class="form-group">
                    <label class="col-sm-2 control-label">启用状态</label>
                    <div class="col-sm-10">
                    <div class="imgdsb">
                    @if ($pendantTwoData === 0)
                    <input type="radio" name="disabled" value="false" checked="checked"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true"> 禁用
                    @elseif ($pendantTwoData->disabled == 'false')
                    <input type="radio" name="disabled" value="false" checked="checked"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true"> 禁用
                    @else
                    <input type="radio" name="disabled" value="false"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true" checked="checked"> 禁用
                    @endif
                    </div>
                    </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-12">
                    <hr >
                    <button type="submit" class="btn btn-primary">保存配置</button>
                    </div>
                    </div>
                    </form>
                    </div>

                    <div class="tab-pane fade" id="panel-772037">
                    <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" id="csrf_token" 
                            value="{{{ Session::getToken() }}}" />
                    <input type="hidden" name="imageThree" value="imageThree" />

                    <div class="form-horizontal">
                    <div class="form-group">
                    <label class="col-sm-2 control-label">图片 #3</label>
                    <div class="col-sm-10">
                    @if ($pendantThreeData === 0)
                    <input type="hidden" name="pendantID" value="3" />
                    <input type="enclosures_id" class="form-control" placeholder="Upload Image" 
                    name="enclosures_id" id="enclosures_id" disabled="disabled" value="">
                    @else
                    <input type="enclosures_id" class="form-control" placeholder="Upload Image" 
                    name="enclosures_id" id="enclosures_id" disabled="disabled" 
                    value="{{$pendantThreeData->threepath->enclosure_name}}">
                    <span class="job_image">
                        <img src="{{{ asset($pendantThreeData->threepath->path) }}}" />
                    </span>
                    @endif
                    <div class="controls">
                    <p class="logop">你可以选择png/jpg/jpeg/gif/bmp图片(1140*350)作为首页图片</p>
                    <input type="file" name="file[]" id="file" value="" />
                    </div>
                    </div>
                    
                    </div>
                    <div class="form-group">
                    <label class="col-sm-2 control-label">内容简介</label>
                    <div class="col-sm-10"> 
                    @if ($pendantThreeData === 0)
                    <input type="brief" class="form-control" placeholder="Brief (限制80个字符)" name="brief" id="brief" 
                    value="">
                    @else
                    <input type="brief" class="form-control" placeholder="Brief (限制80个字符)" name="brief" id="brief" 
                    value="{{$pendantThreeData->brief}}">
                    @endif
                    </div>
                    </div>

                    <div class="form-group">
                    <label class="col-sm-2 control-label">站外链接</label>
                    <div class="col-sm-10">
                    @if ($pendantThreeData === 0)
                    <input type="link" class="form-control" placeholder="Link (例：http://www.no)" 
                    name="link" id="link" value="">
                    @else
                    <input type="link" class="form-control" placeholder="Link (例：http://www.no)" 
                    name="link" id="link" value="{{$pendantThreeData->link}}">
                    @endif
                    </div>
                    </div>
                    <div class="form-group">
                    <label class="col-sm-2 control-label">启用状态</label>
                    <div class="col-sm-10">
                    <div class="imgdsb">
                    @if ($pendantThreeData === 0)
                    <input type="radio" name="disabled" value="false" checked="checked"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true"> 禁用
                    @elseif ($pendantThreeData->disabled == 'false')
                    <input type="radio" name="disabled" value="false" checked="checked"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true"> 禁用
                    @else
                    <input type="radio" name="disabled" value="false"> 启用&nbsp;
                    <input type="radio" name="disabled" value="true" checked="checked"> 禁用
                    @endif
                    </div>
                    </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-12">
                    <hr >
                    <button type="submit" class="btn btn-primary">保存配置</button>
                    </div>
                    </div>
                    </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@stop