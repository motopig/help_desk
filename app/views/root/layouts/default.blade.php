<!-- header -->
@include('root.layouts.header')
<!-- ./ header -->

<div id="wrapper">

    <!-- sidebar -->
    @include('root.layouts.sidebar')
    <!-- ./ sidebar -->

    <div id="page-wrapper">
    @yield('content')
    <hr />
    </div><!-- /#page-wrapper -->

    <footer>
        <div class="col-lg-12">
            <p>Copyright &copy; Ecdo.cc &middot; 2013  </p>
        </div>
    </footer>

</div><!-- /#wrapper -->

<!-- footer -->
@include('root.layouts.footer')
<!-- ./ footer -->
