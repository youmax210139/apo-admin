@include('layouts.baseHeader')
<div id="loading">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <div class="object" id="object_four"></div>
            <div class="object" id="object_three"></div>
            <div class="object" id="object_two"></div>
            <div class="object" id="object_one"></div>
        </div>
    </div>
</div>
<div class="wrapper" style="background:#ecf0f5">

    <!-- Content Wrapper. Contains page content -->
    <section class="content-header">
        <h1>
            @yield('function')
            <small>@yield('pageDesc')</small>
        </h1>
        <ol class="breadcrumb">
            <li><a class="btn-xs" href="@yield('function_link')"><i class="fa fa-dashboard"></i>@yield('function')</a></li>
            <li class="active">@yield('here')</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Your Page Content Here -->
        @yield('content')
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
</div>
@include('layouts.page-footer')
