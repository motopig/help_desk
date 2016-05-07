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
    // $('#element').tooltip('show');

    // // 定时执行消息推送 - no - start
    // var audio_status = '0'; // 提示音开关
    
    // function message() {
    //     $.ajax({ // 一个Ajax过程 
    //         type: "post", // 以post方式与后台沟通 
    //         url: "{{ URL::to($com->path.'/admin/work/messagework') }}", // 提交地址
    //         dataType: 'json', // 从后台返回的值以JSON方式解释
    //         data: 'csrf_token={{{ Session::getToken() }}}', // 发给后台的数据项, laravel验证post

    //         success: function(json) { // 如果调用ajax返回成功
    //             if (json.workCount > 0) {
    //                 $('#user-bell').show();
    //                 $('#user-work').show();
    //                 $('#user-badge').html(json.workCount);
                    
    //                 // 播放提示音
    //                 if ($('#h_audio').val() == '0' && audio_status == '0') {
    //                     // 适用于html5
    //                     $('<audio id="chatAudio"><source src="{{ asset("assets/audio/notify.mp3") }}" type="audio/mpeg"></audio>').appendTo('body');
    //                     $('#chatAudio')[0].play();
    //                     audio_status = '1';
    //                 }
    //             }

    //             if (json.workCountOne > 0) {
    //                 $('#user-bell').show();
    //                 $('#user-work-me').show();
    //                 $('#user-badge-me').html(json.workCountOne);
                    
    //                 // 播放提示音
    //                 if ($('#h_audio').val() == '0' && audio_status == '0') {
    //                     // 适用于html5
    //                     $('<audio id="chatAudio"><source src="{{ asset("assets/audio/notify.mp3") }}" type="audio/mpeg"></audio>').appendTo('body');
    //                     $('#chatAudio')[0].play();
    //                     audio_status = '1';
    //                 }
    //             }
    //         }
    //     });
    // }

    // if ($('#h_message').val() == '0') {
    //     // setInterval(message, 1000); // 测试
    //     setInterval(message, 5000); // 正式
    //     clearInterval(message);
    // }
    // // 定时执行消息推送 - no - end

    // $(function(){
    //     $('.view-link-group').click(function(){

    //         $('#viewClientModal').modal();

    //         $.ajax({
    //             type: "GET",
    //             url: $(this).attr('href')
    //         }).done(function(html_form) {
    //                 $('#viewClientModalbody').html(html_form);
    //                 $('#viewClientModal').show();
    //             });

    //         return false;
    //     });

    //     $('.view-link-me').click(function(){

    //         $('#viewClientModalMe').modal();

    //         $.ajax({
    //             type: "GET",
    //             url: $(this).attr('href')
    //         }).done(function(html_form) {
    //                 $('#viewClientModalbodyMe').html(html_form);
    //                 $('#viewClientModalMe').show();
    //             });

    //         return false;
    //     });
    // });
</script>
@stop

