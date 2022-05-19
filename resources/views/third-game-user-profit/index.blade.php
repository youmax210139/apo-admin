@extends('layouts.base')
@section('title','个人三方日报表')
@section('function','个人三方日报表')
@section('function_link', '/thirdgameuserprofit/')
@section('here','个人三方日报表')
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
                                <label class="col-sm-3 control-label">日期</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" id="start_date" value="{{ $start_date }}" autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" id="end_date" value="{{ $end_date }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">个人销量</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="amount_min" placeholder="最小金额" autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="amount_max" placeholder="最大金额" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">三方游戏</label>
                                <div class="col-sm-9">
                                    <select name="platform_id" id="platform_id" class="form-control">
                                        <option value="0">全部</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform['id'] }}">{{ $platform['name'] }}
                                                [ {{ $platform['ident'] }} ]
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" autocomplete="off"/>
                                </div>
                                <label class="control-label checkbox-inline col-sm-3" style="padding-top: 7px;">
                                    <input type="checkbox" name="include_all" value="1"/>
                                    包含所有下级
                                </label>
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
            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm">日期</th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" data-sortable="false">三方游戏</th>
                            <th class="hidden-sm" data-sortable="false">个人销量</th>
                            <th class="hidden-sm" data-sortable="false">个人派奖</th>
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
                        <tr>
                            <th colspan="3" class="text-right"><b>本页总计：</b></th>
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
                    'username': $("input[name='username']").val(),
                    'include_all': $("input[name='include_all']:checked").val(),
                    'amount_min': $('input[name="amount_min"]').val(),
                    'amount_max': $('input[name="amount_max"]').val(),
                };
                return $.extend({}, data, param);
            }

            function colorRender(data, value) {
                var color = data >= 0 ? '#12ca12' : 'red';
                return "<span style='color:" + color + ";'>" + value + "</span>";
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
                    {"data": "username"},
                    {"data": "name"},
                    {"data": "bet"},
                    {"data": "user_win"},
                    {"data": "admin_deduct"},
                    {"data": "win"},
                    {"data": "fd"},
                    {"data": "ds"},
                    {"data": "chou_shui"},
                    {"data": "real_win"}
                ],
                columnDefs: [
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
                            return colorRender(data, new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data));
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
                            return colorRender(data, new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data));
                        }
                    }
                ],
                "footerCallback": function (tfoot, data, start, end, display) {
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[\$,]/g, ''))
                            : typeof i === 'number' ? i : 0;
                    };

                    var sum_bet = 0;
                    var sum_user_win = 0;
                    var sum_admin_deduct = 0;
                    var sum_win = 0;
                    var sum_fd = 0;
                    var sum_ds = 0;
                    var sum_chou_shui = 0;
                    var sum_real_win = 0;

                    for (item in data) {
                        sum_bet += intVal(data[item].bet);
                        sum_user_win += intVal(data[item].user_win);
                        sum_admin_deduct += intVal(data[item].admin_deduct);
                        sum_win += intVal(data[item].win);
                        sum_fd += intVal(data[item].fd);
                        sum_ds += intVal(data[item].ds);
                        sum_chou_shui += intVal(data[item].chou_shui);
                        sum_real_win += intVal(data[item].real_win);
                    }

                    $(tfoot).find('th').eq(1).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_bet)
                    );
                    $(tfoot).find('th').eq(2).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_user_win)
                    );
                    $(tfoot).find('th').eq(3).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_admin_deduct)
                    );
                    $(tfoot).find('th').eq(4).html(
                        colorRender(sum_win, new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_win))
                    );
                    $(tfoot).find('th').eq(5).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_fd)
                    );
                    $(tfoot).find('th').eq(6).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_ds)
                    );
                    $(tfoot).find('th').eq(7).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_chou_shui)
                    );
                    $(tfoot).find('th').eq(8).html(
                        colorRender(sum_real_win, new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_real_win))
                    );
                }
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
