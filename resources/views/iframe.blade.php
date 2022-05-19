@extends('layouts.iframe-base')
@section('title','控制面板')

@section('function','控制面板')
@section('function_link', '/')

@section('here','首页')

@section('content')
    <div class="row" id="app_body">
        <div class="app-tabsbody-item app-show" iframe_id="/index/Dashboard/">
            <iframe src="/index/Dashboard/" frameborder="0" class="app-iframe"></iframe>
        </div>
    </div>
@stop

