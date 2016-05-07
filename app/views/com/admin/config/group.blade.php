@extends('com.admin.layouts.default')

@section('styles')
@parent
<style>
    /*.announcement-heading {font-size: 35px;}*/
    pre {
        background: none;
        border: none;
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

        <!-- <h1>邮件设置
            <small>欢迎</small>
        </h1> -->

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to($com->path.'/admin') }}">首页</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config') }}">系统设置</a></li>
            <li><a href="{{ URL::to($com->path.'/admin/config/mail') }}">邮箱列表</a></li>
            <li class="active">组别绑定</li>
        </ol>

        <!-- Notifications -->
        @include('root.notifications')
        <!-- ./ notifications -->

        <hr>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">

        <form class="col-sm-6" role="form" method="post">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="{{{ Session::getToken() }}}" />

            <table class="table">
                <thead>
                <tr>
                    <th>邮箱</th>
                    <th>绑定组别</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($company_email as $ce)
                    <tr>
                        <td>
                            <input type="radio" name="email[{{$ce->id}}]" value="{{$ce->id}}" checked /> &nbsp;{{$ce->email}}
                        </td>
                        <td>
                            <select name="group[{{$ce->id}}]">
                                <option value="0">请选择...</option>
                                @foreach ($group as $g)
                                    <option value="{{$g->id}}" @if ($g->id == $ce->group_id) selected @endif >
                                        {{$g->group_name}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <hr />
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">

                    <button type="submit" class="btn btn-primary">绑定</button>

                    <a href="{{ URL::to($com->path.'/admin/config/mail') }}">
                        <button type="button" class="btn btn-default">取消</button>
                    </a>
                </div>
            </div>

        </form>

    </div>


</div><!-- /.row -->
@stop