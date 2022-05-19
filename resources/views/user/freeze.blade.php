@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','用户冻结')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            @if($user->frozen==0)
                                <h3 class="panel-title">用户冻结</h3>
                            @else
                                <h3 class="panel-title">用户解冻</h3>
                            @endif
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
                                @if($user->frozen==0)
                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">上次解冻原因</label>
                                        <div class="col-md-6 control-label">
                                            <p class="text-left">{{$user->unfrozen_reason}}</p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label checkbox-inline text-bold">冻结范围</label>
                                        <div class="col-md-6">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freeze" value="1" checked="">
                                                    仅冻结此用户，不冻结其下级
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freeze" value="3">
                                                    冻结此用户和直属下级
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freeze" value="2">
                                                    冻结此用户和所有下级
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label checkbox-inline text-bold">冻结方式</label>
                                        <div class="col-md-6">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freezetype" value="1" checked="">
                                                    完全冻结
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freezetype" value="2">
                                                    可登录，不可投注，不可充提
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freezetype" value="3">
                                                    可登录，不可投注，可充提
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">本次冻结原因</label>
                                        <div class="col-md-5">
                                            <input type="text" name="reason" class="form-control" placeholder="请填写详细的理由"
                                                   maxlength="64">
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">冻结状态</label>
                                        <div class="col-md-6 control-label">
                                            <p class="text-left">
                                                @if($user->frozen==1)
                                                    完全冻结
                                                @elseif($user->frozen==2)
                                                    可登录，不可投注，不可充提
                                                @else
                                                    可登录，不可投注，可充提
                                                @endif
                                                    {{$user->freeze_value}}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">上次冻结原因</label>
                                        <div class="col-md-6 control-label">
                                            <p class="text-left">{{$user->frozen_reason}}</p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label checkbox-inline text-bold">解冻方式</label>
                                        <div class="col-md-6">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freeze" value="1" checked="">
                                                    仅解冻此会员，不解冻其下级
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freeze" value="3">
                                                    解冻此用户和直属下级
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="freeze" value="2">
                                                    解冻此会员和所有下级
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">本次冻结原因</label>
                                        <div class="col-md-5">
                                            <input type="text" name="reason" class="form-control" placeholder="请填写详细的理由"
                                                   maxlength="64">
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group margin">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary btn-md col-sm-2" id="search_btn">
                                            确定
                                        </button>
                                        <button type="reset" class="btn btn-default btn-md col-sm-2 col-md-offset-1"
                                                onclick="location.href='/user/';">取消
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                @if($user->frozen==0)
                                    <input type="hidden" name="flag" value="freeze">
                                @else
                                    <input type="hidden" name="flag" value="unfreeze">
                                @endif
                                <input type="hidden" name="id" value="{{$user->id}}">
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
                        reason: {
                            validators: {
                                notEmpty: {
                                    message: '请输入原因!'
                                }
                            }
                        },
                    }
                }).on('success.form.bv', function (e) {
                if (confirm("确认执行操作？")) {
                    return true;
                }
                e.preventDefault();
            });
        });
    </script>
@stop