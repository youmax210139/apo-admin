<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ get_config('web_title', 'Laravel') }} - @yield('title')</title>
    {{-- Bootstrap 3.3.6 --}}
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="/assets/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{-- Ionicons --}}
    {{-- <link rel="stylesheet" href="/assets/libs/ionicons/2.0.1/css/ionicons.min.css"> --}}
    {{-- Theme style --}}
    <link rel="stylesheet" href="/assets/dist/css/AdminLTE.min.css?t=20190725">
    {{--  AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    --}}
    <link rel="stylesheet" href="/assets/dist/css/skins/_all-skins.min.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    {{-- DataTabels --}}
    <link rel="stylesheet" href="/assets/plugins/datatables/dataTables.bootstrap.css">
    {{-- Validator --}}
    <link rel="stylesheet" href="/assets/plugins/bootstrapvalidator/bootstrapValidator.css"/>
    {{-- toast --}}
    <link rel="stylesheet" href="/assets/plugins/bootoast/bootoast.css"/>
    {{-- loading --}}
    <link rel="stylesheet" href="/assets/dist/css/load/load.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-dialog.min.css">
	@yield('css')
</head>
{{--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
--}}
<body class="hold-transition @php echo isset($_COOKIE['skin'])?$_COOKIE['skin']:'skin-blue'; @endphp fixed sidebar-mini">
<div class="modal fade myModal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>