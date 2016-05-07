@extends('root.layouts.default')

@section('script')
@parent

@stop

@section('content')
<div class="row">

    <div class="col-lg-12">

        <h1>公司管理
            <small>简介</small>
        </h1>

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to('root') }}">首页</a></li>
            <li class="active"> 公司管理 ( 第 {{ $company->getCurrentPage() }} 页 / 共 {{ $company->getTotal() }}  条 )</li>
        </ol>

        <!-- Notifications -->

        @include('root.notifications')

        <!-- ./ notifications -->
    </div>
</div><!-- /.row -->

<!--<div class="row">-->
<!--    <div class="col-sm-3">-->
<!--    <div class="input-group">-->
<!--        <input type="text" class="form-control">-->
<!--        <div class="input-group-btn">-->
<!--            <button type="button" class="btn btn-default" tabindex="-1">搜索</button>-->
<!--        </div>-->
<!--    </div>-->
<!--    </div>-->
<!--</div>-->

<div class="row">
    <div class="col-lg-12">

        <hr>

        <div class="table-responsive clearfix">
            <table class="table table-hover table-striped  tablesorter">
                <thead>
                <tr>
                    <th>公司ID <i class="fa fa-sort"></i></th>
                    <th>公司名称 <i class="fa fa-sort"></i></th>
                    <th>联系电话 <i class="fa fa-sort"></i></th>
                    <th width="20%">操作</th>
                </tr>
                </thead>
                <tbody>

                @if ($company->count() >= 1)
                @foreach ($company as $c)

                @if ($c->disabled == 'true')
                    <tr class="danger">
                @else
                    <tr>
                @endif

                    <td>{{ $c->id }}</td>
                    <td>{{ $c->company_name }}</td>
                    <td>{{ $c->mobile }}</td>
                    <td>
                        <a href="{{ URL::to('root/company/edit/'.$c->id) }}" class="btn btn-xs btn-primary">编辑</a>

                    @if ($c->disabled == 'true')
                        <a href="{{ URL::to('root/company/enable/'.$c->id) }}" class="btn btn-xs btn-danger">启用</a>
                    @else
                        <a href="{{ URL::to('root/company/disabled/'.$c->id) }}" class="btn btn-xs btn-warning">停用</a>
                    @endif

                        <a href="{{ URL::to($c->path) }}" target="_blank" class="btn btn-xs btn-default">浏览</a>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">没记录</td>
                </tr>
                @endif

                </tbody>
            </table>
        </div>

        <!--分页-->
        {{ $company->links() }}
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-sm-9">
        <a href="{{ URL::to('root/company/create') }}">
            <button class="btn btn-primary">创建公司</button>
        </a>
    </div>
</div>

@stop


