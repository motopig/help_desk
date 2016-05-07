@extends('root.layouts.default')

@section('script')
@parent
<script src="{{{ asset('assets/js/flot/jquery.flot.js') }}}"></script>
<script src="{{{ asset('assets/js/flot/jquery.flot.tooltip.min.js') }}}"></script>
<script src="{{{ asset('assets/js/flot/jquery.flot.resize.js') }}}"></script>
<script src="{{{ asset('assets/js/flot/jquery.flot.pie.js') }}}"></script>
<script src="{{{ asset('assets/js/flot/chart-data-flot.js') }}}"></script>
@stop

@section('content')
<div class="row">

    <div class="col-lg-12">

        <h1>报表统计
            <small>简介</small>
        </h1>

        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <a href="{{ URL::to('root') }}">首页</a></li>
            <li class="active"> 报表统计</li>
        </ol>

        <!-- Notifications -->

        @include('root.notifications')

        <!-- ./ notifications -->
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <h2>Flot Charts</h2>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Line Graph Example with Tooltips</h3>
            </div>
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart-line"></div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Pie Chart Example with Tooltips</h3>
            </div>
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart-pie"></div>
                </div>
                <div class="text-right">
                    <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Multiple Axes Line Graph Example with Tooltips and Raw Data</h3>
            </div>
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart-multiple-axes"></div>
                </div>
                <div class="text-right">
                    <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Moving Line Chart</h3>
            </div>
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart-moving-line"></div>
                </div>
                <div class="text-right">
                    <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Bar Graph with Tooltips</h3>
            </div>
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-chart-bar"></div>
                </div>
                <div class="text-right">
                    <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <h3>Additional Flot Chart Information</h3>
        <p>Full documentation for Flot can be found at <a href="http://www.flotcharts.org/" target="_blank">http://www.flotcharts.org/</a>. Flot has a lot of different options available, and they have a bunch of plugins as well that allow you to do neat things. Here we are using the tooltip plugin, the resize plugin, and the pie chart plugin, but there are many more to choose from. The documentation is a bit more advanced and requires a good deal of JavaScript knowledge in order to make the charts work for you.</p>
        <p><strong>NOTE:</strong> The charts are responsive, and the Flot charts are redrawn when the window resizes. The only one that needs a page refresh on a window resize is the pie chart. If you find a way to fix this, please let me know.</p>
    </div>
</div><!-- /.row -->

@stop