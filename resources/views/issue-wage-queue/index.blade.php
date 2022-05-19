@extends('layouts.base')

@section('title','奖期工资进度列表')
@section('function','奖期工资进度列表')
@section('function_link', '/issuewagequeue/')
@section('here','奖期工资进度列表')

@section('content')
<div class="row">
    <div class="col-xs-12">
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
                                <label for="created_date" class="col-sm-3 control-label">最后派发时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date"
                                                id='start_date' value="{{ $start_date }}" placeholder="开始时间"
                                                autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date"
                                                id='end_date' value="{{ $end_date }}" placeholder="结束时间"
                                                autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lottery_id" class="col-sm-3 control-label">彩种名称</label>
                                <div class="col-sm-9">
                                    <select name="lottery_id" class="form-control">
                                        <option value="" selected>全部</option>
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
                                <label for="lottery_status" class="col-sm-3 control-label">彩种状态</label>
                                <div class="col-sm-9">
                                    <select name="lottery_status" class="form-control">
                                        <option value="" selected>全部</option>
                                        <option value=true>开售</option>
                                        <option value=false>停售</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="wage_type" class="col-sm-3 control-label">工资类型</label>
                                <div class="col-sm-9">
                                    <select name="wage_type" class="form-control">
                                        <option value="" selected>全部</option>
                                        <option value="7">奖期亏损工资</option>
                                        <!--<option value="8">奖期(销量)工资</option>
                                        <option value="9">奖期挂单工资 </option>-->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="queue_status" class="col-sm-3 control-label">队列状态</label>
                                <div class="col-sm-9">
                                    <select name="queue_status" class="form-control">
                                        <option value="1">已派发</option>
                                        <option value="2">未派发</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="btn-group col-md-6">
                        <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search">
                            <i class="fa fa-search" aria-hidden="true"></i> 查询
                        </button>
                    </div>
                    <div class=" btn-group col-md-6">
                        <button type="reset" class="btn btn-default col-sm-2">
                            <i class="fa fa-refresh" aria-hidden="true"></i> 重置
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-body">
                <table id="tags-table"
                        class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                    <thead>
                    <tr>
                        <th class="hidden-sm" data-sortable="false">工资类型</th>
                        <th class="hidden-sm">彩种名称</th>
                        <th class="hidden-sm">彩种标识</th>
                        <th class="hidden-sm">彩种状态</th>
                        <th class="hidden-sm" data-sortable="false">最新派发奖期</th>
                        <th class="hidden-sm" data-sortable="false">奖期截止时间</th>
                        <th class="hidden-sm" data-sortable="false">首次派发时间</th>
                        <th class="hidden-sm">最新派发时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
        $("#tags-table").DataTable().ajax.reload();
    }

    $(function () {
        var get_params = function (data) {
            var param = {
                'start_date': $("input[name='start_date']").val(),
                'end_date': $("input[name='end_date']").val(),
                'lottery_id': $("select[name='lottery_id']").val(),
                'lottery_status': $("select[name='lottery_status']").val(),
                'wage_type': $("select[name='wage_type']").val(),
                'queue_status': $("select[name='queue_status']").val()
            };
            return $.extend({}, data, param);
        };

        var colums = [
            {"data": "wage_type"},
            {"data": "lottery_name"},
            {"data": "lottery_ident"},
            {"data": "lottery_status"},
            {"data": "lottery_issue"},
            {"data": "issue_sale_end"},
            {"data": "created_at"},
            {"data": "updated_at"}
        ];
        var columnDefs = [
            {
                'targets': 0,
                "render": function (data, type, row) {
                    var str = '';
                    if (row.wage_type == null) {
                        str = '无工资';
                    } else {
                        // 工资类型 1日工资 2实时工资 3小时工资,4浮动工资,5挂单日工资,6实时工资-中挂单, 7奖期亏损工资
                        if  (row.wage_type == 7) {
                            str = '奖期亏损工资';
                        } else {
                            str = '其他工资';
                        }
                    }
                    return str;
                }
            },
            {
                'targets': 3,
                "render": function (data, type, row) {
                    var label = 'label-success';
                    var status = '开售'
                    if (row.lottery_status == false) {
                        label = 'label-danger';
                        status = '停售'
                    }
                    return app.getLabelHtml(status, label);
                }
            },
            {
                'targets': -2,
                "render": function (data, type, row) {
                    if (row.created_at !== null && row.created_at !== undefined && row.created_at !== '' && row.created_at !== 'null') {
                        return row.created_at;
                    } else {
                        return app.getLabelHtml('没有派发', 'label-default');
                    }
                }
            },
            {
                'targets': -1,
                "render": function (data, type, row) {
                    if (row.updated_at !== null && row.updated_at !== undefined && row.updated_at !== '' && row.updated_at !== 'null') {
                        return row.updated_at + "  " + app.getLabelHtml(timestampFormat(Date.parse(row.updated_at)/1000), 'label-default');
                    }

                    function timestampFormat(timestamp) {
                        function zeroize(num) {
                            return (String(num).length == 1 ? '0' : '') + num;
                        }
                    
                        var curTimestamp = parseInt(new Date().getTime() / 1000); //当前时间戳
                        var timestampDiff = curTimestamp - timestamp; // 参数时间戳与当前时间戳相差秒数
                    
                        var curDate = new Date(curTimestamp * 1000); // 当前时间日期对象
                        var tmDate = new Date(timestamp * 1000);  // 参数时间戳转换成的日期对象
                    
                        var Y = tmDate.getFullYear(), m = tmDate.getMonth() + 1, d = tmDate.getDate();
                        var H = tmDate.getHours(), i = tmDate.getMinutes(), s = tmDate.getSeconds();
                    
                        if ( timestampDiff < 60 ) { // 一分钟以内
                            return "刚刚";
                        } else if( timestampDiff < 3600 ) { // 一小时前之内
                            return Math.floor( timestampDiff / 60 ) + "分钟前";
                        } else if ( curDate.getFullYear() == Y && curDate.getMonth()+1 == m && curDate.getDate() == d ) {
                            return '今天' + zeroize(H) + ':' + zeroize(i);
                        } else {
                            var newDate = new Date( (curTimestamp - 86400) * 1000 ); // 参数中的时间戳加一天转换成的日期对象
                            if ( newDate.getFullYear() == Y && newDate.getMonth()+1 == m && newDate.getDate() == d ) {
                                return '昨天' + zeroize(H) + ':' + zeroize(i);
                            } else if ( curDate.getFullYear() == Y ) {
                                return  zeroize(m) + '月' + zeroize(d) + '日 ' + zeroize(H) + ':' + zeroize(i);
                            } else {
                                return  Y + '年' + zeroize(m) + '月' + zeroize(d) + '日 ' + zeroize(H) + ':' + zeroize(i);
                            }
                        }
                    }
                }
            }
        ];
        var table = $("#tags-table").DataTable({
            language: app.DataTable.language(),
            order: [[7, "desc"]],
            serverSide: true,
            pageLength: 25,
            searching: false,
            ajax: app.DataTable.ajax(null, null, get_params),
            "columns": colums,
            columnDefs: columnDefs,
            "footerCallback": function (tfoot, data, start, end, display) {
                var profit = 0;
                var amount = 0;
                for(x in data) {
                    var row = data[x];
                    profit += parseFloat(row['total_profit']);
                    amount += parseFloat(row['amount']);
                }
                $(tfoot).find('th').eq(1).html(
                    app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(profit), 'text-red', true)
                );
                $(tfoot).find('th').eq(3).html(
                    app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(amount), 'text-red', true)
                );
            },
        });

        table.on('preXhr.dt', function () {
            loadShow();
        });
        table.on('draw.dt', function () {
            loadFadeOut();
        });

        $('#search').submit(function (event) {
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