@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','用户积分修改')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">用户积分修改</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form role="form" class="form-horizontal" method="POST" id="defaultForm" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">用户名</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left">{{$user->username}}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">用户积分</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left"><code>{{$user->fund->points}}</code></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">积分</label>
                                    <div class="col-md-5">
                                        <input style="width: 180px"  type="text" name="point" class="form-control" placeholder="输入正整数积分">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label checkbox-inline text-bold">操作类型</label>
                                    <div class="col-md-6">
                                        <label class="radio-inline">
                                            <input type="radio" name="ordertype" checked="" value="0">
                                            增加
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="ordertype" value="1">
                                            扣除
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">备注</label>
                                    <div class="col-md-5">
                                        <input type="text" name="description" class="form-control" placeholder="请输入操作原因">
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
                    <label class="col-md-3 control-label">用户名</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left">{{$user->username}}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">用户积分</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><i>{{$user->fund->points}}</i></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">操作积分</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><b><code id="confrim-point"></code></b></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">操作类型</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left" id="confrim-ordertype"></p>
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
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="point" value="">
                    <input type="hidden" name="order_type" value="">
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
            	point: {
                    validators: {
                        notEmpty: {
                            message: '请输入积分!'
                        },
                        numeric:{
                            message: '积分必须是数字类型!'
                        }
                    }
                },description: {
                    validators: {
                        notEmpty: {
                            message: '请输入原因!'
                        }
                    }
                },
            }
        }).on('success.form.bv', function(e) {
                 var money = $('#defaultForm input[name="point"]').val();
                 var ordertype = $('#defaultForm input:checked').val();
                 var ordertype_text = $('#defaultForm input:checked').parent().text();
                 var description = $('#defaultForm input[name="description"]').val();

                 $("#confrim-point").html(money);
                 $('#confrimForm input[name="point"]').val(money);
                 $("#confrim-ordertype").html(ordertype_text);
                 $('#confrimForm input[name="order_type"]').val(ordertype);
                 $('#confrimForm input[name="ordertypetext"]').val(ordertype_text);
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