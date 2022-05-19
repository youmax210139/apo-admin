@extends('layouts.base')
@section('title','个人盈亏排行')
@section('function','个人盈亏排行')
@section('function_link', '/userreport/')
@section('here','个人盈亏排行')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/userreport/" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="export" value="1"/>
                    <div class="box-header with-border">
                        <h3 class="box-title"><!--搜索查询区--></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">会员</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name='username' placeholder="会员名"/>
                                </div>
                                <label class="control-label checkbox-inline col-sm-4" style="padding-top: 7px;">
                                    <input type="checkbox" name="include_all" value="1" />
                                    包含所有下级
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">最终盈亏</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="profit_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="profit_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">充值从</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="deposit_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="deposit_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">日工资从</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="wage_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="wage_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="agent_level_id" class="col-sm-3 control-label">代理级别</label>
                                <div class="col-sm-9">
                                    <select name="agent_level" class="form-control">
                                        <option value="all">不限</option>
                                        <option value="root">总代</option>
                                        @for($i=1;$i<$agent_level;$i++)
                                            <option value="{{ $i }}">{{$i}}级代理 </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="start_time" id="start_time"
                                               value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control " name="end_time" id="end_time"
                                               value="{{$end_date}}" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">投注从</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="bet_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="bet_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">提现从</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="withdrawal_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="withdrawal_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">显示零结算用户</label>
                                <div class="col-sm-9">
                                    <select name="show_zero" class="form-control">
                                        <option value="1" >是</option>
                                        <option value="0" selected="selected">否</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">余额从</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="balance_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="balance_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">返点从</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="rebate_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="rebate_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">活动奖励从</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="activity_min"
                                               value="" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="activity_max"
                                               value="" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-3 control-label">用户组别</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="all">所有组别</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}" @if($item->id==1) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn">
                            <i class="fa fa-search" aria-hidden="true"></i>查询
                        </button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                        <button type="button" onclick="$('#search').submit()" class="btn btn-warning margin" id="export">
                            <i class="fa fa-download" aria-hidden="true"></i>导出
                        </button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->
            <div class="box box-primary">
                <style>
                    table.dataTable.table-condensed tr > th {
                        text-align: center;
                        vertical-align: middle;
                    }

                    table.dataTable.table-condensed tbody > tr > td {
                        text-align: center;
                        vertical-align: middle;
                    }
                </style>
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false"></th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" data-sortable="false">用户组</th>
                            <th class="hidden-sm" data-sortable="false">奖金组</th>
                            <th class="hidden-sm" >余额</th>
                            <th class="hidden-sm" >最终盈亏</th>
                            <th width="100" class="hidden-sm" data-sortable="false">提存差</th>
                            <th class="hidden-sm" >充值</th>
                            <th class="hidden-sm" >提款</th>
                            <th class="hidden-sm" >投注</th>
                            <th class="hidden-sm" >派奖</th>
                            <th width="130" class="hidden-sm" >返点</th>
                            <th class="hidden-sm" data-sortable="false">手续费(存/取)</th>
                            <th class="hidden-sm" >活动奖励</th>
                            <th width="130" class="hidden-sm" >日工资</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5" class="text-right"><b>本页总计： </b></th>
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
                        </tr>
                        <tr id="total_sum" style="display: none;">
                            <th colspan="5" class="text-right"><b>全部总计： </b></th>
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
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_time',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);
        layConfig.elem = '#end_time';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'include_all': $("input[name='include_all']:checked").val(),
                    'start_time': $("input[name='start_time']").val(),
                    'end_time': $("input[name='end_time']").val(),
                    'deposit_min': $("input[name='deposit_min']").val(),
                    'deposit_max': $("input[name='deposit_max']").val(),
                    'wage_min': $("input[name='wage_min']").val(),
                    'wage_max': $("input[name='wage_max']").val(),
                    'bet_min': $("input[name='bet_min']").val(),
                    'bet_max': $("input[name='bet_max']").val(),
                    'withdrawal_min': $("input[name='withdrawal_min']").val(),
                    'withdrawal_max': $("input[name='withdrawal_max']").val(),
                    'balance_min': $("input[name='balance_min']").val(),
                    'balance_max': $("input[name='balance_max']").val(),
                    'rebate_min': $("input[name='rebate_min']").val(),
                    'rebate_max': $("input[name='rebate_max']").val(),
                    'activity_min': $("input[name='activity_min']").val(),
                    'activity_max': $("input[name='activity_max']").val(),
                    'show_zero': $("select[name='show_zero']").val(),
                    'profit_min': $("input[name='profit_min']").val(),
                    'profit_max': $("input[name='profit_max']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'agent_level': $("select[name='agent_level']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[1, "asc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"visible": false},
                    {"data": "username"},
                    {"data": "user_group_name"},
                    {"data": "prize_level"},
                    {"data": "balance"},
                    {"data": "total_profit"},
                    {"data": "total_deposit"},
                    {"data": "total_deposit"},
                    {"data": "total_withdrawal"},
                    {"data": "total_price"},
                    {"data": "total_bonus"},
                    {"data": "total_rebate"},
                    {"data": "total_deposit_fee"},
                    {"data": "total_activity"},
                    {"data": "total_wage"},
                ],
                columnDefs: [
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            if (row['user_observe']) {
                                return app.getColorHtml(row.username, 'label-danger', false);
                            } else {
                                return row.username;
                            }
                        }
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            var label = 'label-success';

                            if (row.user_group_id == 2 ) {
                                label = 'label-warning';
                            } else if (row.user_group_id == 3 ) {
                                label = 'label-danger';
                            }

                            return app.getLabelHtml(data, label);
                        }
                    },
                    {
                        'targets': 5,
                        'render': function (data, type, row) {

                            // 盈亏 = 投注-奖金-返点-日工资-充值手续费-提现手续费-活动奖金
                            $label_css = 'text-danger';
                            if( data > 0 ){
                                $label_css = 'text-success';
                            }

                            return app.getColorHtml(data, $label_css, false);
                        }
                    },
                    {
                        'targets': 6,
                        'render': function (data, type, row) {
                            return row['total_deposit']-row['total_withdrawal'];
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            return row['total_deposit_fee']+'/'+row['total_withdrawal_fee'];
                        }
                    },
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                    var profit  = 0;
                    var balance = 0;
                    var deposit = 0;
                    var withdrawal = 0;
                    var bet     = 0;
                    var award   = 0;
                    var rebate  = 0;
                    var deposit_fee    = 0;
                    var withdrawal_fee = 0;
                    var activity    = 0;
                    var wage        = 0;

                    for(x in data){
                        var row = data[x];
                        profit         += parseFloat(row['total_price'])-parseFloat(row['total_bonus'])-parseFloat(row['total_rebate'])-parseFloat(row['total_wage'])+parseFloat(row['total_deposit_fee'])+parseFloat(row['total_withdrawal_fee'])-parseFloat(row['total_activity']);
                        balance        += parseFloat(row['total_deposit'] - row['total_withdrawal']);
                        deposit        += parseFloat(row['total_deposit']);
                        withdrawal     += parseFloat(row['total_withdrawal']);
                        bet            += parseFloat(row['total_price']);
                        award          += parseFloat(row['total_bonus']);
                        rebate         += parseFloat(row['total_rebate']);
                        deposit_fee    += parseFloat(row['total_deposit_fee']);
                        withdrawal_fee += parseFloat(row['total_withdrawal_fee']);
                        activity       += parseFloat(row['total_activity']);
                        wage           += parseFloat(row['total_wage']);
                    }
                    profit = new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(profit);
                    var profit_label = 'text-red';
                    if( profit >= 0 ) {
                        profit = '+'+profit;
                        profit_label = 'text-green';
                    }
                    $(tfoot).find('th').eq(1).html(
                        app.getColorHtml(profit, profit_label, true)
                    );

                    balance = new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(balance);
                    var balance_label = 'text-red';
                    if( balance >= 0 ) {
                        balance = '+'+balance;
                        balance_label = 'text-green';
                    }
                    $(tfoot).find('th').eq(2).html(
                        app.getColorHtml(balance, balance_label, true)
                    );

                    $(tfoot).find('th').eq(3).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(deposit), 'text-green', true)
                    );
                    $(tfoot).find('th').eq(4).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(withdrawal), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(5).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(bet), 'text-green', true)
                    );
                    $(tfoot).find('th').eq(6).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(award), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(7).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(rebate), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(8).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(deposit_fee), 'text-red', true)+'/'+
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(withdrawal_fee), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(9).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(activity), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(10).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(wage), 'text-red', true)
                    );
                }
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
            });

            $('#search_btn').click(function () {
                event.preventDefault();
                table.ajax.reload();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
        });

    </script>
@stop
