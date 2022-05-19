@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','修改虚拟货币钱包')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">修改 {{$user->username}} 的虚拟货币钱包</h3>
                        </div>
                        <div class="panel-body">

                            @include('partials.errors')
                            @include('partials.success')

                            <form class="form-horizontal" role="form" method="POST" action="/user/editbankvirtualcurrency">
                                <input type="hidden" name="id" value="{{ $bank->id }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">

                                <div class="form-group">
                                    <label for="bank_virtual_id" class="col-md-3 control-label checkbox-inline text-bold">开户银行</label>
                                    <div class="col-md-7">
                                        <select name="bank_virtual_id" class="form-control">
                                            <option value="">选择虚拟货币银行</option>
                                            @foreach($banks as $v)
                                                <option value="{{ $v->id }}"  @if ( $v->id == $bank->bank_virtual_id )selected="selected"@endif>{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-md-3 control-label checkbox-inline text-bold">钱包姓名</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="name" id="name" value="{{ $bank->name }}"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="account" class="col-md-3 control-label checkbox-inline text-bold">钱包地址</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="account" id="account" value="{{ $bank->account }}"  />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-5">
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
        </div>
    </div>
@stop