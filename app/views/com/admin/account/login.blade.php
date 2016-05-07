@section('styles')
@parent
<style>
    /* Global Styles */

    html,
    body {
        height: 100%;
        width: 100%;
        margin: 0;
    }

    .container {
        width: 380px;
        height: auto;
        background-color: #ffffff;
        padding: 30px;
        border:1px solid #cccccc;
        border-radius:5px;
        -moz-border-radius:5px;
    }

    .vert-text {
        display: table-cell;
        vertical-align: middle;
        text-align: center;
    }

    .vert-text h1 {
        padding: 0;
        margin: 0;
    }

    /* Side Menu */

    #sidebar-wrapper {
        margin-right: -250px;
        right: 0;
        width: 250px;
        background: #000;
        position: fixed;
        height: 100%;
        overflow-y: auto;
        z-index: 1000;
        -webkit-transition: all 0.4s ease 0s;
        -moz-transition: all 0.4s ease 0s;
        -ms-transition: all 0.4s ease 0s;
        -o-transition: all 0.4s ease 0s;
        transition: all 0.4s ease 0s;
    }

    .sidebar-nav {
        position: absolute;
        top: 0;
        width: 250px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .sidebar-nav li {
        line-height: 40px;
        text-indent: 20px;
    }

    .sidebar-nav li a {
        color: #999999;
        display: block;
        text-decoration: none;
    }

    .sidebar-nav li a:hover {
        color: #fff;
        background: rgba(255,255,255,0.2);
        text-decoration: none;
    }

    .sidebar-nav li a:active,
    .sidebar-nav li a:focus {
        text-decoration: none;
    }

    .sidebar-nav > .sidebar-brand {
        height: 55px;
        line-height: 55px;
        font-size: 18px;
    }

    .sidebar-nav > .sidebar-brand a {
        color: #999999;
    }

    .sidebar-nav > .sidebar-brand a:hover {
        color: #fff;
        background: none;
    }

    #menu-toggle {
        top: 0;
        right: 0;
        position: fixed;
        z-index: 1;
    }

    #sidebar-wrapper.active {
        right: 250px;
        width: 250px;
        -webkit-transition: all 0.4s ease 0s;
        -moz-transition: all 0.4s ease 0s;
        -ms-transition: all 0.4s ease 0s;
        -o-transition: all 0.4s ease 0s;
        transition: all 0.4s ease 0s;
    }

    .toggle {
        margin: 5px 5px 0 0;
    }

    /* Full Page Image Header Area */

    .header {
        display: table;
        height: 100%;
        width: 100%;
        position: relative;
        background: url("{{{ asset('assets/img/bga.jpg') }}}") no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
@stop

@section('script')
@parent
<script>
    $("#menu-close").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
</script>
<script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
</script>
<script>
    $(function() {
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
                || location.hostname == this.hostname) {

                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            }
        });
    });
</script>
@stop

<!-- title -->
@section('title')
{{ $com->company_name }} :: @parent
@stop
<!-- ./title -->

<!-- header -->
@include('com.admin.layouts.header')
<!-- ./ header -->

<!-- Side Menu -->
<!-- <a id="menu-toggle" href="#" class="btn btn-primary btn-lg toggle"><i class="fa fa-reorder"></i></a> -->
<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <a id="menu-close" href="#" class="btn btn-default btn-lg pull-right toggle"><i class="fa fa-times"></i></a>
        <!-- <li class="sidebar-brand"><a href="{{ URL::to('/') }}">平台首页</a></li>
        <li><a href="{{ URL::to( $com->path ) }}">公司首页</a></li> -->
        <li class="sidebar-brand"><a href="{{ URL::to( $com->path ) }}">工单平台首页</a></li>
    </ul>
</div>
<!-- /Side Menu -->

<!-- Full Page Image Header Area -->
<div id="top" class="header">
    <div class="vert-text">
        <div class="container">

            <h1>工单系统后台登录</h1>

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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Full Page Image Header Area -->

<!-- footer -->
@include('com.admin.layouts.footer')
<!-- ./ footer -->




