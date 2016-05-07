<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @section('title')
        ECDO支持系统 :: 管理后台
        @show
    </title>

    <!-- css cdn -->
    <link href="http://cdn.bootcss.com/twitter-bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS
    ================================================== -->
    <!--<link href="{{{ asset('assets/css/bootstrap.min.css') }}}" rel="stylesheet">-->

    <!-- Add custom CSS here -->
    <link href="{{{ asset('assets/css/admin.css') }}}" rel="stylesheet">
    <link rel="stylesheet" href="{{{ asset('assets/font-awesome/css/font-awesome.min.css') }}}">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="{{{ asset('assets/css/morris-0.4.3.min.css') }}}">

    @yield('styles_src')


    @section('styles')
    <style>

    </style>
    @show


    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body>
