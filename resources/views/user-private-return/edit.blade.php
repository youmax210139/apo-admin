@extends('layouts.base')

@section('title','用户私返')
@section('function','用户私返')
@section('function_link', '/userprivatereturn/')
@section('here','设置私返')

@section('content')
    <link rel="stylesheet" href="/assets/plugins/bootstrap-slider/bootstrap-slider.min.css">
    <style>
        .slider-selection {
            background: #3c8dbc;
        }

        .slider-track-high {
            background: #b3d5e6;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">设置私返契约</h3>
                </div>
                <div class="panel-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <form class="form-horizontal" role="form" method="POST" action="/userprivatereturn/edit">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="time_type" class="col-sm-3 control-label">时间类型</label>
                                <div class="col-sm-9">
                                    <select name="time_type" class="form-control">
                                        <option value="1" @if($time_type == 1) selected @endif>日</option>
                                        <option value="2" @if($time_type == 2) selected @endif>小时</option>
                                        <option value="3" @if($time_type == 3) selected @endif>奖期</option>
                                        <option value="4" @if($time_type == 4) selected @endif>实时</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rate_type" class="col-sm-3 control-label">私返类型</label>
                                <div class="col-sm-9">
                                    <select name="rate_type" class="form-control">
                                        <option value="1" @if($rate_type == 1) selected @endif>销量</option>
                                        <option value="2" @if($rate_type == 2) selected @endif>活跃人数</option>
                                        <option value="3" @if($rate_type == 3) selected @endif>销量+活跃人数</option>
                                        <option value="4" @if($rate_type == 4) selected @endif>盈亏</option>
                                        <option value="5" @if($rate_type == 5) selected @endif>销量+盈亏</option>
                                        <option value="6" @if($rate_type == 6) selected @endif>活跃人数+盈亏</option>
                                        <option value="7" @if($rate_type == 7) selected @endif>全部</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cardinal" class="col-sm-3 control-label">私返基数</label>
                                <div class="col-sm-9">
                                    <select name="cardinal" class="form-control">
                                        <option value="1" @if($cardinal == 1) selected @endif>销量</option>
                                        <option value="4" @if($cardinal == 4) selected @endif>盈亏</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="table">
                            <thead>
                                <tr>
                                    <th colspan="6" class="title">
                                        <span>调整 <b class="text-danger">{{$user->username}}</b> 用户私返契约</span>
                                        <a href="javascript:void(0);" class="add_tr">添加</a>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="1">销量({{$rate_unit}})</th>
                                    <th colspan="1">亏损({{$rate_unit}})</th>
                                    <th colspan="1">活跃人数</th>
                                    <th colspan="2">私返比率(%)</th>
                                    <th colspan="1">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($contracts)
                                    @foreach($contracts as $k=>$v)
                                        <tr>
                                            <td>
                                                <input type="text" value="{{$v['bet']}}" class="input" name="bet[]"/>
                                            </td>
                                            <td>
                                                <input type="text" value="{{$v['profit']}}" class="input" name="profit[]"/>
                                            </td>
                                            <td>
                                                <input type="text" value="{{$v['active']}}" class="input" name="active[]"/>
                                            </td>
                                            <td>
                                                <b>{{$rate_step}}</b>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input class="exSlider" data-slider-tooltip="hide" type="text" data-slider-min="{{$rate_step}}"
                                                    data-slider-max="{{$rate_limit}}" data-slider-step="{{$rate_step}}"
                                                    data-slider-value="{{$v['rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$rate_limit}}</b>
                                            </td>
                                            <td>
                                                <input type="text" value="{{$v['rate']}}" class="input rate" name="rate[]"/>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="del_tr">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="panel-footer text-center">
                            <button type="button" class="btn btn-warning btn-md" onclick="location.href='/userprivatereturn/index/?username={{$user->username}}'">
                                <i class="fa fa-minus-circle"></i> 返回契约列表
                            </button>
                            <button type="submit" class="btn btn-primary btn-md save" style="margin-left:10px;">
                                <i class="fa fa-plus-circle"></i> 保存
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_del_confirm" tabIndex="-1">
        <div class="modal-dialog modal-primary">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        <i class="fa fa-question-circle fa-lg"></i>
                        确认要删除 <span class="row_verify_text">该会员</span> 及其下级的私返契约吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" value="" id="delete_user_id">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-success" id="delete_confirm">
                        <i class="fa fa-check-circle-o"></i> 确认
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('css')
    <style>
        th, td {
            text-align: center;
        }

        .title {
            color: #31708f;
            background: #d9edf7;
        }

        .input {
            width: 70px;
            text-align: center;
            border: 1px solid darkgray;
            border-radius: 6px;
        }

        .input:focus {
            color: #a94442;
            font-weight: bold;
            border-color: #66afe9;
            outline: none;
        }

        .add_tr {
            float: right;
            font-weight: normal;
            color: #a94442;
        }

        .add_tr:hover {
            color: #a94442;
            font-weight: bold;
        }
    </style>
@stop
@section('js')
    <script src="/assets/plugins/bootstrap-slider/bootstrap-slider.min.js"></script>
    <script>
        $('#table .exSlider').slider();
        $('#table').on('slide', '.exSlider', function () {
            var index = $('#table .exSlider').index($(this));
            var value = $(this).val();
            $("input.rate:eq(" + index + ")").val(value);
        });

        //私返比率输入框
        $("#table").on('blur', '.input.rate', function () {
            var input = $(this);
            var slider = $('#table .exSlider').eq($(this).index()-1);
            var value = parseFloat(input.val());
            var max = parseFloat(slider.attr('data-slider-max'));
            var min = parseFloat(slider.attr('data-slider-step'));
            var digit = min.toString().split(".")[1].length;

            if (isNaN(value) || value < min) {
                value = min;
            }

            if (value > max) {
                value = max;
            }

            value = value.toFixed(digit);

            slider.slider('setValue', value);
            $(this).val(value);
        });

        //销量输入框
        $("#table").on('blur', '.input[name="bet"]', function () {
            var value = Math.abs(parseInt($(this).val()));
            if (isNaN(value) || value == 0) {
                value = 0;
            }
            $(this).val(value);
        });

        //盈亏输入框
        $("#table").on('blur', '.input[name="profit"]', function () {
            var value = Math.abs(parseInt($(this).val()));
            if (isNaN(value) || value == 0) {
                value = 0;
            }
            $(this).val(value);
        });

        //活跃人数输入框
        $("#table").on('blur', '.input[name="active"]', function () {
            var value = Math.abs(parseInt($(this).val()));
            if (isNaN(value) || value == 0) {
                value = 0;
            }
            $(this).val(value);
        });

        //输入框回车校验
        $("#table").on('keyup', '.input', function (event) {
            if (event.keyCode == 13) {
                $(this).blur();
            }
        });

        //添加行
        $('#table .add_tr').on('click', function () {
            var trHtml = '<tr>' +
                '<td><input type="text" value="0" class="input" name="bet[]" /></td>' +
                '<td><input type="text" value="0" class="input" name="profit[]" /></td>' +
                '<td><input type="text" value="0" class="input" name="active[]" /></td>' +
                '<td><b>{{$rate_step}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<input class="exSlider" data-slider-tooltip="hide" type="text" data-slider-min="{{$rate_step}}" ' +
                'data-slider-max="{{$rate_limit}}" data-slider-step="{{$rate_step}}" data-slider-value="{{$rate_step}}"/>' +
                '&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$rate_limit}}</b></td>' +
                '<td><input type="text" value="{{$rate_step}}" class="input rate" name="rate[]"/></td>' +
                '<td><a href="javascript:void(0);" class="del_tr">删除</a></td>' +
                '</tr>';

            $('#table tbody').append(trHtml);
            $('#table .exSlider:last').slider();
            $('#table .exSlider').eq($('#table .exSlider').size()-2).slider();
        });

        //删除行
        $('#table').on('click', '.del_tr', function () {
            $(this).parent().parent().remove();
        });

        //删除当前契约
        $(document).on('click', ".del", function () {
            $('#delete_user_id').val($(this).attr('user_id'));
            $(".row_verify_text").text($(this).attr('username'));
            $("#modal_del_confirm").modal();
        });
        $('#delete_confirm').click(function () {
            var user_id = $('#delete_user_id').val();
            loadShow();
            $.ajax({
                url: "/userprivatereturn/delete",
                dataType: "json",
                method: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    'user_id': user_id,
                },
            }).done(function (json) {
                loadFadeOut();
                $('#modal_del_confirm').modal('hide');
                $('#delete_user_id').val('');
                if (json.hasOwnProperty('code') && json.code == '302') {
                    window.location.reload();
                }
                var type = 'danger';
                if (json.status == 0) {
                    type = 'success';
                    location.reload();
                }
                $.notify({
                    title: '<strong>提示!</strong>',
                    message: json.msg
                }, {
                    type: type
                });
            });
        });
    </script>
@stop
