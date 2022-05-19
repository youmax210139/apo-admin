  <header class="main-header">

    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>{{mb_substr(get_config('web_title','Apollo'),0,1)}}</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>{{get_config('web_title','Apollo')}}后台</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
        <a href="javascript:;" id="iframe_refresh" title="刷新" style="float: left;
    background-color: transparent;
    background-image: none;
    padding: 15px 15px;
    font-family: fontAwesome;color: #fff">
            <i class="fa fa-refresh"></i>
        </a>
        <div id="marquee_alert_menu"></div>
        <style>
            #marquee_alert_menu {
                height: 50px;
                line-height: 50px;
                width: 400px;
                color: white;
                font-size: 18px;
                overflow: hidden;
                float: left;
                text-shadow:1px 1px 5px #000;
            }

            #marquee_alert_menu ul {
                padding: 0;
            }

            #marquee_alert_menu li {
                list-style: none;
            }
        </style>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown tasks-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    <i class="fa fa-flag-o"></i>
                    <span class="label label-danger" id="tasks_num">0</span>
                </a>
                <ul class="dropdown-menu" id="tasks_alert_menu" style="width: 380px">

                </ul>
            </li>
            @if(Gate::check('project/alert'))
            <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    <i class="fa fa-bell-o"></i>
                    <span class="label label-warning" id="project_alert_num" style="display: none">0</span>
                </a>
                <ul class="dropdown-menu" id="project_alert_menu" style="width:auto;max-width: 560px">

                </ul>
            </li>
            @endif
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" title="皮肤" data-toggle="dropdown" aria-expanded="true">
                        <i class="fa fa-paw"></i>
                    </a>
                    <ul class="list-unstyled clearfix dropdown-menu" id="skins">
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-blue"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9"></span><span
                                            class="bg-light-blue"
                                            style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin">Blue</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-black"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span
                                            style="display:block; width: 20%; float: left; height: 7px; background: #fefefe"></span><span
                                            style="display:block; width: 80%; float: left; height: 7px; background: #fefefe"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin">Black</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-purple"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-purple-active"></span><span class="bg-purple"
                                                                                 style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin">Purple</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-green"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-green-active"></span><span class="bg-green"
                                                                                style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin">Green</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-red"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-red-active"></span><span class="bg-red"
                                                                              style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin">Red</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-yellow"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-yellow-active"></span><span class="bg-yellow"
                                                                                 style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin">Yellow</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-blue-light"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9"></span><span
                                            class="bg-light-blue"
                                            style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin" style="font-size: 12px">Blue Light</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-black-light"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span
                                            style="display:block; width: 20%; float: left; height: 7px; background: #fefefe"></span><span
                                            style="display:block; width: 80%; float: left; height: 7px; background: #fefefe"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin" style="font-size: 12px">Black Light</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-purple-light"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-purple-active"></span><span class="bg-purple"
                                                                                 style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin" style="font-size: 12px">Purple Light</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-green-light"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-green-active"></span><span class="bg-green"
                                                                                style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin" style="font-size: 12px">Green Light</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-red-light"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-red-active"></span><span class="bg-red"
                                                                              style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin" style="font-size: 12px">Red Light</p></li>
                        <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0)"
                                                                                   data-skin="skin-yellow-light"
                                                                                   style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
                                                                                   class="clearfix full-opacity-hover">
                                <div><span style="display:block; width: 20%; float: left; height: 7px;"
                                           class="bg-yellow-active"></span><span class="bg-yellow"
                                                                                 style="display:block; width: 80%; float: left; height: 7px;"></span>
                                </div>
                                <div>
                                    <span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span
                                            style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span>
                                </div>
                            </a>
                            <p class="text-center no-margin" style="font-size: 12px">Yellow Light</p></li>
                    </ul>
                </li>
                 <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

              <span>{{auth()->user()->usernick}} [{{$admin->role_name}}]</span>
            </a>
            <ul class="dropdown-menu">

              <!-- The user image in the menu -->
              <li class="user-header">
                {{--<img src="/123.png" class="img-circle" alt="User Image">--}}
                <p>
                  {{auth()->user()->usernick}}
                  <br />
                  {{$admin->role_name}}
                  <br />
                  <small>上次登录时间：{{auth()->user()->updated_at}}，</small>
                    <small>IP 地址：{{auth()->user()->last_ip}}</small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
              	<div class="pull-left">
              	<a href='/profile/password' title="修改密码" mountTabs class="btn btn-default btn-flat">修改密码</a>
              	</div>
                  <div class="pull-left">
                       <a href='/profile/googlekey' title="谷歌登录器" mountTabs class="btn btn-default btn-flat">谷歌登录器</a>
                  </div>
                <div class="pull-right">
                    <form method="POST" action="/logout">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-default btn-flat">
                            登出
                        </button>
                    </form>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>