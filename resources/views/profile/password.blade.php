@extends('layouts.base')
@section('title','管理员中心')
@section('function','管理员中心')
@section('here','修改登录密码')
@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">修改登录密码</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" role="form" method="POST" action="/profile/password" id='defaultForm'>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                @include('profile._form')
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
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
        $(document).ready(function () {
            $('#defaultForm')
                .bootstrapValidator({
                    message: '该数据不可用',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        old_password: {
                            validators: {
                                notEmpty: {
                                    message: '旧密码不能为空'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 20,
                                    message: '密码长度在 6-20 位之间'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9_\.]+$/,
                                    message: '密码只能字母数字_.组合'
                                }
                            }
                        },
                        new_password: {
                            validators: {
                                notEmpty: {
                                    message: '新密码不能为空'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 20,
                                    message: '密码长度在 6-20 位之间'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9_\.]+$/,
                                    message: '密码只能字母数字_.组合'
                                },
                                identical: {
                                    field: 'new_password_confirmation',
                                    message: '密码和确认密码不相同'
                                }
                            }
                        },
                        new_password_confirmation: {
                            validators: {
                                notEmpty: {
                                    message: '确认密码不能为空'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 20,
                                    message: '密码长度在 6-20 位之间'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9_\.]+$/,
                                    message: '密码只能字母数字_.组合'
                                },
                                identical: {
                                    field: 'new_password',
                                    message: '密码和新密码不相同'
                                }
                            }
                        }
                    }
                })
        });
    </script>
@stop
