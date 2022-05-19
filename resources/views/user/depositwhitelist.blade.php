@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','充值白名单')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading"> 
                        <h3 class="panel-title">充值白名单</h3>
                    </div>
                    <div class="panel-body">
                        @include('partials.errors')
                        @include('partials.success')
                        <!-- form start -->
                        <form id='defaultForm' class="form-horizontal" role="form" method="POST">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">用户名</label>
                                <div class="col-md-6 control-label">
                                    <p class="text-left">{{$user->username}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">是否白名单</label>
                                <div class="col-md-6">
                                    <p class="help-block">
                                        <code>
                                            @if($user->is_pay_whitelist)
                                            是
                                            @else
                                            否
                                            @endif
                                        </code>
                                    </p>
                                </div>
                            </div>
                            @if($user->is_pay_whitelist)
                            <div class="form-group">
                                <label class="col-md-3 control-label checkbox-inline text-bold">取消白名单方式</label>
                                <div class="col-md-6">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" value="0" checked="">
                                            仅取消此会员，不取消其下级
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" value="1">
                                            取消此会员和所有下级
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-md-3 control-label checkbox-inline text-bold">添加白名单方式</label>
                                <div class="col-md-6">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" value="0" checked="">
                                            仅添加此会员，不添加下级
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" value="1">
                                            添加此会员和所有下级
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="form-group margin">
                                <div class="col-md-7 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary btn-md col-sm-2" id="search_btn">确定</button>
                                    <button type="reset" class="btn btn-default btn-md col-sm-2 col-md-offset-1"  onclick="location.href = '/user/';">取消</button>
                                </div>
                            </div>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="id" value="{{$user->id}}">
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@stop