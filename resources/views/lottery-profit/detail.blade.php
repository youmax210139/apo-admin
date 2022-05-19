@extends('layouts.base')

@section('title','游戏明细')
@section('function','彩票盈亏报表')
@section('function_link', '/lotteryprofit/')
@section('here','游戏明细')


@section('content')
<div class="row">
<div class="col-sm-12">
     @include('partials.errors')
     @include('partials.success')
    <!--搜索框 Start-->
    <div class="box box-primary">
        <form class="form-horizontal" id="search">
            <div class="box-body">
                <div class="row" style="padding-top: 10px;">
                    <input type="hidden" class="form-control" name="id"  value="{{ $id }}">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="created_date" class="col-sm-3 control-label">时间范围</label>
                            <div class="col-sm-9">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="start_date" id='start_date' value="{{ $start_date }}" placeholder="开始时间">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="end_date" id='end_date' value="{{ $end_date }}" placeholder="结束时间">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="btn-group col-sm-6">
                            <button type="submit" class="btn btn-primary col-sm-12" id="search"><i class="fa fa-search" aria-hidden="true"></i> 查询</button>
                        </div>
                        <div class=" btn-group col-sm-6">
                            <button type="reset" class="btn btn-default col-sm-12" ><i class="fa fa-refresh" aria-hidden="true"></i> 重置</button>
                        </div>
                    </div>

                </div>
            </div>
	   </form>
    </div>

    <div class="box box-primary">
        <div class="box-body">
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                    <tr>
                        <th class="hidden-sm" data-sortable="false">游戏</th>
                        <th class="hidden-sm" data-sortable="false">玩法</th>
                        <th class="hidden-sm" data-sortable="false">销售额</th>
                        <th class="hidden-sm" data-sortable="false">返点</th>
                        <th class="hidden-sm" data-sortable="false">实际销售额</th>
                        <th class="hidden-sm" data-sortable="false">中奖额</th>
                        <th class="hidden-sm" data-sortable="false">盈亏</th>
                        <th class="hidden-sm" data-sortable="false"></th>
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
<script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig ={
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
        };
        laydate(layConfig);

        layConfig.elem = '#end_date';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'id'         : $("input[name='id']").val(),
                    'start_date' : $("input[name='start_date']").val(),
                    'end_date'   : $("input[name='end_date']").val(),
                };
                return $.extend({}, data, param);
            };

            var group_total = null;
            var json_cb = function(json) {
                if(typeof(json.group_total) !== undefined) {
                    group_total = json.group_total;
                }
            };
            var table = $("#tags-table").DataTable({
                language:app.DataTable.language(),
                ordering:  false,
                serverSide: true,
                paging: false,
                searching:false,
                info:false,

                ajax: app.DataTable.ajax(null, null, get_params, json_cb),
                "columns": [
                    {"data": "lottery_name"},
                    {"data": "method_name"},
                    {"data": "total_price"},
                    {"data": "total_rebate"},
                    {"data": "total_real_price"},
                    {"data": "total_bonus"},
                    {"data": "total_profit"},
                    {"data": "lottery_id"},
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        'visible': false
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            return money_format(data);
                        }
                    },
                    {
                        'targets': 3,
                        'render': function (data, type, row) {
                            return money_format(data);
                        }
                    },
                    {
                        'targets': 4,
                        'render': function (data, type, row) {
                            return money_format(data);
                        }
                    },
                    {
                        'targets': 5,
                        'render': function (data, type, row) {
                            return money_format(data);
                        }
                    },
                    {
                        'targets': 6,
                        'render': function (data, type, row) {
                            var label = 'text-success';

                            if (row.total_profit < 0 ) {
                                label = 'text-danger';
                            }

                            return app.getColorHtml(money_format(data), label, true);
                        }
                    }
                ],
                "drawCallback": function(settings) {
                    var api = this.api();
                    var rows = api.rows().nodes();

                    var lottery_id_colums = api.column(-1).data();
                    for (var row=0; row<lottery_id_colums.length; row++)
                    {
                        if(lottery_id_colums[row] !== lottery_id_colums[row+1]){
                            if (group_total === null) {
                                return;
                            }
                            var group_total_price = money_format(group_total[lottery_id_colums[row]].total_price);
                            var group_total_rebate = money_format(group_total[lottery_id_colums[row]].total_rebate);
                            var group_total_real_price = money_format(group_total[lottery_id_colums[row]].total_real_price);
                            var group_total_bonus = money_format(group_total[lottery_id_colums[row]].total_bonus);
                            var group_total_profit = money_format(group_total[lottery_id_colums[row]].total_profit);

                            $(rows).eq(row).after(
                                '<tr style="background-color: #ffece6;">' +
                                '<td colspan="2" class="text-right text-success">小结：</td>' +
                                '<td>'+group_total_price+'</td>' +
                                '<td>'+group_total_rebate+'</td>' +
                                '<td>'+group_total_real_price+'</td>' +
                                '<td>'+group_total_bonus+'</td>' +
                                '<td>'+app.getColorHtml(group_total_profit, parseFloat(group_total_profit) < 0 ? 'text-danger' : 'text-success', true)+'</td>' +
                                '</tr>'
                            );
                        }
                    }
                },
                "footerCallback": function ( tfoot, data, start, end, display ){
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[\$,]/g, ''))
                            : typeof i === 'number' ? i : 0;
                    };

                    var sum_total_price = 0;
                    var sum_total_bonus = 0;
                    var sum_total_real_price = 0;
                    var sum_total_rebate = 0;
                    var sum_total_profit = 0;

                    for (item in data) {
                        sum_total_price += intVal(data[item].total_price);
                        sum_total_bonus += intVal(data[item].total_bonus);
                        sum_total_real_price += intVal(data[item].total_real_price);
                        sum_total_rebate += intVal(data[item].total_rebate);
                        sum_total_profit += intVal(data[item].total_profit);
                    }

                    $(tfoot).find('th').eq(1).html(
                        money_format(sum_total_price)
                    );
                    $(tfoot).find('th').eq(2).html(
                        money_format(sum_total_rebate)
                    );
                    $(tfoot).find('th').eq(3).html(
                        money_format(sum_total_real_price)
                    );
                    $(tfoot).find('th').eq(4).html(
                        money_format(sum_total_bonus)
                    );
                    $(tfoot).find('th').eq(5).html(
                        app.getColorHtml(money_format(sum_total_profit), sum_total_profit > 0 ? 'text-success' : 'text-danger')
                    );
                }
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            $('#search').submit(function(event){
            	event.preventDefault();
            	table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function(){
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            var money_format = function (money) {
                return new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(money);
            }

        });
    </script>
@stop
