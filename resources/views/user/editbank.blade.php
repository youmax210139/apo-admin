@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','修改银行卡')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">修改 {{$user->username}} 的银行卡</h3>
                        </div>
                        <div class="panel-body">

                            @include('partials.errors')
                            @include('partials.success')

                            <form class="form-horizontal" role="form" method="POST" action="/user/editbank">
                                <input type="hidden" name="id" value="{{ $bank->id }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">

                                <div class="form-group">
                                    <label for="bank_id" class="col-md-3 control-label checkbox-inline text-bold">开户银行</label>
                                    <div class="col-md-7">
                                        <select name="bank_id" class="form-control">
                                            <option value="">选择开户银行</option>
                                            @foreach($banks as $v)
                                                <option value="{{ $v->id }}"  @if ( $v->id == $bank->bank_id )selected="selected"@endif>{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">省份</label>
                                    <div class="col-md-7">
                                        <select name="province_id" id="province_id" class="form-control">
                                            <option value="">省份</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">城市</label>
                                    <div class="col-md-7">
                                        <select name="city_id" id="city_id" class="form-control">
                                            <option value="">城市</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">区/县</label>
                                    <div class="col-md-7">
                                        <select name="district_id" id="district_id" class="form-control">
                                            <option value="">区/县</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="branch" class="col-md-3 control-label checkbox-inline text-bold">支行名称</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="branch" id="branch" value="{{ $bank->branch }}"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="account_name" class="col-md-3 control-label checkbox-inline text-bold">持卡人姓名</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="account_name" id="account_name" value="{{ $bank->account_name }}"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="account" class="col-md-3 control-label checkbox-inline text-bold">银行卡号</label>
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

@section('js')
    <script>
        var regions = {!! $regions !!} ;
        var user_pid = {{ $bank->province_id }};
        var user_cid = {{ $bank->city_id }};
        var user_did = {{ $bank->district_id }};
        function province(user_pid) {
            var html = '<option value="">省份</option>';
            if(typeof regions[0] !== 'undefined') {
                var data = regions[0];
                for(var i=0; i<data.length; i++) {
                    if(user_pid == data[i].id) {
                        html += '<option value="'+ data[i].id +'" selected>'+ data[i].name +'</option>';
                    } else {
                        html += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
                    }
                }
            }
            $("#province_id").html(html);
        }
        function city(parent_id, user_cid) {
            var html = '<option value="">城市</option>';
            if(typeof regions[parent_id] !== 'undefined') {
                var data = regions[parent_id];
                for(var i=0; i<data.length; i++) {
                    if(user_cid == data[i].id) {
                        html += '<option value="'+ data[i].id +'" selected>'+ data[i].name +'</option>';
                    } else {
                        html += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
                    }
                }
            }
            $("#city_id").html(html);
        }
        function district(parent_id, user_did) {
            var html = '<option value="">区/县</option>';
            if(typeof regions[parent_id] !== 'undefined') {
                var data = regions[parent_id];
                for(var i=0; i<data.length; i++) {
                    if(user_did == data[i].id) {
                        html += '<option value="'+ data[i].id +'" selected>'+ data[i].name +'</option>';
                    } else {
                        html += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
                    }
                }
            }
            $("#district_id").html(html);
        }

        $(function () {
            province(user_pid);
            city(user_pid, user_cid);
            district(user_cid, user_did);
            $("#province_id").change(function () {
                city($(this).val(), 0);
                district('', 0);
            });
            $("#city_id").change(function () {
                district($(this).val(), 0);
            });
        })
    </script>
@stop