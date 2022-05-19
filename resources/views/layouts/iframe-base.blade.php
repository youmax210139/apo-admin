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
<div class="wrapper">
    <!-- Main Header -->
@include('layouts.mainHeader')
<!-- Left side column. contains the logo and sidebar -->
@include('layouts.mainSidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
       
        <!-- Content Header (Page header) -->
        <section class="content-header navTopBar">
        {{--<h1>--}}
        {{--@yield('function')--}}
        {{--<small>@yield('pageDesc')</small>--}}
        {{--</h1>--}}
        {{--<ol class="breadcrumb">--}}
        {{--<li><a href="@yield('function_link')"><i class="fa fa-dashboard"></i>@yield('function')</a></li>--}}
        {{--<li class="active">@yield('here')</li>--}}
        {{--</ol>--}}
        <!--<h6>
              {{--  @if(Request::is('log-viewer*'))
                    仪表盘
                @else
                    {!! Breadcrumbs::render(Route::currentRouteName()) !!}
                @endif--}}
                </h6>-->
            <ul style="margin-left: 92px" id="iframe-tabs" class="nav nav-tabs" >
                <li role="presentation" iframe_id="/index/Dashboard/" class="active"><a href="javascript:;"><i class="fa fa-dashboard"></i></a></li>
            </ul>
            <div style="left: 72px" class="control ltabs fa fa-angle-left"></div>
            <div class="control rtabs fa fa-angle-right"></div>
            <!-- Single button -->
            <div class=" control btn-group" style="left: 20px">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    操作 <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="">
                    <li><a id="iframe-tabs-close-other" href="#">关闭其他选项卡</a></li>
                    <li><a id="iframe-tabs-close-cur" href="#">关闭当前选项卡</a></li>
                </ul>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Your Page Content Here -->
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane active" id="control-sidebar-home-tab">
                <h3 class="control-sidebar-heading">Recent Activity</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript:;">
                            <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                                <p>Will be 23 on April 24th</p>
                            </div>
                        </a>
                    </li>
                </ul>
                <!-- /.control-sidebar-menu -->

                <h3 class="control-sidebar-heading">Tasks Progress</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Custom Template Design
                                <span class="label label-danger pull-right">70%</span>
                            </h4>

                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                            </div>
                        </a>
                    </li>
                </ul>
                <!-- /.control-sidebar-menu -->

            </div>
            <!-- /.tab-pane -->
            <!-- Stats tab content -->
            <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
            <!-- /.tab-pane -->
            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">
                <form method="post">
                    <h3 class="control-sidebar-heading">General Settings</h3>

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Report panel usage
                            <input type="checkbox" class="pull-right" checked>
                        </label>

                        <p>
                            Some information about this general settings option
                        </p>
                    </div>
                    <!-- /.form-group -->
                </form>
            </div>
            <!-- /.tab-pane -->
        </div>
    </aside>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
@include('layouts.baseFooter')