<!-- Sidebar -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <a class="navbar-brand" href="#">edesk</a>
        <a class="navbar-brand" href="#">{{ $com->company_name }} 客户支持系统</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li
            {{{ (Request::is($com->path . '/admin') ? 'class=active' : '') }}}>
            <a href="{{ URL::to($com->path.'/admin') }}">
            <span class="glyphicon glyphicon-home"></span>&nbsp; 首页
            </a></li>

            <!-- binway工单定制 - no - start -->
            <li
            {{{ (Request::is($com->path . '/admin/order*') ? 'class=active' : '') }}}>
            <!-- <a href="{{ URL::to($com->path.'/admin/work') }}"> -->
            <a href="{{ URL::to($com->path.'/admin/order') }}">
            <span class="glyphicon glyphicon-tasks"></span>&nbsp; 工单
            </a></li>
            <!-- binway工单定制 - no - end -->

            <!-- <li
            {{{ (Request::is($com->path . '/admin/work*') ? 'class=active' : '') }}}><a href="{{ URL::to($com->path.'/admin/work') }}">工单管理</a></li> -->

            <li
            {{{ (Request::is($com->path . '/admin/client*') ? 'class=active' : '') }}}>
            <a href="{{ URL::to($com->path.'/admin/client') }}">
            <span class="glyphicon glyphicon-globe"></span>&nbsp; 客户
            </a></li>

            <li
            {{{ (Request::is($com->path . '/admin/user*') ? 'class=active' : '') }}}>
            <a href="{{ URL::to($com->path.'/admin/user') }}">
            <span class="glyphicon glyphicon-user"></span>&nbsp; 客服
            </a></li>

            @if ($level < 3)
            <li
            {{{ (Request::is($com->path . '/admin/group*') ? 'class=active' : '') }}}>
            <a href="{{ URL::to($com->path.'/admin/group') }}">
            <span class="glyphicon glyphicon-tower"></span>&nbsp; 组别
            </a></li>
            @endif
            @if ($level < 5)
            <li
            {{{ (Request::is($com->path . '/admin/report*') ? 'class=active' : '') }}}>
            <a href="{{ URL::to($com->path.'/admin/report') }}">
            <span class="glyphicon glyphicon-signal"></span>&nbsp; 报表
            </a></li>
            @endif
            @if ($level < 3)
            <li
            {{{ (Request::is($com->path . '/admin/quere*') ? 'class=active' : '') }}}>
            <!-- <a href="{{ URL::to($com->path.'/admin/quere') }}"> -->
            <!-- binway路由控制 - start -->
            <a href="{{ URL::to($com->path.'/admin/quere/order') }}">
            <!-- binway路由控制 - end -->
            <span class="glyphicon glyphicon-dashboard"></span>&nbsp; 队列
            </a></li>
            <li
            {{{ (Request::is($com->path . '/admin/config*') ? 'class=active' : '') }}}>
            <a href="{{ URL::to($com->path.'/admin/config') }}">
            <span class="glyphicon glyphicon-wrench"></span>&nbsp; 系统
            </a></li>
            @endif
            <li>
            <a href="http://www.no" target="_blank">
            E&nbsp; no
            </a>
            </li>

            <!-- @if ($level < 5)
            <li
            {{{ (Request::is($com->path . '/admin/check*') ? 'class=active' : '') }}}><a href="{{ URL::to($com->path.'/admin/check') }}">工单审核</a></li>
            @endif -->
        </ul>

        <ul class="nav navbar-nav navbar-right navbar-user">
            <!-- @if (Auth::user()->level > 0)
            <input type="hidden" id="h_message" name="h_message" value="{{Auth::user()->message}}" />
            <input type="hidden" id="h_audio" name="h_audio" value="{{Auth::user()->audio}}" />
            <li class="dropdown messages-dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span id="user-bell" style="display:none;" class="glyphicon glyphicon-bell"></span> 消息通知 
                <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                @if (Auth::user()->message == '0')
                @if (Auth::user()->level == 3)
                <li id="user-work">
                    <a href="{{ URL::to($com->path.'/admin/work/getmessage'.'?type=group') }}" class="view-link-group">
                    <span class="glyphicon glyphicon-tasks"></span> 工单池 
                    <span id="user-badge" class="badge"></span>
                    </a>
                </li>
                <li class="divider"></li>
                @endif

                <li id="user-work-me">
                    <a href="{{ URL::to($com->path.'/admin/work/getmessage'.'?type=me') }}" class="view-link-me">
                    <span class="glyphicon glyphicon-envelope"></span> 我的工单 
                    <span id="user-badge-me" class="badge"></span>
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
            </li>
            @endif -->

            <li class="dropdown user-dropdown">
                <!-- 平台公司使用头像 - no - start -->
                @if (Auth::user()->head > 0)
                <a href="#" style="padding-top: 12px;" class="dropdown-toggle" data-toggle="dropdown">
                <img title="{{Auth::user()->name }}" src="{{ asset(Auth::user()->enclosures_path) }}" 
                style="border-radius: 50%;height: 23px;width: 23px;" />
                您好，{{{
                    Auth::user()->name }}} <b class="caret"></b>
                </a>
                @else
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-user" title="{{{Auth::user()->name }}}"></i> 
                您好，{{{
                    Auth::user()->name }}} <b class="caret"></b></a>
                @endif
                <!-- 平台公司使用头像 - no - end -->

                <ul class="dropdown-menu">
                    <!-- <li><a href="{{ URL::to($com->path) }}" target="_blank"><i class="fa fa-home"></i> 工单首页</a></li> -->
                    <li><a href="{{ URL::to($com->path.'/admin/account') }}"><i class="fa fa-user"></i> 个人信息</a></li>
                    <!-- @if ($level > 2)
                    <li class="divider"></li>
                    <li><a href="{{ URL::to($com->path.'/admin/apply') }}"><i class="glyphicon glyphicon-cog"></i> 应用设置</a></li>
                    @endif -->
                    <li class="divider"></li>
                    <li><a href="{{ URL::to($com->path.'/admin/logout') }}"><i class="fa fa-power-off"></i> 退出</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>

<!-- Modal -->
<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">消息通知 - 工单池</h4>
            </div>
            <div class="modal-body" id="viewClientModalbody" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal -->
<div class="modal fade" id="viewClientModalMe" tabindex="-1" role="dialog" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="viewClientModalLabel">消息通知 - 我的工单</h4>
            </div>
            <div class="modal-body" id="viewClientModalbodyMe" >

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->