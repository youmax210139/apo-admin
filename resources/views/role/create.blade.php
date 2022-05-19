@extends('layouts.base')

@section('title','角色管理')

@section('function','添加角色')
@section('function_link', '/role/')

@section('here','添加角色')

{{--@section('pageDesc','DashBoard')--}}
@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                @include('partials.errors')
                @include('partials.success')
                <form class="form-horizontal" role="form" method="POST" action="/role/create">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @include('role._form')
                    <div class="form-group">
                        <div class="col-md-offset-5">
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
@stop