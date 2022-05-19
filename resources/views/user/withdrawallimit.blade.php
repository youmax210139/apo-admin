@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','用户提款次数')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">用户每日提现次数</h3>
                    </div>
                    <div class="panel-body">
                        @include('partials.errors')
                        @include('partials.success')
                        <form class="form-horizontal" id="defaultForm" role="form" method="POST">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">用户名</label>
                                <div class="col-md-6">
                                    <p class="help-block">{{ $user->username }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">当前提款次数</label>
                                <div class="col-md-6">
                                    <p class="help-block">{{$user->withdrawal_num + get_config('withdrawal_times_base',10)}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">提现次数</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="num" value="{{$user->withdrawal_num}}" placeholder="" maxlength="256"><p class="help-block">提现次数=用户次数(正负均可)+配置次数</p>
                                </div>
                            </div>
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
                    <!-- /.box-body -->


                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-primary" id="modal-confrim" tabIndex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    ×
                </button>
                <h4 class="modal-title">操作信息确认</h4>
            </div>
            <div class="modal-body">
              <p class="lead">
                    <i class="fa fa-question-circle fa-lg"></i>
                    确认要<span class="row_verify_text">执行</span>该操作吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="confrimForm" id="confrimForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="num" value="0">
                    <button type="button" class="btn btn-default" data-dismiss="modal">重新填写</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa"></i> 确认执行
                    </button>
                </form>
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
                        num: {
                            validators: {
                                notEmpty: {
                                    message: '请输入提现次数!'
                                },
                                numeric: {
                                    message: '必须是数字类型!'
                                }
                            }
                        }
                    }
                }).on('success.form.bv', function (e) {
            var num = $('#defaultForm input[name="num"]').val();
            $('#confrimForm input[name="num"]').val(num);
            $("#modal-confrim").modal();
            $('#modal-confrim').on('hidden.bs.modal', function (e) {
                $('#defaultForm :submit').prop("disabled", false)
            });
            e.preventDefault();
        });
    });
</script>
@stop
