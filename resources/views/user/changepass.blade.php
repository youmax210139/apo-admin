@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','修改密码')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">修改密码</h3>
                    </div>
                    <div class="panel-body">
                        @include('partials.errors')
                        @include('partials.success')
                        <div>
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#loginpass" aria-controls="loginpass" role="tab" data-toggle="tab">登陆密码</a></li>
                                <li role="presentation"><a href="#securitypass" aria-controls="securitypass" role="tab" data-toggle="tab">资金密码</a></li>

                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="loginpass">
                                    <form role="form" class="form-horizontal"  method="POST" id="loginpassForm" action="/user/changepass?id={{$user->id}}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="flag" value="loginpass">
                                        <div class="form-group margin">
                                            <label for="tag" class="col-md-3 control-label">用户名</label>
                                            <div class="col-md-6 control-label">
                                                <p class="text-left">{{$user->username}}</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="tag" class="col-md-3 control-label">用户昵称</label>
                                            <div class="col-md-6 control-label">
                                                <p class="text-left">{{$user->usernick}}</p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="tag" class="col-md-3 control-label">登陆密码</label>
                                            <div class="col-md-5">
                                                <input type="password" name="password" class="form-control" placeholder="请输入登陆密码">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="tag" class="col-md-3 control-label">确认密码</label>
                                            <div class="col-md-5">
                                                <input type="password" name="comfirmpassword" class="form-control" placeholder="请输入确认密码">
                                            </div>
                                        </div>

                                        <div class="form-group margin">
                                            <div class="col-md-7 col-md-offset-3">
                                                <button type="submit" class="btn btn-primary btn-md" id="search_btn">确定修改</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="securitypass">
                                    <form role="form" class="form-horizontal"  method="POST" id="securityForm" action="/user/changepass?id={{$user->id}}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="flag" value="securitypass">

                                        <div class="form-group margin">
                                            <label for="tag" class="col-md-3 control-label">用户名</label>
                                            <div class="col-md-6 control-label">
                                                <p class="text-left">{{$user->username}}</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="tag" class="col-md-3 control-label">用户昵称</label>
                                            <div class="col-md-6 control-label">
                                                <p class="text-left">{{$user->usernick}}</p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="tag" class="col-md-3 control-label">资金密码</label>
                                            <div class="col-md-5">
                                                <input type="password" name="security_password" class="form-control" placeholder="请输入资金密码">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="tag" class="col-md-3 control-label">确认密码</label>
                                            <div class="col-md-5">
                                                <input type="password" name="comfirm_security_password" class="form-control" placeholder="请输入确认密码">
                                            </div>
                                        </div>

                                        <div class="form-group margin">
                                            <div class="col-md-7 col-md-offset-3">
                                                <button type="submit" class="btn btn-primary btn-md" id="search_btn">确定修改</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>

                        </div>
                    </div>
                    <!-- /.box-body -->
                </div> 

            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    $(document).ready(function () {
        $('#loginpassForm')
                .bootstrapValidator({
                    message: '该数据不可用',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        password: {
                            validators: {
                                notEmpty: {
                                    message: '请输入登陆密码!'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 16,
                                    message: '密码长度在6到16之间'
                                }
                            }
                        },
                        comfirmpassword: {
                            validators: {
                                notEmpty: {
                                    message: '请输入确认密码!'
                                },
                                identical: {
                                    field: 'password',
                                    message: '两次密码不同请重新输入'
                                }
                            }
                        }
                    }
                }).on('success.form.bv', function (e) {

            return true;
        });
        $('#securityForm')
                .bootstrapValidator({
                    message: '该数据不可用',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        security_password: {
                            validators: {
                                notEmpty: {
                                    message: '请输入资金密码!'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 16,
                                    message: '密码长度在6到16之间'
                                }
                            }
                        },
                        comfirm_security_password: {
                            validators: {
                                notEmpty: {
                                    message: '请输入确认密码!'
                                },
                                identical: {
                                    field: 'security_password',
                                    message: '两次密码不同请重新输入'
                                }
                            }
                        }
                    }
                }).on('success.form.bv', function (e) {

            return true;
        });
    });
</script>
@stop