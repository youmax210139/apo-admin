@extends('layouts.base')

@section('title','活动管理')

@section('function','活动管理')
@section('function_link', '/activity/')

@section('here','发放礼金')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">发放礼金</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form role="form" class="form-horizontal" method="POST" id="defaultForm" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">活动名称</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left">{{$activity->name}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">活动介绍</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left">{{$activity->summary}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">金额</label>
                                    <div class="col-md-5">
                                        <input style="width: 180px"  type="text" name="money" class="form-control" placeholder="输入正整数金额">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">发放用户名</label>
                                    <div class="col-md-6 control-label">
                                        <textarea name="award_user" class="form-control" placeholder="多个用户名请使用,号隔开"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">备注</label>
                                    <div class="col-md-5">
                                        <input type="text" name="description" class="form-control" placeholder="请输入备注">
                                    </div>
                                </div>

                                <div class="form-group margin">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary btn-md col-sm-2">确定</button>
                                        <button type="button" class="btn btn-default btn-md col-sm-2 col-md-offset-1" onclick="history.back()">取消</button>
                                    </div>
                                </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-confrim" tabIndex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    ×
                </button>
                <h4 class="modal-title">操作信息确认</h4>
            </div>
            <div class="modal-body form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label">发放给</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left" id="comfrim-users"></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">发放金额</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><b><code id="confrim-point"></code></b></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">备注</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left" id="confrim-description"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form class="confrimForm" id="confrimForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="post">
                    <input type="hidden" name="money" value="">
                    <input type="hidden" name="award_user" value="">
                    <input type="hidden" name="description" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal">重新填写</button>
                    <button type="submit" class="btn btn-primary">
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
  $(document).ready(function() {
    $('#defaultForm')
        .bootstrapValidator({
            message: '该数据不可用',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                award_user: {
                    validators: {
                        notEmpty: {
                            message: '请输入用户名!'
                        }
                    }
                },
            	money: {
                    validators: {
                        notEmpty: {
                            message: '请输入礼金!'
                        },
                        numeric:{
                            message: '礼金必须是数字类型!'
                        }
                    }
                },description: {
                    validators: {
                        notEmpty: {
                            message: '请输入备注!'
                        }
                    }
                },
            }
        }).on('success.form.bv', function(e) {
                 var money = $('#defaultForm input[name="money"]').val();
                 var users = $('#defaultForm textarea[name="award_user"]').val();
                 var description = $('#defaultForm input[name="description"]').val();

                 $("#comfrim-users").html(users);
                 $('#confrimForm input[name="award_user"]').val(users);

                 $("#confrim-point").html(money);
                 $('#confrimForm input[name="money"]').val(money);

                 $("#confrim-description").html(description);
                 $('#confrimForm input[name="description"]').val(description);

        	 $("#modal-confrim").modal();
                 $('#modal-confrim').on('hidden.bs.modal', function (e) {
                   $('#defaultForm :submit').prop( "disabled", false )
                  });
        	e.preventDefault();
        });
});
</script>
@stop