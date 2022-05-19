@extends('layouts.base')

@section('title','第三方游戏日志查看')

@section('function','第三方游戏接口日志查看')
@section('function_link', '/thirdgame/')

@section('here','第三方游戏日志查看')

{{--@section('pageDesc','DashBoard')--}}


@section('content')


<style>
.api{
    margin:5px;border:1px solid #ccc;padding: 5px;
}
.api_title{
    font-size: 16px;cursor: pointer;
}
.detail{
    display:none;
}
</style>

@forelse($list as $v)
    <div class="row api">
        <div class="api_title">{{ $v['api'] }}</div>
        <div class="detail"><pre>{{ $v['detail'] }}</pre></div>
    </div>
@empty
    <div>暂无日志</div>
@endforelse

@stop




@section('js')
 <script src="/assets/js/app/common.js" charset="UTF-8"></script>
 <script>
    $('.api_title').bind('click', function(){
        $(this).next('.detail').slideToggle("fast");
    });
 </script>
@stop