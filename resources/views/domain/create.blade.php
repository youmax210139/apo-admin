@extends('layouts.base')

@section('title','域名管理')

@section('function','域名管理')
@section('function_link', '/lotterycategory/')

@section('here','添加域名')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">

        <div class="row">
            <div class="">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">添加域名</h3>
                    </div>
                    <div class="panel-body">

                        @include('partials.errors')
                        @include('partials.success')

                        <form class="form-horizontal" role="form" method="POST" action="/domain/create">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">域名</label>
                                <div class="col-md-5">
                                    <input type="text" placeholder="域名格式为 www.example.com" class="form-control" name="domain" id="tag" value="" maxlength="64" />
                                </div>
                            </div>
                           
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