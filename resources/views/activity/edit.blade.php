@extends('layouts.base')

@section('title','活动管理')

@section('function','活动管理')
@section('function_link', '/activity/')

@section('here','编辑活动')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">编辑活动</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" role="form" method="POST" action="/activity/edit">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="id" value="{{ $id }}">
                                @include('activity._form')
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                       <button type="button" class="btn btn-warning btn-md" onclick="location.href = '/activity/';">
                                           <i class="fa fa-minus-circle"></i>
                                           取消
                                       </button>
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            保存
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
