@extends('layouts.base')
@section('title','用户日工资')
@section('function','用户日工资')
@section('function_link', '/userdailywagecontract/')
@section('here','设置日工资')
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
    @if($wage_line_multi_available == 1 && $lines)
        <div class="row page-title-row" style="margin:5px;">
            <div class="col-md-12">
                @foreach($lines as $tmp_line)
                    <a href="{{url('userdailywagecontract/edit')}}?user_id={{$user->id}}&wage_type={{$tmp_line->type}}"
                       type="button" class="btn @if($line->type == $tmp_line->type) btn-primary @else  btn-default @endif ">{{__("wage.line_type_".$tmp_line->type)}}</a>
                @endforeach
                <div class="btn-group" style="float: right">
                    <button user_id="{{$user->id}}" username="{{$user->username}}" wage_type="{{$line->type}}" type="button" class="btn btn-danger del">清除团队【{{__("wage.line_type_".$line->type)}}】契约</button>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">设置工资契约 [ {{__('wage.line_type_'.$line->type)}} ] </h3>
                </div>
                <div class="panel-body">
                    @include('partials.errors')
                    @include('partials.success')
                    @if($parent_user)
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th colspan="{{$col_span_count}}" class="title">上级 <span
                                        class="text-danger">{{$parent_user->username}}</span> 用户工资契约
                                </th>
                            </tr>
                            <tr>
                                @if($show_conditions['bet'])
                                    @if($line->type == 6 && in_array(get_config('dailywage_type_ident'), ['Daqin']))
                                        <th>注单金额</th>
                                    @else
                                        <th>日销量</th>
                                    @endif
                                @endif
                                @if($show_conditions['active'])
                                    <th>活跃人数</th>
                                @endif
                                @if($show_conditions['profit'])
                                    <th>日亏损({{ $line->unit_name }})</th>
                                @endif
                                @if($show_conditions['rate'])
                                    <th>工资比率(%)</th>
                                @endif
                                @if($line->type == 8 && in_array(get_config('dailywage_type_ident'), ['Huaxin']))
                                    @if($show_conditions['win_rate'])
                                        <th>盈利会员工资比例(%)</th>
                                    @endif
                                    @if($show_conditions['loss_rate'])
                                        <th>亏损会员工资比例(%)</th>
                                    @endif
                                @else
                                    @if($show_conditions['win_rate'])
                                        <th>中单工资比例(%)</th>
                                    @endif
                                    @if($show_conditions['loss_rate'])
                                        <th>挂单工资比例(%)</th>
                                    @endif
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @if($parent_contracts)
                                @foreach($parent_contracts as $v)
                                    <tr>
                                        @if($show_conditions['bet'])
                                            <th>{{ $v['bet'] }}</th>
                                        @endif
                                        @if($show_conditions['active'])
                                            <th>{{ $v['active'] }}</th>
                                        @endif
                                        @if($show_conditions['profit'])
                                            <th>{{ isset($v['profit']) ? $v['profit'] : '-' }}</th>
                                        @endif
                                        @if($show_conditions['rate'])
                                            <th>{{ $v['rate'] }}(%)</th>
                                        @endif
                                        @if($show_conditions['win_rate'])
                                            <th>{{ $v['win_rate'] }}(%)</th>
                                        @endif
                                        @if($show_conditions['loss_rate'])
                                            <th>{{ $v['loss_rate'] }}(%)</th>
                                        @endif
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
                    <form class="form-horizontal" role="form" method="POST" action="/userdailywagecontract/edit">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="wage_type" value="{{$line->type}}"><!--{{$user_contract_type}}-->
                        <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
                        <table class="table table-striped table-bordered table-hover" id="table">
                            <thead>
                            <tr>
                                <th colspan="@if( $line->type == 2 || $dailywage_check_profit) 6 @elseif(in_array($line->type,[6,7])) 6 @elseif($line->type == 8) 7 @else 5 @endif" class="title">
                                    <span>调整 <b class="text-danger">{{$user->username}}</b> 用户工资契约</span>
                                    @if($line->type == 3 && $check_active_user)
                                        <a href="javascript:void(0);" class="add_tr" hidden>添加</a>
                                    @else
                                        <a href="javascript:void(0);" class="add_tr">添加</a>
                                    @endif
                                </th>
                            </tr>
                            @if( $line->type == 3 )
                                @if($check_active_user)
                                    <tr>
                                        <th>活跃人数</th>
                                        <th colspan="2">工资比率(%)</th>
                                    </tr>
                                @else
                                    <tr>
                                        <th colspan="2">中单工资比率(%)</th>
                                        <th colspan="2">挂单工资比率(%)</th>
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                            @if($contracts)
                                @foreach($contracts as $k=>$v)
                                    @if($check_active_user)
                                        <tr>
                                            <td><input type="text" value="{{$v['active']  or '正在更新缓存稍后在打开'}}" class="input" name="active[]"/></td>

                                            <td><b>{{$dailywage_step}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
                                                    class="exSlider"
                                                    data-slider-tooltip="hide"
                                                    type="text" data-slider-min="{{$dailywage_step}}"
                                                    data-slider-max="{{$dailywage_loss_limit}}"
                                                    data-slider-step="{{$dailywage_step}}"
                                                    data-slider-value="{{$v['loss_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$dailywage_loss_limit}}</b>
                                            </td>
                                            <td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td><b>{{$dailywage_step}}</b>
                                                <input
                                                    class="exSlider"
                                                    data-slider-tooltip="hide"
                                                    type="text" data-slider-min="{{$dailywage_step}}"
                                                    data-slider-max="{{$dailywage_win_limit}}"
                                                    data-slider-step="{{$dailywage_step}}"
                                                    data-slider-value="{{$v['win_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$dailywage_win_limit}}</b>
                                            </td>
                                            <td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>
                                            <td><b>{{$dailywage_step}}</b>
                                                <input
                                                    class="exSlider"
                                                    data-slider-tooltip="hide"
                                                    type="text" data-slider-min="{{$dailywage_step}}"
                                                    data-slider-max="{{$dailywage_loss_limit}}"
                                                    data-slider-step="{{$dailywage_step}}"
                                                    data-slider-value="{{$v['loss_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$dailywage_loss_limit}}</b>
                                            </td>
                                            <td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            @elseif($line->type == 5)
                                <tr>
                                    @if($check_active_user)
                                        <th>活跃人数</th>
                                    @endif
                                    @if($check_win_rate)
                                        <th colspan="2">中单工资比率(%)</th>
                                    @endif
                                    <th colspan="2">挂单工资比率(%)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($contracts)
                                @foreach($contracts as $k=>$v)
                                    <tr>
                                        @if($check_active_user)
                                            <td><input type="text" value="{{$v['active']  or ''}}" class="input" name="active[]"/></td>
                                        @endif
                                        @if($check_win_rate)
                                            <td><b>{{$dailywage_step}}</b>
                                                <input
                                                    class="exSlider"
                                                    data-slider-tooltip="hide"
                                                    type="text" data-slider-min="{{$dailywage_step}}"
                                                    data-slider-max="{{$dailywage_win_limit}}"
                                                    data-slider-step="{{$dailywage_step}}"
                                                    data-slider-value="{{$v['win_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$dailywage_win_limit}}</b>
                                            </td>
                                            <td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>
                                        @endif
                                        <td><b>{{$dailywage_step}}</b>
                                            <input
                                                class="exSlider"
                                                data-slider-tooltip="hide"
                                                type="text" data-slider-min="{{$dailywage_step}}"
                                                data-slider-max="{{$dailywage_loss_limit}}"
                                                data-slider-step="{{$dailywage_step}}"
                                                data-slider-value="{{$v['loss_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <b>{{$dailywage_loss_limit}}</b>
                                        </td>
                                        <td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>
                                    </tr>
                                @endforeach
                            @endif

                            @elseif(in_array($line->type,[6,7]))
                                <tr>
                                    @if($check_bet)
                                        @if(in_array(get_config('dailywage_type_ident'), ['Daqin']))
                                            <th>注单金额</th>
                                        @else
                                            <th>前一日销量(万)</th>
                                        @endif
                                    @endif
                                    @if($check_active_user)
                                        <th>活跃人数</th>
                                    @endif
                                    @if($check_win_rate)
                                        <th colspan="2">中单工资比率(%)</th>
                                    @endif
                                    <th colspan="2">挂单工资比率(%)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($contracts)
                                @foreach($contracts as $k=>$v)
                                    <tr>
                                        @if($check_bet)
                                            <td><input type="text" value="{{$v['bet']  or ''}}" class="input" name="bet[]"/></td>
                                        @endif
                                        @if($check_active_user)
                                            <td><input type="text" value="{{$v['active']  or ''}}" class="input" name="active[]"/></td>
                                        @endif
                                        @if($check_win_rate)
                                            <td><b>{{$dailywage_step}}</b>
                                                <input
                                                    class="exSlider"
                                                    data-slider-tooltip="hide"
                                                    type="text" data-slider-min="{{$dailywage_step}}"
                                                    data-slider-max="{{$dailywage_win_limit}}"
                                                    data-slider-step="{{$dailywage_step}}"
                                                    data-slider-value="{{$v['win_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$dailywage_win_limit}}</b>
                                            </td>
                                            <td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>
                                        @endif
                                        <td><b>{{$dailywage_step}}</b>
                                            <input
                                                class="exSlider"
                                                data-slider-tooltip="hide"
                                                type="text" data-slider-min="{{$dailywage_step}}"
                                                data-slider-max="{{$dailywage_loss_limit}}"
                                                data-slider-step="{{$dailywage_step}}"
                                                data-slider-value="{{$v['loss_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <b>{{$dailywage_loss_limit}}</b>
                                        </td>
                                        <td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>
                                    </tr>
                                @endforeach
                            @endif

                            @elseif($line->type == 2)
                                <tr>
                                    <!--
                                    <th>5日销量</th>
                                    <th>15日销量</th>
                                    -->
                                    @if( $dailywage_check_user )
                                        <th>活跃人数</th>
                                    @endif
                                    <th colspan="2">工资比率(%)</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($contracts)
                                @foreach($contracts as $k=>$v)
                                    <tr>
                                        @if( $dailywage_check_user )
                                            <td><input type="text" value="{{$v['active']}}" class="input active" name="active[]"/></td>
                                        @endif
                                        <td><b>{{$dailywage_step}}  </b> <input
                                                class="exSlider"
                                                data-slider-tooltip="hide"
                                                type="text" data-slider-min="{{$dailywage_step}}"
                                                data-slider-max="{{$dailywage_limit}}"
                                                data-slider-step="{{$dailywage_step}}"
                                                data-slider-value="{{$v['rate']}}"/>
                                            <b> {{$dailywage_limit}}</b>
                                        </td>
                                        <td><input type="text" value="{{$v['rate']}}" class="input rate" name="rate[]"/></td>
                                        <td><a href="javascript:void(0);" class="del_tr">删除</a></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    @if( $dailywage_check_user )
                                        <td><input type="text" value="0" class="input active" name="active[]"/></td>
                                    @endif
                                    <td><b>{{$dailywage_step}}</b>
                                        <input class="exSlider" data-slider-tooltip="hide" type="text" data-slider-min="{{$dailywage_step}}"
                                               data-slider-max="{{$dailywage_limit}}" data-slider-step="{{$dailywage_step}}" data-slider-value="{{$dailywage_step}}"/>
                                        <b>{{$dailywage_limit}}</b></td>
                                    <td><input type="text" value="{{$dailywage_step}}" class="input rate" name="rate[]"/></td>
                                    <td></td>
                                </tr>
                            @endif
                            @elseif($line->type == 8)
                                <tr>
                                    @if($check_bet)
                                        <th>销量</th>
                                    @endif
                                    @if($check_active_user)
                                        <th>活跃人数</th>
                                    @endif
                                    @if($check_profit)
                                        <th>日亏损({{ $line->unit_name }})</th>
                                    @endif
                                    @if($check_win_rate)
                                        <th colspan="2">盈利会员工资比例(%)</th>
                                    @endif
                                    @if($check_loss_rate)
                                        <th colspan="2">亏损会员工资比例(%)</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @if($contracts)
                                @foreach($contracts as $k=>$v)
                                    <tr>
                                        @if($check_bet)
                                            <td><input type="text" value="{{$v['bet'] or ''}}" class="input" name="bet[]"/></td>
                                        @endif
                                        @if($check_active_user)
                                            <td><input type="text" value="{{$v['active'] or ''}}" class="input" name="active[]"/></td>
                                        @endif
                                        @if($check_profit)
                                            <td><input type="text" value="{{$v['profit'] or ''}}" class="input" name="profit[]"/></td>
                                        @endif
                                        @if($check_win_rate)
                                            <td><b>{{$dailywage_step}}</b>
                                                <input
                                                    class="exSlider"
                                                    data-slider-tooltip="hide"
                                                    type="text" data-slider-min="{{$dailywage_step}}"
                                                    data-slider-max="{{$dailywage_win_limit}}"
                                                    data-slider-step="{{$dailywage_step}}"
                                                    data-slider-value="{{$v['win_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$dailywage_win_limit}}</b>
                                            </td>
                                            <td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>
                                        @endif
                                        @if($check_loss_rate)
                                            <td><b>{{$dailywage_step}}</b>
                                                <input
                                                    class="exSlider"
                                                    data-slider-tooltip="hide"
                                                    type="text" data-slider-min="{{$dailywage_step}}"
                                                    data-slider-max="{{$dailywage_loss_limit}}"
                                                    data-slider-step="{{$dailywage_step}}"
                                                    data-slider-value="{{$v['loss_rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>{{$dailywage_loss_limit}}</b>
                                            </td>
                                            <td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                            @else
                                <tr>
                                    @if($check_bet)
                                        <th>销量({{ $line->unit_name }})</th>
                                    @endif
                                    @if( $dailywage_check_profit )
                                        <th>日亏损({{ $line->unit_name }})</th>
                                    @endif
                                    @if( $dailywage_check_user )
                                        <th>活跃人数</th>
                                    @endif
                                    <th colspan="2">工资比率(%)</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($contracts)
                                @foreach($contracts as $k=>$v)
                                    <tr>
                                        @if($check_bet)
                                            <td><input type="text" value="{{$v['bet']  or ''}}" class="input" name="bet[]"/></td>
                                        @endif

                                        @if( $dailywage_check_profit )
                                            <td><input type="text" value="{{$v['profit']}}" class="input" name="profit[]"/></td>
                                        @endif
                                        @if( $dailywage_check_user )
                                            <td><input type="text" value="{{$v['active']}}" class="input" name="active[]"/></td>
                                        @endif
                                        <td><b>{{$dailywage_step}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
                                                class="exSlider"
                                                data-slider-tooltip="hide"
                                                type="text" data-slider-min="{{$dailywage_step}}"
                                                data-slider-max="{{$dailywage_limit}}"
                                                data-slider-step="{{$dailywage_step}}"
                                                data-slider-value="{{$v['rate']}}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <b>{{$dailywage_limit}}</b>
                                        </td>
                                        <td><input type="text" value="{{$v['rate']}}" class="input rate" name="rate[]"/></td>
                                        <td><a href="javascript:void(0);" class="del_tr">删除</a></td>
                                    </tr>
                                @endforeach
                            @endif
                            @endif
                            </tbody>
                        </table>
                        <div class="panel-footer text-center">
                            <button type="button" class="btn btn-warning btn-md" onclick="location.href='/userdailywagecontract/index/?username={{$user->username}}'"><i
                                    class="fa fa-minus-circle"></i> 返回契约列表
                            </button>
                            <button type="submit" class="btn btn-primary btn-md save" style="margin-left:10px;"><i
                                    class="fa fa-plus-circle"></i> 保存
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
                        确认要删除 <span class="row_verify_text">该会员</span> 及其下级的【{{__("wage.line_type_".$line->type)}}】工资契约吗?
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
            var slider = $('#table .exSlider').eq($(this).index() - 1);
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
            @if( $line->type == 2 )
            var trHtml = '<tr>' +
                //'<td><input type="text" value="1" class="input" name="bet_5[]" /></td>' +
                //'<td><input type="text" value="1" class="input" name="bet_15[]" /></td>' +
                @if( $dailywage_check_user )
                    '<td><input type="text" value="0" class="input" name="active[]" /></td>' +
                @endif
                    '<td><b>{{$dailywage_step}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<input class="exSlider" data-slider-tooltip="hide" type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_limit}}" data-slider-step="{{$dailywage_step}}" data-slider-value="{{$dailywage_step}}"/>' +
                '&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$dailywage_limit}}</b></td>' +
                '<td><input type="text" value="{{$dailywage_step}}" class="input rate" name="rate[]"/></td>' +
                '<td><a href="javascript:void(0);" class="del_tr">删除</a></td>' +
                '</tr>';
            @elseif ( $line->type == 5 )
            var trHtml = '<tr>' +
                @if($check_active_user)
                    '<td><input type="text" value="0" class="input" name="active[]" /></td>' +
                @endif
                    @if($check_win_rate)
                    '<td><b>{{$dailywage_step}}</b>' +
                '<input ' +
                'class="exSlider" ' +
                'data-slider-tooltip="hide" ' +
                'type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_win_limit}}" ' +
                'data-slider-step="{{$dailywage_step}}" ' +
                'data-slider-value="{{$dailywage_win_limit}}"/>&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<b>{{$dailywage_win_limit}}</b>' +
                '</td>' +
                '<td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>' +
                @endif
                    '<td><b>{{$dailywage_step}}</b>' +
                '<input ' +
                'class="exSlider" ' +
                'data-slider-tooltip="hide" ' +
                'type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_loss_limit}}" ' +
                'data-slider-step="{{$dailywage_step}}" ' +
                'data-slider-value="{{$dailywage_loss_limit}}"/> &nbsp;&nbsp;&nbsp;&nbsp;' +
                '<b>{{$dailywage_loss_limit}}</b>' +
                '</td>' +
                '<td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>' +
                ' </tr>';
            @elseif ( in_array($line->type,[6,7]))
            var trHtml = '<tr>' +
                @if($check_bet)
                    '<td><input type="text" value="0" class="input" name="bet[]" /></td>' +
                @endif
                    @if($check_active_user)
                    '<td><input type="text" value="0" class="input" name="active[]" /></td>' +
                @endif
                    @if($check_win_rate)
                    '<td><b>{{$dailywage_step}}</b>' +
                '<input ' +
                'class="exSlider" ' +
                'data-slider-tooltip="hide" ' +
                'type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_win_limit}}" ' +
                'data-slider-step="{{$dailywage_step}}" ' +
                'data-slider-value="{{$dailywage_win_limit}}"/>&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<b>{{$dailywage_win_limit}}</b>' +
                '</td>' +
                '<td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>' +
                @endif
                    '<td><b>{{$dailywage_step}}</b>' +
                '<input ' +
                'class="exSlider" ' +
                'data-slider-tooltip="hide" ' +
                'type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_loss_limit}}" ' +
                'data-slider-step="{{$dailywage_step}}" ' +
                'data-slider-value="{{$dailywage_loss_limit}}"/> &nbsp;&nbsp;&nbsp;&nbsp;' +
                '<b>{{$dailywage_loss_limit}}</b>' +
                '</td>' +
                '<td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>' +
                '<td><a href="javascript:void(0);" class="del_tr">删除</a></td>' +
                ' </tr>';
            @elseif ( $line->type == 3 )
            @if($check_active_user)
            var trHtml = '<tr>' +
                '<td><input type="text" value="0" class="input" name="active[]" /></td>' +
                '<td><b>{{$dailywage_step}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<input ' +
                'class="exSlider" data-slider-tooltip="hide" ' +
                'type="text" ' + 'data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_loss_limit}}" data-slider-step="{{$dailywage_step}}" data-slider-value="{{$dailywage_step}}"/>' +
                '&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$dailywage_loss_limit}}</b></td>' +
                '<td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="win_rate[]"/></td>' +
                '<td><a href="javascript:void(0);" class="del_tr">删除</a></td>' +
                '</tr>';
            @else
            var trHtml = '<tr>' +
                '<td><b>{{$dailywage_step}}</b>' +
                '<input ' +
                'class="exSlider" ' +
                'data-slider-tooltip="hide" ' +
                'type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_win_limit}}" ' +
                'data-slider-step="{{$dailywage_step}}" ' +
                'data-slider-value="{{$dailywage_win_limit}}"/>&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<b>{{$dailywage_win_limit}}</b>' +
                '</td>' +
                '<td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>' +
                '<td><b>{{$dailywage_step}}</b>' +
                '<input ' +
                'class="exSlider" ' +
                'data-slider-tooltip="hide" ' +
                'type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_loss_limit}}" ' +
                'data-slider-step="{{$dailywage_step}}" ' +
                'data-slider-value="{{$dailywage_loss_limit}}"/> &nbsp;&nbsp;&nbsp;&nbsp;' +
                '<b>{{$dailywage_loss_limit}}</b>' +
                '</td>' +
                '<td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>' +
                ' </tr>';
            @endif
            @elseif ($line->type == 8)
            var trHtml = '<tr>' +
                @if($check_bet)
                    '<td><input type="text" value="0" class="input" name="bet[]" /></td>' +
                @endif
                @if($check_active_user)
                    '<td><input type="text" value="0" class="input" name="active[]" /></td>' +
                @endif
                @if($check_profit)
                    '<td><input type="text" value="0" class="input" name="profit[]" /></td>' +
                @endif
                @if($check_win_rate)
                    '<td><b>{{$dailywage_step}}</b>' +
                    '<input ' +
                    'class="exSlider" ' +
                    'data-slider-tooltip="hide" ' +
                    'type="text" data-slider-min="{{$dailywage_step}}" ' +
                    'data-slider-max="{{$dailywage_win_limit}}" ' +
                    'data-slider-step="{{$dailywage_step}}" ' +
                    'data-slider-value="{{$dailywage_win_limit}}"/>&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '<b>{{$dailywage_win_limit}}</b>' +
                    '</td>' +
                    '<td><input type="text" value="{{$v['win_rate']}}" class="input rate" name="win_rate[]"/></td>' +
                @endif
                @if($check_loss_rate)
                    '<td><b>{{$dailywage_step}}</b>' +
                    '<input ' +
                    'class="exSlider" ' +
                    'data-slider-tooltip="hide" ' +
                    'type="text" data-slider-min="{{$dailywage_step}}" ' +
                    'data-slider-max="{{$dailywage_loss_limit}}" ' +
                    'data-slider-step="{{$dailywage_step}}" ' +
                    'data-slider-value="{{$dailywage_loss_limit}}"/>&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '<b>{{$dailywage_loss_limit}}</b>' +
                    '</td>' +
                    '<td><input type="text" value="{{$v['loss_rate']}}" class="input rate" name="loss_rate[]"/></td>' +
                @endif
                '<td><a href="javascript:void(0);" class="del_tr">删除</a></td></tr>';
            @else
            var trHtml = '<tr>' +
                @if($check_bet)
                    '<td><input type="text" value="0" class="input" name="bet[]" /></td>' +
                @endif

                    @if($dailywage_check_profit)
                    '<td><input type="text" value="0" class="input" name="profit[]" /></td>' +
                @endif
                    @if($dailywage_check_user)
                    '<td><input type="text" value="0" class="input" name="active[]" /></td>' +
                @endif
                    '<td><b>{{$dailywage_step}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<input class="exSlider" data-slider-tooltip="hide" type="text" data-slider-min="{{$dailywage_step}}" ' +
                'data-slider-max="{{$dailywage_limit}}" data-slider-step="{{$dailywage_step}}" data-slider-value="{{$dailywage_step}}"/>' +
                '&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$dailywage_limit}}</b></td>' +
                '<td><input type="text" value="{{$dailywage_step}}" class="input rate" name="rate[]"/></td>' +
                '<td><a href="javascript:void(0);" class="del_tr">删除</a></td>' +
                '</tr>';
            @endif

            $('#table tbody').append(trHtml);
            $('#table .exSlider:last').slider();
            $('#table .exSlider').eq($('#table .exSlider').size() - 2).slider();
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
                url: "/userdailywagecontract/delete",
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
