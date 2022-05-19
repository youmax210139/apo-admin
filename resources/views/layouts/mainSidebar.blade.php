<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
      {{--  <div class="user-panel">
            <div class="pull-left image">
                <img src="/imgs/avatar/u1.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{auth()->user()->username}}</p>
                <!-- Status -->
                <a><i class="fa fa-circle text-success"></i> 在线</a>
            </div>
        </div>
--}}
        <!-- search form (Optional) -->
      {{--  <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="搜索...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>--}}
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu tree" data-widget="tree">
            <!-- Optionally, you can add icons to the links -->

            <li><a href="/"><i class="fa fa-dashboard"></i> <span>控制面板</span></a></li>
            <?php $menus=get_menu(auth()->user());?>
            @foreach($menus['tree'] as $menu)
                <li class="treeview @if($menu->active) active @endif">
                    <a href="#"><i class="fa {{ $menu->icon }}"></i> <span>{{ $menu->name }}</span><span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span></a>
                    <ul class="treeview-menu">
                        @foreach($menus['subtree'][$menu->id] as $submenu)
                            <li  @if($submenu->active == true) class="active" @endif style="margin-left:18px"><a title="{{ $submenu->name }}" href="/{{$submenu->rule}}/" mountTabs><i class="fa fa-circle-o"></i>{{ $submenu->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
            @endforeach

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>