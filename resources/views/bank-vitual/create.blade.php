@extends('layouts.base')
@section('title','虚拟货币银行管理')
@section('function','虚拟货币银行管理')
@section('function_link', '/bankvirtual/')
@section('here','添加虚拟货币银行')
@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">添加虚拟货币银行</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" role="form" method="POST" action="/bankvirtual/create">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PUT">
                                @include('bank-vitual._form')
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
