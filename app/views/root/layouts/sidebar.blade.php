<!-- Sidebar -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ URL::to('root') }}">ECDO支持系统管理后台</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li {{{ (Request::is('root') ? 'class=active' : '') }}}><a href="{{ URL::to('root') }}">首页</a></li>
            <li {{{ (Request::is('root/company*') ? 'class=active' : '') }}}><a href="{{ URL::to('root/company') }}">公司管理</a></li>
            <li {{{ (Request::is('root/user*') ? 'class=active' : '') }}}><a href="{{ URL::to('root/user') }}">用户管理</a></li>
            <li {{{ (Request::is('root/report*') ? 'class=active' : '') }}}><a href="{{ URL::to('root/report') }}">报表统计</a></li>
            <li {{{ (Request::is('root/config*') ? 'class=active' : '') }}}><a href="{{ URL::to('root/config') }}">系统设置</a></li>
        </ul>

        <ul class="nav navbar-nav navbar-right navbar-user">
            <!--
            <li class="dropdown messages-dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i> Messages <span
                        class="badge">7</span> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li class="dropdown-header">7 New Messages</li>
                    <li class="message-preview">
                        <a href="#">
                            <span class="avatar"><img src="http://placehold.it/50x50"></span>
                            <span class="name">John Smith:</span>
                            <span class="message">Hey there, I wanted to ask you something...</span>
                            <span class="time"><i class="fa fa-clock-o"></i> 4:34 PM</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="message-preview">
                        <a href="#">
                            <span class="avatar"><img src="http://placehold.it/50x50"></span>
                            <span class="name">John Smith:</span>
                            <span class="message">Hey there, I wanted to ask you something...</span>
                            <span class="time"><i class="fa fa-clock-o"></i> 4:34 PM</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="message-preview">
                        <a href="#">
                            <span class="avatar"><img src="http://placehold.it/50x50"></span>
                            <span class="name">John Smith:</span>
                            <span class="message">Hey there, I wanted to ask you something...</span>
                            <span class="time"><i class="fa fa-clock-o"></i> 4:34 PM</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="#">View Inbox <span class="badge">7</span></a></li>
                </ul>
            </li>
            -->

            <li class="dropdown user-dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> 您好，{{{
                    Auth::user()->name }}} <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::to('root/account') }}"><i class="fa fa-user"></i> 个人资料</a></li>
<!--                    <li><a href="#"><i class="fa fa-envelope"></i> Inbox <span class="badge">7</span></a></li>-->
<!--                    <li><a href="#"><i class="fa fa-gear"></i> 设置</a></li>-->
                    <li class="divider"></li>
                    <li><a href="{{ URL::to('root/logout') }}"><i class="fa fa-power-off"></i> 退出</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>
