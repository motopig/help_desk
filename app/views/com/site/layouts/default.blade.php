<!-- header -->
@include('com.site.layouts.header')
<!-- ./ header -->

@section('styles')
@parent
<style>
a {
    blr:expression(this.onFocus=this.blur())
}
</style>
@stop

@section('script')
@parent
<script>
    $('#element').tooltip('show');

    // 定时执行消息推送 - no - start
    var audio_status = '0'; // 提示音开关
    
    function message() {
        $.ajax({ // 一个Ajax过程 
            type: "post", // 以post方式与后台沟通 
            url: "{{ URL::to($com->path.'/site/work/messagework') }}", // 提交地址
            dataType: 'json', // 从后台返回的值以JSON方式解释
            data: 'csrf_token={{{ Session::getToken() }}}', // 发给后台的数据项, laravel验证post

            success: function(json) { // 如果调用ajax返回成功
                if (json.workCount > 0) {
                    $('#user-bell').show();
                    $('#user-work').show();
                    $('#user-badge').html(json.workCount);
                    $('#no-user-work').hide();

                    // 播放提示音
                    if ($('#h_audio').val() == '0' && audio_status == '0') {
                        // 适用于html5
                        $('<audio id="chatAudio"><source src="{{ asset("assets/audio/notify.mp3") }}" type="audio/mpeg"></audio>').appendTo('body');
                        $('#chatAudio')[0].play();
                        audio_status = '1';
                    }
                } else {
                    $('#user-bell').hide();
                    $('#user-work').hide();
                    $('#user-badge').html('');
                    $('#no-user-work').show();
                    audio_status = '0';
                }
            }
        });
    }

    if ($('#h_message').val() == '0') {
        // setInterval(message, 1000); // 测试
        // setInterval(message, 5000); // 正式
        // clearInterval(message);
    }
    // 定时执行消息推送 - no - end

    $(function(){
        $('.view-link').click(function(){

            $('#viewClientModal').modal();

            $.ajax({
                type: "GET",
                url: $(this).attr('href')
            }).done(function(html_form) {
                    $('#viewClientModalbody').html(html_form);
                    $('#viewClientModal').show();
                });

            return false;
        });
    });
</script>
@stop

<nav class="navbar navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ URL::to($com->path.'/') }}" title="首页" onFocus="this.blur()">edesk</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <!-- <a href="{{ URL::to($com->path.'/site/work/job') }}" onFocus="this.blur()">提交工单</a> -->

                    <!-- ECDO前台暂时邮件发送 - start -->
                    @if ($com->path == 'no')
                    <a href="mailto:service@no">提交工单</a>
                    @else
                    <a href="#">提交工单</a>
                    @endif
                    <!-- ECDO前台暂时邮件发送 - end -->
                </li>
                <!-- <li><a href="{{ URL::to($com->path.'/site/work') }}" onFocus="this.blur()">查看工单</a></li> -->
            </ul>

            @if (count(Auth::user()) > 0 && Auth::user()->firm)
            <input type="hidden" id="h_message" name="h_message" value="{{Auth::user()->message}}" />
            <input type="hidden" id="h_audio" name="h_audio" value="{{Auth::user()->audio}}" />

            <ul class="nav navbar-nav navbar-right navbar-user">

            <!-- 定时执行消息推送 - no - start -->
            <!-- <li class="dropdown user-dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span id="user-bell" style="display:none;" class="glyphicon glyphicon-bell"></span> 消息通知 
                <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                @if (Auth::user()->message == '0')
                <li id="user-work" style="display:none;">
                    <a href="{{ URL::to($com->path.'/site/work/getmessage') }}" class="view-link">
                    <span class="glyphicon glyphicon-envelope"></span> 工单通知 
                    <span id="user-badge" class="badge"></span>
                    </a>
                </li>
                <li id="no-user-work">
                    <a href="javascript:void(0);">
                    <span class="glyphicon glyphicon-ban-circle"></span> 暂无消息通知 
                    </a>
                </li>
                @elseif (Auth::user()->message == '1')
                <li>
                    <a href="{{ URL::to($com->path.'/apply') }}">
                    <span class="glyphicon glyphicon-remove-circle"></span> 消息通知未启用 
                    </a>
                </li>
                @endif
                </ul>
            </li> -->
            <!-- 定时执行消息推送 - no - end -->

            <li class="dropdown user-dropdown">
            <!-- 平台公司使用头像 - no - start -->
            <!-- @if (Auth::user()->head > 0)
            <a href="#" style="padding-top: 12px;" class="dropdown-toggle" data-toggle="dropdown">
            <img title="{{Auth::user()->name }}" src="{{ asset(Auth::user()->enclosures_path) }}" 
            style="border-radius: 50%;height: 23px;width: 23px;" />
            您好，{{{
                    Auth::user()->name }}} <b class="caret"></b>
            </a>
            @else
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-user" title="{{Auth::user()->name }}"></span>
            您好，{{{
                    Auth::user()->name }}} <b class="caret"></b></a>
            @endif -->
            <!-- 平台公司使用头像 - no - end -->
           <!--  <ul class="dropdown-menu">
            <li><a href="{{ URL::to($com->path.'/account') }}">
                <span class="glyphicon glyphicon-user"></span> 个人信息</a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ URL::to($com->path.'/apply') }}"><i class="glyphicon glyphicon-cog"></i> 应用设置</a>
            </li>
            <li class="divider"></li>
            <li><a href="{{ URL::to($com->path.'/logout') }}">
                <span class="glyphicon glyphicon-off"></span> 退出</a></li>
            </ul> -->
            </li>
            </ul>
            @else
            <!-- <input type="hidden" id="h_message" name="h_message" value="false" />
            <input type="hidden" id="h_audio" name="h_audio" value="false" />
            <ul class="nav navbar-nav navbar-right navbar-user">
            <li>
            <a href="{{ URL::to($com->path.'/login') }}" onFocus="this.blur()">
                <span class="glyphicon glyphicon-user"></span> 登入
            </a>
            </li>
            </ul> -->
            @endif

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
</nav>

<!-- Modal -->
<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">工单消息通知</h4>
            </div>
            <div class="modal-body" id="viewClientModalbody" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="container">

    <!-- Notifications -->
    <div class="hero-notifications">
    @include('com.notifications')
    </div>
    <!-- ./ notifications -->

    @yield('content')

    <span style="float:right;"><hr /></span>

    <!-- <footer>
        <div class="col-lg-12">
            <p>Copyright &copy; <a href="http://www.no" target="_blank">Ecdo.cc</a> &middot; 2013  </p>
        </div>
    </footer> -->

</div><!-- /.container -->

<!-- footer -->
@include('com.site.layouts.footer')
<!-- ./ footer -->
