@extends('layouts.base')

@section('title','页面不存在')



@section('here','')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <h2 class="headline text-yellow" style="font-size: 100px; text-align: center"> 404</h2>
    <div class="error-page">
        <div class="error-content" style="text-align: center;margin-left: 0px">
            <h3><i class="fa fa-warning text-yellow"></i> 我勒个去! 页面不存在哦.</h3>
            <p>
               可能你迷路了，别担心程序猿可以帮你找到回家的路！
            </p>

        </div>
        <!-- /.error-content -->
    </div>
@stop
