@extends('layouts.base')
@section('title','抽返水规则')
@section('function','抽返水规则')
@section('function_link', '/pumprule/')
@section('here','添加')
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
                    <h3 class="panel-title">设置返水规则 [ {{$self->user_type_name}} <b >{{$self->username}}</b> ] </h3>
                </div>
                <div class="panel-body">
                    @include('partials.errors')
                    @include('partials.success')
                    @if($top && $top->user_id  <> $self->top_id)
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th colspan="{{count($conditions)}}" class="title">
                                    总代 {{$top->username}} 返水规则 [ @if($top->rule_content['enable'] == 1)  <span class="text-success">开启</span> @else <span class="text-danger">关闭</span> @endif]
                                </th>
                            </tr>
                            <tr>
                                @foreach($conditions as $condition)
                                    <th>{{$condition}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @if($top->rule_content['inlet'])
                                @foreach($top->rule_content['inlet'] as $rule)
                                    <tr>
                                        @foreach($conditions as $c_key => $condition)
                                            <th>{{$rule[$c_key]}}</th>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">暂无数据</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    @endif
                    @if($parent)
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th colspan="{{count($conditions)}}" class="title">上级 <span class="text-danger">{{$parent->username}}</span> 用户工资契约
                                </th>
                            </tr>
                            <tr>
                                @foreach($conditions as $condition)
                                    <th>{{$condition}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @if($parent->rule_content['inlet'])
                                @foreach($parent->rule_content['inlet'] as $rule)
                                    <tr>
                                        @foreach($conditions as $c_key => $condition)
                                            <th>{{$rule[$c_key]}}</th>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">暂无数据</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    @endif
                    <form class="form-horizontal" role="form" method="POST" action="/pumprule/create">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="step" value="1">
                        <input type="hidden" name="user_id" id="user_id" value="{{$self->user_id}}">
                        <table class="table table-striped table-bordered table-hover" id="table">
                            <thead>
                            <tr>
                                <th colspan="{{count($conditions)+2}}" class="title">
                                    <span>调整 {{$self->user_type_name}} <b class="text-danger">{{$self->username}}</b> 返水规则</span>
                                    <a href="javascript:void(0);" class="add_tr">添加</a>
                                </th>
                            </tr>
                            <tr>
                                @foreach($conditions as $condition)
                                    <th>{{$condition}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @if($self->rule_content['inlet'])
                                @foreach($self->rule_content['inlet'] as $rule)
                                    <tr>
                                        @foreach($conditions as $c_key=>$condition)
                                            @if($c_key == 'scale')
                                                <td><b>0.01</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
                                                            class="exSlider"
                                                            data-slider-tooltip="hide"
                                                            type="text" data-slider-min="0.01"
                                                            data-slider-max="1"
                                                            data-slider-step="0.01"
                                                            data-slider-value="{{$rule[$c_key]}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <b>{1</b>
                                                </td>
                                                <td><input type="text" value="{{$rule[$c_key]}}" class="input rate" name="{{$c_key}}[]"/></td>
                                            @else
                                                <td><input type="text" value="{{$rule[$c_key]  or '正在更新缓存稍后在打开'}}" class="input" name="{{$c_key}}[]"/></td>
                                            @endif
                                        @endforeach
                                        <td><a href="javascript:void(0);" class="del_tr">删除</a></td>
                                    <tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div class="panel-footer text-center">
                            <button type="button" class="btn btn-warning btn-md" onclick="location.href='/pumprule/index/?username={{$self->username}}'"><i
                                        class="fa fa-minus-circle"></i> 返回规则列表
                            </button>
                            <button type="submit" class="btn btn-primary btn-md save" style="margin-left:10px;"><i
                                        class="fa fa-plus-circle"></i> 下一步
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
                        确认要删除 <span class="row_verify_text">该会员</span> 及其下级的返水规则吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" value="" id="delete_user_id">
                    <input type="hidden" value="" id="delete_wage_type">
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

        //日工资比率输入框
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

        //日销量和活跃人数输入框
        $("#table").on('blur', '.input[name="bet"],input[name="active"]', function () {
            var value = Math.abs(parseInt($(this).val()));
            if (isNaN(value) || value == 0) {
                value = 1;
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
                    @foreach($conditions as $c_key=>$condition)
                            @if($c_key == 'scale')
                        '<td><b>0.1</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<input class="exSlider" data-slider-tooltip="hide" type="text" data-slider-min="0.1" ' +
                'data-slider-max="1" data-slider-step="0.1" data-slider-value="0.1"/>' +
                '&nbsp;&nbsp;&nbsp;&nbsp;<b>1</b></td>' +
                '<td><input type="text" value="0.1" class="input rate" name="{{$c_key}}[]"/></td>' +
                '<td><a href="javascript:void(0);" class="del_tr">删除</a></td>' +
                    @else
                        '<td><input type="text" value="0" class="input" name="{{$c_key}}[]" /></td>'+
                    @endif
                            @endforeach
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
            $('#delete_wage_type').val($(this).attr('wage_type'));
            $(".row_verify_text").text($(this).attr('username'));
            $("#modal_del_confirm").modal();
        });
        $('#delete_confirm').click(function () {
            var user_id = $('#delete_user_id').val();
            var wage_type = $('#delete_wage_type').val();
            loadShow();
            $.ajax({
                url: "/pumprule/delete",
                dataType: "json",
                method: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    'user_id': user_id,
                    'wage_type': wage_type,
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
