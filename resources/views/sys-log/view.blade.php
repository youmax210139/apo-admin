@extends('layouts.base')

@section('title','日志详情')
@section('function','系统日志')
@section('function_link', '/syslog/')
@section('here','日志详情')

{{--@section('pageDesc','DashBoard')--}}
@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{$name}}</h3>
                        </div>
                        <div class="panel-body">
                          <pre>{{$content}}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop