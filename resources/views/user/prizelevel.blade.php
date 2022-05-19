@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','总代奖级调整')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">总代奖级调整</h3>
                    </div>
                    <div class="panel-body">
                        @include('partials.errors')
                        @include('partials.success')
                        <form role="form" class="form-horizontal" method="POST" id="defaultForm">
                             <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="POST">
                            <div class="form-group">
                                <label class="col-md-3 control-label">用户名</label>
                                <div class="col-md-5">
                                    <input readonly="true" value="{{ $user->username }}" type="text" name="username" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">返点</label>
                                <div class="col-md-5">
                                    <input readonly="true" value="{{ $rebate }}" type="text" name="rebate" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">原奖金级别</label>
                                <div class="col-md-5">
                                    <input readonly="true" value="{{ $level }}" type="text" name="old_level" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">新奖金级别</label>
                                <div class="col-md-5">
                                    <select name="user_prize_level" class="form-control" id="user_prize_level">
                                        <option value="">请选择奖金级别</option>
                                        @for($i=2000; $i >= 1600; $i-=10)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label"></label>
                                <div class="col-md-5" id="change_msg"></div>
                            </div>

                            <div class="form-group margin">
                                <div class="col-md-7 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary btn-md col-sm-2">确定调整</button>
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
@stop

@section('js')
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script>
    var level = {{ $level }};
    var rebate = {{ $rebate }};
    $('#user_prize_level').bind('change', function () {
        var new_level = $(this).val();
        var rebate_diff = (level - new_level) / 2000;
        var new_rebate = (rebate + rebate_diff).toFixed(4);
        var msg = "调整前：<span style='color:red;'>" + level + "</span>，返点：<span style='color:red;'>" + rebate + "</span>";
        msg += "<br />调整后：<span style='color:red;'>" + new_level + "</span>，返点：<span style='color:red;'>" + new_rebate + "</span>";
        msg += "<br />整条线所有成员返点比例将：<span style='color:red;'>";
        if (new_rebate > 0) {
            msg += (rebate_diff > 0 ? "+" : "") + rebate_diff;
        } else {
            msg += "清零";
        }
        msg += "</span>";
        if(rebate_diff < 0){
            msg += "<br/>下调返点中，如果下级返点比例扣减后小于零，将被清零处理";
        }
        $('#change_msg').html(msg);
    });

    $('#defaultForm')
        .bootstrapValidator({
            message: '该数据不可用',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                old_level:{
                },
                user_prize_level: {
                    validators: {
                        notEmpty: {message: '请选择奖金级别'},
                        different: {
                            field: 'old_level',
                            message: '新奖金级别不能和原奖金级别相同'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
        BootstrapDialog.confirm({
            message: "确定要提交吗？注意，此操作将会修改此总代及其所有下级返点比例且数据不可恢复！！！",
            type: BootstrapDialog.TYPE_WARNING,
            closable: true,
            draggable: true,
            btnCancelLabel: '取消',
            btnOKLabel: '确认提交',
            btnOKClass: 'btn-warning',
            callback: function(result) {
                if (result) {
                    loadShow();
                    $.ajax({
                        url: "/user/prizelevel",
                        dataType: "json",
                        method: "POST",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {
                            user_id: '{{ $user->id }}',
                            user_prize_level: $('#user_prize_level').val()
                        }
                    }).done(function (json) {
                        $.notify({
                            title: '<strong>提示!</strong>',
                            message: json.msg
                        },{
                            type: json.status == 0 ? 'success' : 'danger'
                        });
                        loadFadeOut();
                    });
                }
            }});

            return false;
        });
</script>
@stop