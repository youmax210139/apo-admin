@extends('layouts.base')

@section('title','配额组管理')

@section('function','配额组管理')
@section('function_link', '/quotas/')

@section('here','添加配额组')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">

        <div class="row">
            <div class="">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">添加配额组</h3>
                    </div>
                    <div class="panel-body">

                        @include('partials.errors')
                        @include('partials.success')

                        <form class="form-horizontal" role="form" method="POST" action="/quotas/create">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">配额上限</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="high" id="tag" value="{{ $high }}" maxlength="16" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">配额下限</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="low" id="tag" value="{{ $low }}" maxlength="16" />
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