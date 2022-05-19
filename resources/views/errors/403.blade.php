@extends('layouts.base')

@section('title','403 Error Page')

@section('function','403 Error Page')
@section('function_link', '#')

@section('here','403 Error Page')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="error-page">
        <h2 class="headline text-yellow"> 403</h2>

        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> 没有权限访问.</h3>
            <p>
                我没有办法，权限不够真的不可以！
            </p>

        </div>
        <!-- /.error-content -->
    </div>
@stop
