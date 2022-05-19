@extends('layouts.base')

@section('title','个人盈亏报表')
@section('function','个人盈亏报表')
@section('function_link', '/userprofit/')
@section('here','个人盈亏报表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/userreport/" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="会员名"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">盈亏</label>
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
                                <label for="calculate_total" class="col-sm-3 control-label">全部总计</label>
                                <div class="col-sm-9">
                                    <select name="calculate_total" class="form-control">
                                        <option value="1" checked="checked">否</option>
                                        <option value="2">是</option>
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
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">显示零结算日期</label>
                                <div class="col-sm-9">
                                    <select name="show_all" class="form-control">
                                        <option value="1" >是</option>
                                        <option value="0" selected="selected">否</option>
                                    </select>
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
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn">
                            <i class="fa fa-search" aria-hidden="true"></i>查询
                        </button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                        <!--
                        <button type="submit" class="btn btn-warning margin" id="export_btn">
                            <i class="fa fa-download" aria-hidden="true"></i>导出
                        </button>
                        -->
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
                                <th class="hidden-sm" data-sortable="false">日期</th>
                                <th class="hidden-sm" data-sortable="false">盈亏</th>
                                <th width="100" class="hidden-sm" data-sortable="false">提存差</th>
                                <th class="hidden-sm" data-sortable="false">充值</th>
                                <th class="hidden-sm" data-sortable="false">提款</th>
                                <th class="hidden-sm" data-sortable="false">投注</th>
                                <th class="hidden-sm" data-sortable="false">派奖</th>
                                <th width="130" class="hidden-sm" data-sortable="false">返点</th>
                                <th class="hidden-sm" data-sortable="false">手续费(存/取)</th>
                                <th class="hidden-sm" data-sortable="false">活动奖励</th>
                                <th width="130" class="hidden-sm" data-sortable="false">日工资</th>
                                <!--<th class="hidden-sm" style="min-width: 120px" data-sortable="false">操作</th>-->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right"><b>本页总计： </b></th>
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
                                <th colspan="2" class="text-right"><b>全部总计： </b></th>
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
                    'start_time': $("input[name='start_time']").val(),
                    'end_time': $("input[name='end_time']").val(),
                    'profit_min': $("input[name='profit_min']").val(),
                    'profit_max': $("input[name='profit_max']").val(),
                    'deposit_min': $("input[name='deposit_min']").val(),
                    'deposit_max': $("input[name='deposit_max']").val(),
                    'rebate_min': $("input[name='rebate_min']").val(),
                    'rebate_max': $("input[name='rebate_max']").val(),
                    'show_all': $("select[name='show_all']").val(),
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
                    {"data": "created_at"},
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
                    //{"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': 2,
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
                        'targets': 3,
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
                    var rebeta  = 0;
                    var deposit_fee    = 0;
                    var withdrawal_fee = 0;
                    var activity    = 0;
                    var wage        = 0;

                    for(var x in data){
                        var row = data[x];
                        profit         += parseFloat(row['total_price'])-parseFloat(row['total_bonus'])-parseFloat(row['total_rebate'])-parseFloat(row['total_wage'])+parseFloat(row['total_deposit_fee'])+parseFloat(row['total_withdrawal_fee'])-parseFloat(row['total_activity']);
                        balance        += parseFloat(row['total_deposit'] - row['total_withdrawal']);
                        deposit        += parseFloat(row['total_deposit']);
                        withdrawal     += parseFloat(row['total_withdrawal']);
                        bet            += parseFloat(row['total_price']);
                        award          += parseFloat(row['total_bonus']);
                        rebeta         += parseFloat(row['total_rebate']);
                        deposit_fee    += parseFloat(row['total_deposit_fee']);
                        withdrawal_fee += parseFloat(row['total_withdrawal_fee']);
                        activity       += parseFloat(row['total_activity']);
                        wage           += parseFloat(row['total_wage']);
                    }
                    var profit_label = 'text-red';
                    if( profit >= 0 ) {
                        profit_label = 'text-green';
                    }
                    profit = (profit >= 0?'+':'')+(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(profit));
                    $(tfoot).find('th').eq(1).html(
                        app.getColorHtml(profit, profit_label, true)
                    );
                    var balance_label = 'text-red';
                    if( balance >= 0 ) {
                        balance_label = 'text-green';
                    }
                    balance = (balance >= 0?'+':'')+(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(balance));
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
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(rebeta), 'text-red', true)
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
                if (json !== undefined && json !== null && typeof json['total'] == 'object') {
                    var profit = parseFloat(json['total']['total_profit']);
                    var profit_label = 'text-red';
                    if( profit>=0 ) {
                        profit = '+'+ new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_profit']);
                        profit_label = 'text-green';
                    }
                    $("#total_sum").find('th').eq(1).html(
                        app.getColorHtml(profit, profit_label, true)
                    );
                    var amount = json['total']['total_deposit'] - json['total']['total_withdrawal'];
                    amount = parseFloat(amount);
                    var amount_label = 'text-red';
                    if( amount>=0 ) {
                        amount = '+'+ new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(amount);
                        amount_label = 'text-green';
                    }
                    $("#total_sum").find('th').eq(2).html(
                        app.getColorHtml(amount, amount_label, true)
                    );
                    $("#total_sum").find('th').eq(3).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_deposit']), 'text-green', true)
                    );
                    $("#total_sum").find('th').eq(4).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_withdrawal']), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(5).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_price']), 'text-green', true)
                    );
                    $("#total_sum").find('th').eq(6).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_bonus']), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(7).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_rebate']), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(8).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_deposit_fee']), 'text-red', true)+'/'+
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_withdrawal_fee']), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(9).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_activity']), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(10).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['total']['total_wage']), 'text-red', true)
                    );

                }
            });

            $('#search_btn').click(function () {
                event.preventDefault();
                table.ajax.reload();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            //计算合计
            $("select[name='calculate_total']").change(function () {
                if ($(this).val() == 1) {
                    $('#total_sum').hide();
                } else {
                    $('#total_sum').show();
                }
            });

        });
    </script>
@stop
