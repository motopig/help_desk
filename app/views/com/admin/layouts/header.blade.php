<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @section('title')
        工单系统 :: 管理后台
        @show
    </title>

    <script src="{{{ asset('assets/bootcss/jquery.min.js') }}}"></script>
    <script charset="utf-8" src="{{{ asset('assets/js/jquery.timeago.js') }}}"></script>
    <script charset="utf-8" src="{{{ asset('assets/js/jquery.timeago.zh-CN.js') }}}"></script>
    <script src="{{{ asset('assets/js/multiple-file-upload/jquery.MultiFile.js') }}}"></script>

    <!-- ueditor配置文件 -->
    <script type="text/javascript" src="{{{ asset('assets/ueditor/ueditor.config.js') }}}"></script>
    <!-- ueditor编辑器源码文件 -->
    <script type="text/javascript" src="{{{ asset('assets/ueditor/ueditor.all.js') }}}"></script>
    <!-- ueditor语言包文件(建议手动加载语言包，避免在ie下，因为加载语言失败导致编辑器加载失败) -->
    <script type="text/javascript" src="{{{ asset('assets/ueditor/lang/zh-cn/zh-cn.js') }}}"></script>

    <!-- css cdn -->
    <link href="{{{ asset('assets/bootcss/bootstrap.min.css') }}}" rel="stylesheet">

    <!-- CSS
    ================================================== -->
    <!--<link href="{{{ asset('assets/css/bootstrap.min.css') }}}" rel="stylesheet">-->

    <!-- Add custom CSS here -->
    <link href="{{{ asset('assets/css/admin.css') }}}" rel="stylesheet">
    <link rel="stylesheet" href="{{{ asset('assets/font-awesome/css/font-awesome.min.css') }}}">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="{{{ asset('assets/css/morris-0.4.3.min.css') }}}">
    <link rel="shortcut icon" href="{{{ asset('/favicon.ico') }}}">
    @yield('styles_src')

    @section('styles')
    <style>
        /*导航半透明 - no*/
        .navbar-inverse {
            background: none repeat scroll 0 0 rgba(0, 0, 0, 0.75);
        }

        /*使用帮助*/
        #link-feedback {
            background: none repeat scroll 0 0 #6A8B82;
            border-radius: 2px 0 0 2px;
            box-shadow: 1px 2px 8px rgba(80, 50, 0, 0.2);
            color: #FFFFFF;
            display: block;
            font-size: 12px;
            right: 0;
            padding: 10px 18px 10px 10px;
            position: fixed;
            top: 181px;
            transition: left 100ms ease-in-out 0s;
            width: 10px;
            z-index: 9990;
        }
        /*返回顶部*/
        #link-topback {
            background: none repeat scroll 0 0 #6A8B82;
            border-radius: 2px 0 0 2px;
            box-shadow: 1px 2px 8px rgba(80, 50, 0, 0.2);
            color: #FFFFFF;
            display: block;
            font-size: 12px;
            right: 0;
            padding: 10px 18px 10px 10px;
            position: fixed;
            top: 248px;
            width: 10px;
            z-index: 9990;
        }
        a {
            blr:expression(this.onFocus=this.blur())
        }
    </style>
    @show

    <!-- <a id="link-feedback" data-url="" href="javascript:;" style="text-decoration:none;" onFocus="this.blur()">
    <span class="glyphicon glyphicon-question-sign"></span>
    <br />
    帮助
    </a>
    <a id="link-topback" href='#top' style="text-decoration:none;" onFocus="this.blur()">
        <span class="glyphicon glyphicon-plane"></span>
    </a> -->

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body style="width:100%;">
