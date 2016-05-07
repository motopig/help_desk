<!-- header -->
@include('com.admin.layouts.header')
<!-- ./ header -->

<div id="wrapper">

    <!-- sidebar -->
    @include('com.admin.layouts.sidebar')
    <!-- ./ sidebar -->

    <div id="page-wrapper">
    @yield('content')
    <span style="float:right;"><hr /></span>
    </div><!-- /#page-wrapper -->

    <!-- <footer>
        <div class="col-lg-12">
            <p>Copyright &copy; <a href="http://www.no" target="_blank">Ecdo.cc</a> &middot; 2013  </p>
        </div>
    </footer> -->

</div><!-- /#wrapper -->

<!-- footer -->
@include('com.admin.layouts.footer')
<!-- ./ footer -->
