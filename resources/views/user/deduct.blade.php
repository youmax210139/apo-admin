@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','人工扣款')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">人工扣款</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form role="form" class="form-horizontal" method="POST" id="defaultForm" onsubmit="return false;">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">用户名</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left">{{$user->username}}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">可用余额</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left"><code>{{$user->fund->balance}}</code></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">冻结金额</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left"><code>{{$user->fund->hold_balance}}</code></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">扣款金额(小写)</label>
                                    <div class="col-md-3">
                                        <input style="width: 180px" onkeyup="checkMoney(this, 'chineseMoney', {{$user->fund->balance}})" type="text" name="money" class="form-control" placeholder="输入金额"></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">扣款金额(大写)</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left"><code id="chineseMoney"></code></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label radio-inline text-bold">账变类型</label>
                                    <div class="col-md-6">
                                        @foreach($order_types as $_key => $order_type)
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="ordertype" @if($_key == 0) checked="" @endif value="{{$order_type->ident}}">
                                                {{$order_type->name}}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group" id="activity" style="display: none">
                                    <label for="tag" class="col-md-3 control-label">相关活动</label>
                                    <div class="col-md-5">
                                        <select name="activity_id" class="form-control">
                                            <option value="">请选择相关活动</option>
                                            @foreach($activities as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="thirdgame" style="display: none">
                                    <label for="tag" class="col-md-3 control-label">三方游戏平台</label>
                                    <div class="col-md-5">
                                        <select name="third_game_platform_id" class="form-control">
                                            <option value="">请选择</option>
                                            @foreach($thirdgames as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">扣款备注</label>
                                    <div class="col-md-5">
                                        <input type="text" name="description" class="form-control" placeholder="请输入扣款原因">
                                    </div>
                                </div>

                                <div class="form-group margin">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary btn-md" id="search_btn">用户扣款</button>
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
                    <h4 class="modal-title">扣款信息确认</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">用户名</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left">{{$user->username}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">可用余额</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><i>{{$user->fund->balance}}</i></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">冻结金额</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><i>{{$user->fund->holdbalance}}</i></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">扣款金额(小写)</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><b><code id="confrim-money"></code></b></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">扣款金额(大写)</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><code id="confrim-chineseMoney"></code></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">账变类型</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><span id="confrim-ordertype"></span></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">扣款备注</label>
                        <div class="col-md-6 control-label">
                            <div class="text-left">
                                <p id="confrim-description"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form class="confrimForm" id="confrimForm" method="POST" action="/user/deduct">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="userid" value="{{$user->id}}">
                        <input type="hidden" name="money" value="">
                        <input type="hidden" name="ordertype" value="">
                        <input type="hidden" name="ordertypetext" value="">
                        <input type="hidden" name="description" value="">
                        <input type="hidden" name="activity_id" value="">
                        <input type="hidden" name="third_game_platform_id" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">重新填写</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa"></i> 确认无误扣款
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
            $("input[type='radio']").click(function () {
                $("#thirdgame").hide();
                $("#activity").hide();
                if ($(this).val() == 'YCYLKJ') {
                    $("#activity").show();
                } else if ($(this).val() == 'SFYCYLKJ') {
                    $("#thirdgame").show();
                }
            });
            $('#defaultForm')
                .bootstrapValidator({
                    message: '该数据不可用',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        money: {
                            validators: {
                                notEmpty: {
                                    message: '请输入充值金额!'
                                },
                                numeric: {
                                    message: '金额必须是数字类型!'
                                }
                            }
                        }, description: {
                            validators: {
                                notEmpty: {
                                    message: '请输入原因!'
                                }
                            }
                        },
                    }
                }).on('success.form.bv', function (e) {
                var money = $('#defaultForm input[name="money"]').val();
                var ordertype = $('#defaultForm input:checked').val();
                var ordertype_text = $('#defaultForm input:checked').parent().text();
                var description = $('#defaultForm input[name="description"]').val();

                $("#confrim-money").html(money);
                $('#confrimForm input[name="money"]').val(money);
                $("#confrim-chineseMoney").html($("#chineseMoney").html());
                $("#confrim-ordertype").html(ordertype_text);
                $('#confrimForm input[name="ordertype"]').val(ordertype);
                $('#confrimForm input[name="ordertypetext"]').val(ordertype_text);
                $("#confrim-description").html(description);
                $('#confrimForm input[name="description"]').val(description);
                $('#confrimForm input[name="activity_id"]').val($('#defaultForm select[name="activity_id"]').val());
                $('#confrimForm input[name="third_game_platform_id"]').val($('#defaultForm select[name="third_game_platform_id"]').val());

                $("#modal-confrim").modal();
                $('#modal-confrim').on('hidden.bs.modal', function (e) {
                    $('#defaultForm :submit').prop("disabled", false)
                });
                e.preventDefault();
            });
        });
    </script>
@stop