@extends('layouts.base')

@section('title','编辑IP记录')

@section('function','登录IP黑名单')
@section('function_link', '/ipblacklist/')

@section('here','编辑IP记录')

{{--@section('pageDesc','DashBoard')--}}
@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                    @include('partials.errors')
                    @include('partials.success')
                    <form class="form-horizontal" role="form" method="POST"
                          action="/ipblacklist/edit">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" value="{{ $id }}">
                        @include('ipblacklist._form')
                        <div class="form-group">
                            <div class="col-md-offset-5">
                               <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
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
@stop