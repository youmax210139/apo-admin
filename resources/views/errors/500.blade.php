@extends('layouts.base')

@section('title','500 Error Page')

@section('function','500 Error Page')
@section('function_link', '#')

@section('here','500 Error Page')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <h2 class="headline text-yellow" style="font-size: 100px; text-align: center"> 500</h2>
    <div class="error-page">

        <div class="error-content" style="margin-left: 0">
            <h3><i class="fa fa-warning text-red"></i> 额悲剧了！页面无法访问！.</h3>

            <p>
                服务器可能累了，烦请联系程序猿伺候它下.
                 或许你可以 <a href="javascript:window.location.reload();">刷新</a> 试试看，反正不要钱
            </p>

        </div>
    </div>
@stop
