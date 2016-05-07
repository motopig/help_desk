<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @section('title')
        {{ $com->company_name }} :: 工单系统
        @show
    </title>

    <!-- css cdn -->
    <link href="{{{ asset('assets/bootcss/bootstrap.min.css') }}}" rel="stylesheet">

    <!-- CSS
    ================================================== -->
    <!--<link href="{{{ asset('assets/css/bootstrap.min.css') }}}" rel="stylesheet">-->

    @yield('styles_src')

    @section('styles')
    <style>
        body {
            margin-top: 50px;
            background: url("{{{ asset('assets/img/bg.png') }}}") no-repeat center center;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .hero-notifications {
            margin-top: 10px;
        }

        .hero-spacer {
            margin-top: 30px;
        }

        .hero-feature {
            margin-bottom: 30px;
        }

        footer {
            margin: 50px 0;
        }

        /*导航半透明*/
        .navbar-inverse {
            background: none repeat scroll 0 0 rgba(0, 0, 0, 0.75);
        }

        .containers {
            width: 380px;
            margin-left: 425px;
            margin-top: 150px;
            background-color: #ffffff;
            padding: 30px;
            border:1px solid #cccccc;
            border-radius:5px;
            -moz-border-radius:5px;
        }

        .pth {
            margin-left: 14px;
        }

        a {
            blr:expression(this.onFocus=this.blur())
        }
    </style>
    @show

</head>

<body>
<nav class="navbar navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ URL::to($com->path.'/') }}" onFocus="this.blur()">edesk</a>
        </div>

        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{ URL::to($com->path.'/site/work/job') }}" onFocus="this.blur()">提交工单</a></li>
                <li><a href="{{ URL::to($com->path.'/site/work') }}" onFocus="this.blur()">查看工单</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right navbar-user">
            <li>
            <a href="{{ URL::to($com->path.'/login') }}">
                <span class="glyphicon glyphicon-user"></span> 登入
            </a>
            </li>
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
</nav>

<div id="top" class="header">
    <div class="vert-text">
        <div class="containers">

            <h1 class="pth">工单系统前台登录</h1>

            <hr />

            <form class="form-horizontal" role="form" method="post">

                <!-- Notifications -->
                @include('root.notifications')
                <!-- ./ notifications -->

                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

                <div class="form-group {{{ $errors->has('email') ? 'has-error' : '' }}}">
                    <label for="email" class="col-sm-2 control-label">邮箱</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" placeholder="Email" name="email" id="email" value="{{{ Input::old('email') }}}" />

                        <span class="help-block">{{{ $errors->first('email') }}}</span>
                    </div>
                </div>

                <div class="form-group {{{ $errors->has('password') ? 'has-error' : '' }}}">

                    <label for="password" class="col-sm-2 control-label">密码</label>

                    <div class="col-sm-9">
                        <input type="password" class="form-control"  placeholder="Password" name="password" id="password" value="" />

                        <span class="help-block">{{{ $errors->first('password') }}}</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-9">
                        <button type="submit" class="btn btn-primary btn-block">登录</button>
                        <br />
                        <a style="text-decoration:none;" onFocus="this.blur()" href="{{ URL::to($com->path.'/') }}">
                        <button type="button" class="btn btn-warning btn-block">返回</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{{ asset('assets/bootcss/jquery.min.js') }}}"></script>
<script src="{{{ asset('assets/bootcss/bootstrap.min.js') }}}"></script>
<!-- Page Specific Plugins -->
<script src="{{{ asset('assets/js/raphael-min.js') }}}"></script>
<!--<script src="{{{ asset('assets/js/morris/morris-0.4.3.min.js') }}}"></script>
<script src="{{{ asset('assets/js/morris/chart-data-morris.js') }}}"></script>-->
<script src="{{{ asset('assets/js/tablesorter/jquery.tablesorter.js') }}}"></script>
<script src="{{{ asset('assets/js/tablesorter/tables.js') }}}"></script>

</body>
</html>