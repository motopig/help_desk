@extends('com.site.layouts.default')

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

@section('styles')
@parent
<style>
.pull-right {
    float: right;
}
.label {
    border-radius: 10px;
    color: #FFFFFF;
    display: inline;
    font-size: 75%;
    font-weight: bold;
    line-height: 1;
    padding: 0.2em 0.6em 0.3em;
    text-align: center;
    vertical-align: baseline;
    white-space: nowrap;
}
a {
    blr:expression(this.onFocus=this.blur())
}
/*userlogo*/
.avatar {
    border-radius: 50%;
    height: 26px;
    width: 26px;
    display: inline-block;
    line-height: 1;
}
.media-object {
    border-radius: 50%;
}
.text-danger {
    resize: none;
    overflow: hidden;
    min-height: 100px;
}
.panel-group .panel {
    border-radius: 20px;
    margin-bottom: 0;
    overflow: hidden;
}
/*上传附件样式*/
img.uplode-image-file {
    border: medium none;
    height: 34px;
}
.conterfilename-font {
    color: #84A099;
}
</style>
@stop

@section('script')
@parent
<script>
// textarea自适应高度
$(function() {
    $("#text-content").bind("keydown keyup",function() {
        $(this).autosize();
    }).show().autosize();
});
$.fn.autosize = function() {
    $(this).height('0px');
    var setheight = $(this).get(0).scrollHeight;
    if($(this).attr("_height") != setheight)
        $(this).height(setheight+"px").attr("_height",setheight);
    else
        $(this).height($(this).attr("_height")+"px");
}

// 获取上传附件
$(function() {
    // 监听上传附件动作
    $("#conterfile").change(function() {
        // 获取文件名并显示
        var fileName = this.value.substring(this.value.lastIndexOf("\\")+1);
        $("#attachment").show();
        $("#conterfilename").html(fileName);

        // 获取后缀名并显示图片
        var suffix = this.value.substr(this.value.indexOf("."));
        if (suffix == '.docx') {
            $("#file_docx").show();
            $("#file_xlsx").hide();
            $("#file_txt").hide();
        } else if (suffix == '.xlsx') {
            $("#file_xlsx").show();
            $("#file_docx").hide();
            $("#file_txt").hide();
        } else if (suffix == '.txt') {
            $("#file_txt").show();
            $("#file_docx").hide();
            $("#file_xlsx").hide();
        } else {
            $("#file_docx").hide();
            $("#file_xlsx").hide();
            $("#file_txt").hide();
        }
    });
});

// 移除上传附件
function linkCancel() {
    var file = $("#conterfile");
    if (file.outerHTML) { // for IE, Opera, Safari, Chrome
        file.outerHTML = file.outerHTML;
    } else { // for FF
        file.value = "";
    }

    $("#file_docx").hide();
    $("#file_xlsx").hide();
    $("#file_txt").hide();
    $("#attachment").hide();
}
</script>
@stop

@section('content')
<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
<!-- CSRF Token -->
<input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />
<div class="row">
<div class="col-lg-12">
<h1>提交工单</h1>
<ol class="breadcrumb">
    <li><span class="glyphicon glyphicon-home"></span> <a href="{{ URL::to($com->path.'/') }}">首页</a></li>
    <li class="active">提交工单</li>
</ol>

<div class="form-group col-lg-5">
    &nbsp;&nbsp;<label for="exampleInputTitle">工单标题</label>
    <input style="border-radius:20px;" type="text" class="form-control" name="title" placeholder="请输入工单标题 (限制20个字符)">
    {{ $errors->first('title') }}
</div>

<div class="form-group col-lg-5" style="margin-left:8px;">
    <label for="exampleInputLevel">工单级别</label>
    <span class="label label-info">一般</span>
    <span class="label label-warning">中等</span>
    <span class="label label-danger">紧急</span>
    <select name="work_level" class="form-control">
      <option value="1" selected="selected">一般</option>
      <option value="2">中等</option>
      <option value="3">紧急</option>
    </select>
</div>
</div>
</div>

<!-- 工单提交信息 -->
<div style="width:80%;">
<textarea name="ask" class="form-control text-danger" id="text-content" placeholder="点击发表评论"></textarea>
<div>
<a href="javascript:;" class="add-attachment toolbar-item" onFocus="this.blur()" unselectable="on" 
tabindex="-1" style="position: relative; overflow: hidden; direction: ltr; float:right; left: -15px; top: -50px;">
<br /><span class="glyphicon glyphicon-paperclip" title="添加附件"></span>
<input id="conterfile" type="file" multiple="multiple" title="添加附件" name="file" 
tabindex="-1" style="position: absolute; right: 0px; top: 0px; font-family: Arial; font-size: 118px; margin: 
0px; padding: 0px; cursor: pointer; opacity: 0;" />
</a>
</div>
</div>
<!-- 附件上传显示内容 -->
<div id="attachment" style="display:none;width:80%;">
<span id="file_docx" style="display:none;"><img class="uplode-image-file" src="{{ asset('assets/img/file_docx.png') }}"></span>
<span id="file_xlsx" style="display:none;"><img class="uplode-image-file" src="{{ asset('assets/img/file_xlsx.png') }}"></span>
<span id="file_txt" style="display:none;"><img class="uplode-image-file" src="{{ asset('assets/img/file_txt.png') }}"></span>
<span id="conterfilename" class="conterfilename-font"></span>
<a href="javascript:;" onclick="linkCancel()">
<span title="移除附件" class="glyphicon glyphicon-remove"></span>
</a>
</div>
<br />
<!-- 附件上传显示内容 -->
<!-- 工单提交信息 -->
<div class="row">
<div class="col-lg-10">
<button type="submit" class="btn btn-primary">提交工单</button>
<a onFocus="this.blur()" href="{{ URL::to($com->path.'/') }}">
<button type="button" class="btn btn-default">取消返回</button>
</a>
</div>
</div>
</form>
@stop
