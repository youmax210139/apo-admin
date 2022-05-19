@extends('layouts.base')
@section('title','三方每日报表')
@section('function','三方每日报表')
@section('function_link', '/thirdgamedaily/')
@section('here','三方每日报表')
@section('content')
    <div class="row">
        <div class="col-md-12">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" id="start_date" value="{{ $start_date }}" autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" id="end_date" value="{{ $end_date }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">三方游戏</label>
                                <div class="col-sm-9">
                                    <select name="platform_id" id="platform_id" class="form-control">
                                        <option value="-1">全部</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform['id'] }}">{{ $platform['name'] }}
                                                [ {{ $platform['ident'] }} ]
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fa fa-info"></i>注意：因第三方提供数据会有延迟，本报表仅供参考，请以第三方后台数据为准。
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">日期</th>
                            <th class="hidden-sm" data-sortable="false">游戏</th>
                            <th class="hidden-sm" data-sortable="false">总投注</th>
                            <th class="hidden-sm" data-sortable="false">中奖总额</th>
                            <th class="hidden-sm" data-sortable="false">管理员扣减</th>
                            <th class="hidden-sm" data-sortable="false">平台盈亏</th>
                            <th class="hidden-sm" data-sortable="false">返水</th>
                            <th class="hidden-sm" data-sortable="false">打赏</th>
                            <th class="hidden-sm" data-sortable="false">抽水</th>
                            <th class="hidden-sm" data-sortable="false">最终结算</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr id="total_sum">
                            <th colspan="2" class="text-right"><b>总计：</b></th>
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
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD',
            istime: false,
        };
        laydate(layConfig);
        layConfig.elem = '#end_date';
        laydate(layConfig);
        $(function () {
            var get_params = function (data) {
                var param = {
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'platform_id': $("select[name='platform_id']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "date"},
                    {"data": "name"},
                    {"data": "total_bets"},
                    {"data": "total_wins"},
                    {"data": "admin_deduct"},
                    {"data": "platform_wins"},
                    {"data": "fd"},
                    {"data": "ds"},
                    {"data": "chou_shui"},
                    {"data": "real_win"}
                ],
                columnDefs: [
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
                ]
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
                if (json !== undefined && json !== null) {
                    if (typeof json['sum_amount'] == 'object') {
                        $("#total_sum").find('th').eq(1).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['total_bets'])
                        );
                        $("#total_sum").find('th').eq(2).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['total_wins'])
                        );
                        $("#total_sum").find('th').eq(3).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['admin_deduct'])
                        );
                        $("#total_sum").find('th').eq(4).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['platform_wins']), 'text-red', true)
                        );
                        $("#total_sum").find('th').eq(5).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['fd'])
                        );
                        $("#total_sum").find('th').eq(6).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['ds'])
                        );
                        $("#total_sum").find('th').eq(7).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['chou_shui'])
                        );
                        $("#total_sum").find('th').eq(8).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['real_win']), 'text-red', true)
                        );
                    } else {
                        $("#total_sum").find('th').eq(1).html('');
                        $("#total_sum").find('th').eq(2).html('');
                        $("#total_sum").find('th').eq(3).html('');
                        $("#total_sum").find('th').eq(4).html('');
                        $("#total_sum").find('th').eq(5).html('');
                        $("#total_sum").find('th').eq(6).html('');
                        $("#total_sum").find('th').eq(7).html('');
                        $("#total_sum").find('th').eq(8).html('');
                    }
                }
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
