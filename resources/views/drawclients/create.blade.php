@extends('layouts.base')

@section('title','号源客户')

@section('function','号源客户')
@section('function_link', '/drawclients/')

@section('here','添加客户')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">添加客户</h3>
                        </div>
                        <div class="panel-body">

                            @include('partials.errors')
                            @include('partials.success')

                            <form class="form-horizontal" role="form" method="POST" action="/drawclients/create">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                @include('drawclients._form')
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

@section('js')
<script>
    function generateKey(id) {
        var len = 32;
        var chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        var result = '';
        chars += chars;
        for (var i = len; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
        $("#"+id).val(result);
    }
</script>    
@stop