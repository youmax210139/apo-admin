@extends('layouts.base')
@if (request()->path() == 'lotterybonus')
    @section('title','彩票分红报表(汇总版)')
    @section('function','彩票分红报表(汇总版)')
    @section('function_link', '/lotterybonus/')
    @section('here','彩票分红报表(汇总版)')
@elseif  (request()->path() == 'lotterybonusrealtime')
    @section('title','彩票分红报表(实时版)')
    @section('function','彩票分红报表(实时版)')
    @section('function_link', '/lotterybonusrealtime/')
    @section('here','彩票分红报表(实时版)')
@endif

@section('content')
    <div class="row">
        <div class="col-sm-12">
        @include('partials.errors')
        @include('partials.success')
        <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search">
                    <div class="box-header with-border">
                        <h3 class="box-title"><!--搜索查询区--></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="username" class="col-sm-3 col-sm-3 control-label">用户名称</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name='username' placeholder="用户名"/>
                                        <input type="hidden" name='id' value="{{ $id }}"/>
                                        <input type="hidden" name="is_search" value="0"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="created_date" class="col-sm-2 control-label">时间范围</label>
                                    <div class="col-sm-10">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control form_datetime" name="start_date" id='start_date' value="{{ $start_date }}" placeholder="开始时间" autocomplete="off">
                                            <span class="input-group-addon">~</span>
                                            <input type="text" class="form-control form_datetime" name="end_date" id='end_date' value="{{ $end_date }}" placeholder="结束时间" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">排序方式</label>
                                    <div class="col-sm-9">
                                        <select name="order" class="form-control">
                                            <option value="0" selected="selected">投注总额排序</option>
                                            <option value="1">盈亏降序</option>
                                            <option value="2">盈亏升序</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">彩种</label>
                                    <div class="col-sm-9">
                                        <select name="lottery_id" class="form-control lottery_id">
                                            <option value=''>所有彩种</option>
                                            @foreach ($lottery_list as $k=>$lotteries)
                                                <optgroup label="{{$k}}">
                                                    @foreach($lotteries as $lottery)
                                                        <option value='{{ $lottery->id }}' ident='{{ $lottery->ident }}'>{{ $lottery->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_group_id" class="col-sm-2 control-label">用户组别</label>
                                    <div class="col-sm-10">
                                        <select name="user_group_id" class="form-control">
                                            <option value="0">所有组别</option>
                                            @foreach($user_group as $item)
                                                <option value="{{ $item->id }}" @if($item->id==1) selected @endif>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">显示零结算用户</label>
                                    <div class="col-sm-9">
                                        <select name="show_zero" class="form-control">
                                            <option value="1">是</option>
                                            <option value="0" selected="selected">否</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if($report_lottery_bonus_team_enable == 1)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="select_team_type" class="col-sm-3 control-label text-right">显示数据</label>
                                        <div class="col-sm-9">
                                            <select id="select_team_type" name="team_type" class="form-control">
                                                <option value="0" selected="selected">显示团队</option>
                                                <option value="1">显示直属下级</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="btn-group col-md-6">
                            <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search"><i class="fa fa-search" aria-hidden="true"></i> 查询</button>
                        </div>
                        <div class=" btn-group col-md-6">
                            <button type="reset" class="btn btn-default col-sm-2"><i class="fa fa-refresh" aria-hidden="true"></i> 重置</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" data-sortable="false">用户组</th>
                            <th class="hidden-sm" data-sortable="false">销售总额</th>
                            <th class="hidden-sm" data-sortable="false">返点</th>
                            <th class="hidden-sm" data-sortable="false">实际销售总额</th>
                            <th class="hidden-sm" data-sortable="false">中奖总额</th>
                            <th class="hidden-sm" data-sortable="false">充值总额</th>
                            <th class="hidden-sm" data-sortable="false">提现总额</th>
                            <th class="hidden-sm" data-sortable="false">充值手续费</th>
                            <th class="hidden-sm" data-sortable="false">提现手续费</th>
                            <th class="hidden-sm" data-sortable="false">活动费用</th>
                            <th class="hidden-sm" data-sortable="false">日工资</th>
                            <th class="hidden-sm" data-sortable="false">最终盈亏</th>
                            @if(get_config('dividend_to_report', 0))
                                <th class="hidden-sm" data-sortable="false">系统分红</th>
                                <th class="hidden-sm" data-sortable="false">经营扣款</th>
                                <th class="hidden-sm" data-sortable="false">运营盈亏</th>
                            @endif
                            @if(get_config('dividend_last_amount_to_report', 0))
                                <th class="hidden-sm" data-sortable="false">前期分红</th>
                                <th class="hidden-sm" data-sortable="false">前营扣款</th>
                                <th class="hidden-sm" data-sortable="false">前营盈亏</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2" class="text-right"><b>总计：</b></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            @if(get_config('dividend_to_report', 0))
                                <th></th>
                                <th></th>
                                <th></th>
                            @endif
                            @if(get_config('dividend_last_amount_to_report', 0))
                                <th></th>
                                <th></th>
                                <th></th>
                            @endif
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @include('lottery-bonus.manual')
        </div>
    </div>
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
        };
        laydate(layConfig);

        layConfig.elem = '#end_date';
        laydate(layConfig);

        function set(id) {
            $("input[name='id']").val(id);
            $("input[name='is_search']").val(0);
            $("#tags-table").DataTable().ajax.reload();
        }

        var dividend_to_report = {{ $dividend_to_report }};
        var dividend_last_amount_to_report = {{ $dividend_last_amount_to_report }};

        $(function () {
            var get_params = function (data) {
                var param = {
                    'id': $("input[name='id']").val(),
                    'username': $("input[name='username']").val(),
                    'lottery_id': $("select[name='lottery_id']").val(),
                    'method_id': $("select[name='method_id']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'is_search': $("input[name='is_search']").val(),
                    'order': $("select[name='order']").val(),
                    'show_zero': $("select[name='show_zero']").val(),
                    @if($report_lottery_bonus_team_enable == 1)
                    'team_type': $("select[name='team_type']").val(),
                    @endif
                };
                return $.extend({}, data, param);
            };

            var json_cb = function (json) {
                if (typeof (json.parent_tree) !== undefined) {
                    showParentTree(json.parent_tree);
                } else {
                    showParentTree(null);
                }
            };

            var colums = [
                {"data": "username"},
                {"data": "user_group_name"},
                {"data": "total_price"},
                {"data": "total_rebate"},
                {"data": "total_real_price"},
                {"data": "total_bonus"},
                {"data": "total_deposit"},
                {"data": "total_withdrawal"},
                {"data": "total_deposit_fee"},
                {"data": "total_withdrawal_fee"},
                {"data": "total_activity"},
                {"data": "total_wage"},
                {"data": "total_profit"}
            ];
            var columnDefs = [
                {
                    'targets': 0,
                    "render": function (data, type, row) {
                        if (typeof row.self !== 'undefined' && row.self === 1) {
                            return row.username;
                        } else {
                            return '<span onclick="set(' + row.user_id + ')" style="color: #3c8dbc; cursor: pointer">' + row.username + '</span>';
                        }
                    }
                },
                {
                    'targets': 1,
                    'render': function (data, type, row) {
                        var label = 'label-success';

                        if (row.user_group_id == 2) {
                            label = 'label-warning';
                        } else if (row.user_group_id == 3) {
                            label = 'label-danger';
                        }

                        return app.getLabelHtml(data, label);
                    }
                },
                {
                    'targets': 2,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 3,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 4,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 5,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 6,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 7,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 8,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 9,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 10,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 11,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                    }
                },
                {
                    'targets': 12,
                    'render': function (data, type, row) {
                        var label = 'text-success';

                        if (row.total_profit == 0) {
                            label = '';
                        }

                        if (row.total_profit < 0) {
                            label = 'text-danger';
                        }

                        return app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data), label, true);
                    }
                }
            ];
            if (dividend_to_report) {
                colums.push({"data": "total_dividend"});
                colums.push({"data": "total_xtjykk"});
                colums.push({"data": "total_profit_dividend"});
                columnDefs.push({
                    'targets': 13,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(row.total_dividend);
                    }
                });
                columnDefs.push({
                    'targets': 14,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(parseFloat(row.total_xtjykk));
                    }
                });
                columnDefs.push({
                    'targets': 15,
                    'render': function (data, type, row) {
                        var label = 'text-success';
                        var diff = parseFloat(row.total_profit) - parseFloat(row.total_dividend) + parseFloat(row.total_xtjykk);
                        if (diff == 0) {
                            label = '';
                        }

                        if (diff < 0) {
                            label = 'text-danger';
                        }

                        return app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(diff), label, true);
                    }
                });
            }
            if (dividend_last_amount_to_report) {
                colums.push({"data": "last_total_dividend"});
                colums.push({"data": "last_total_xtjykk"});
                colums.push({"data": "last_total_profit_dividend"});
                columnDefs.push({
                    'targets': 16,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(row.last_total_dividend);
                    }
                });
                columnDefs.push({
                    'targets': 17,
                    'render': function (data, type, row) {
                        return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(row.last_total_xtjykk);
                    }
                });
                columnDefs.push({
                    'targets': 18,
                    'render': function (data, type, row) {
                        var label = 'text-success';
                        var diff = parseFloat(row.total_profit) - parseFloat(row.last_total_dividend) + parseFloat(row.last_total_xtjykk);
                        if (diff == 0) {
                            label = '';
                        }

                        if (diff < 0) {
                            label = 'text-danger';
                        }

                        return app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(diff), label, true);
                    }
                });
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [],
                serverSide: true,
                dom: "<'row'<'col-sm-12'tr>>",
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params, json_cb),
                "columns": colums,
                columnDefs: columnDefs,
                "footerCallback": function (tfoot, data, start, end, display) {
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[\$,]/g, ''))
                            : typeof i === 'number' ? i : 0;
                    };

                    var sum_total_price = 0;
                    var sum_total_rebate = 0;
                    var sum_total_real_price = 0;
                    var sum_total_bonus = 0;
                    var sum_total_deposit = 0;
                    var sum_total_withdrawal = 0;
                    var sum_total_deposit_fee = 0;
                    var sum_total_withdrawal_fee = 0;
                    var sum_total_activity = 0;
                    var sum_total_wage = 0;
                    var sum_total_profit = 0;
                    var sum_total_dividend = 0;
                    var sum_total_xtjykk = 0;
                    var sum_total_profit_after_d = 0;
                    var sum_last_total_dividend = 0;
                    var sum_last_total_xtjykk = 0;
                    var sum_last_total_profit_after_last = 0;

                    for (item in data) {
                        sum_total_price += intVal(data[item].total_price);
                        sum_total_rebate += intVal(data[item].total_rebate);
                        sum_total_real_price += intVal(data[item].total_real_price);
                        sum_total_bonus += intVal(data[item].total_bonus);
                        sum_total_deposit += intVal(data[item].total_deposit);
                        sum_total_withdrawal += intVal(data[item].total_withdrawal);
                        sum_total_deposit_fee += intVal(data[item].total_deposit_fee);
                        sum_total_withdrawal_fee += intVal(data[item].total_withdrawal_fee);
                        sum_total_activity += intVal(data[item].total_activity);
                        sum_total_wage += intVal(data[item].total_wage);
                        sum_total_profit += intVal(data[item].total_profit);
                        if (dividend_to_report) {
                            sum_total_dividend += intVal(data[item].total_dividend);
                            sum_total_xtjykk += intVal(data[item].total_xtjykk);
                        }
                        if (dividend_last_amount_to_report) {
                            sum_last_total_dividend += intVal(data[item].last_total_dividend);
                            sum_last_total_xtjykk += intVal(data[item].last_total_xtjykk);
                        }
                    }
                    sum_total_profit_after_d = sum_total_profit - sum_total_dividend + sum_total_xtjykk;
                    sum_last_total_profit_after_last = sum_total_profit + sum_last_total_xtjykk - sum_last_total_dividend

                    $(tfoot).find('th').eq(1).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_price)
                    );
                    $(tfoot).find('th').eq(2).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_rebate)
                    );
                    $(tfoot).find('th').eq(3).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_real_price)
                    );
                    $(tfoot).find('th').eq(4).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_bonus)
                    );
                    $(tfoot).find('th').eq(5).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_deposit)
                    );
                    $(tfoot).find('th').eq(6).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_withdrawal)
                    );
                    $(tfoot).find('th').eq(7).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_deposit_fee)
                    );
                    $(tfoot).find('th').eq(8).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_withdrawal_fee)
                    );
                    $(tfoot).find('th').eq(9).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_activity)
                    );
                    $(tfoot).find('th').eq(10).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_wage)
                    );
                    $(tfoot).find('th').eq(11).html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_profit), sum_total_profit > 0 ? 'text-success' : 'text-danger')
                    );
                    if (dividend_to_report) {
                        $(tfoot).find('th').eq(12).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_dividend))
                        );
                        $(tfoot).find('th').eq(13).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_xtjykk)
                        );
                        $(tfoot).find('th').eq(14).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_total_profit_after_d), sum_total_profit_after_d > 0 ? 'text-success' : 'text-danger')
                        );
                    }
                    if (dividend_last_amount_to_report) {
                        $(tfoot).find('th').eq(15).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_last_total_dividend))
                        );
                        $(tfoot).find('th').eq(16).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_last_total_xtjykk))
                        );
                        $(tfoot).find('th').eq(17).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_last_total_profit_after_last), sum_last_total_profit_after_last > 0 ? 'text-success' : 'text-danger')
                        );
                    }
                }
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                var data = table.row(0).data();
                if (typeof data !== 'undefined' && typeof data.self !== 'undefined') {
                    $(table.row(0).node()).css('background-color', '#ffece6');
                }
                loadFadeOut();
            });

            $('#search').submit(function (event) {
                event.preventDefault();
                $("input[name='id']").val(0);
                $("input[name='is_search']").val(1);
                table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            function showParentTree(data) {
                var str = '';
                if (data) {
                    for (var i = 0; i < data.length; i++) {
                        str += '<li><span onclick="set(' + data[i].user_id + ')" class="X-Small btn-xs text-primary" style="cursor: pointer;">' + data[i].username + '</span></li>';
                    }
                }
                $breadcrumb = $(".breadcrumb");
                $first = $breadcrumb.children().first();
                $breadcrumb.children().remove();
                $breadcrumb.append($first);
                $breadcrumb.append(str);
            }
        });
    </script>
@stop
