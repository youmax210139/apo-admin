@extends('layouts.base')

@section('title','账变类型')

@section('function','账变类型')
@section('function_link', '/orderType/')

@section('here','添加类型')

{{--@section('pageDesc','DashBoard')--}}
@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">添加类型</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" role="form" method="POST" action="/orderType/create">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                @include('order-type._form')
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                       <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
                                           <i class="fa fa-minus-circle"></i>
                                           取消
                                       </button>
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            添加
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop