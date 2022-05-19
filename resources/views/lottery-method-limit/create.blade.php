@extends('layouts.base')

@section('title','投注限制')

@section('function','投注限制')
@section('function_link', '/lotterymethodlimit/')

@section('here','添加限制')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">添加玩法</h3>
                        </div>
                        <div class="panel-body">

                            @include('partials.errors')
                            @include('partials.success')

                            <form class="form-horizontal" role="form" method="POST" action="/lotterymethodlimit/create">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="method_id" value="{{request()->get('id')}}"/>
                                @include('lottery-method-limit._form')
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            添加
                                        </button>

                                        <button type="button" class="btn btn-warning btn-md" onclick="javascript:window.history.back()">
                                            <i class="fa fa-reply-all"></i>
                                            返回
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