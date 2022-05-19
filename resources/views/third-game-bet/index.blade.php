@extends('layouts.base')

@section('title','投注纪录')
@section('function','投注纪录')
@section('function_link', '/thirdgamebet/')
@section('here','投注纪录')

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
                                <label for="username" class="col-sm-3 col-sm-3 control-label">ID</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='id' placeholder="ID" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">流水号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='bet_id' placeholder="第三方流水号" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="balance" class="col-sm-3 control-label">投注额</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="amount_min" placeholder="最小金额">
                                        <span class="input-group-addon"> ~ </span>
                                        <input type="text" class="form-control form_datetime" name="amount_max" placeholder="最大金额">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="balance" class="col-sm-3 control-label">平台</label>
                                <div class="col-sm-9">
                                    <select name="platform" class="form-control">
                                        <option value="">选择平台</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform['ident'] }}">{{ $platform['name'] }} [ {{ $platform['ident'] }} ]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">投注时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" autocomplete="off" name="start_date" id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" autocomplete="off" name="end_date" id='end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="btn-group col-md-6">
                            <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                        </div>
                        <div class=" btn-group col-md-6">
                            <button type="reset" class="btn btn-default col-sm-2" ></i>重置</button>
                        </div>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->

            <div class="box box-primary">
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm">ID</th>
                            <th class="hidden-sm" data-sortable="false">第三方流水</th>
                            <th class="hidden-sm">投注时间</th>
                            <th class="hidden-sm" data-sortable="false">平台</th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm"  data-sortable="false" >游戏名称</th>
                            <th class="hidden-sm"  data-sortable="false" >游戏分类</th>
                            <th class="hidden-sm">投注额</th>
                            <th class="hidden-sm">盈亏</th>
                            <th class="hidden-sm" data-sortable="false">返点</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr id="total_sum">
                            <th colspan="7" class="text-right"><b>全部总计： </b></th>
                            <th></th>
                            <th colspan="2"></th>
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
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);
        layConfig.elem = '#end_date';
        laydate(layConfig);
        $(function () {
            var get_params = function (data) {
                var param = {
                    'id': $("input[name='id']").val(),
                    'bet_id': $("input[name='bet_id']").val(),
                    'username': $("input[name='username']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'platform': $("select[name='platform']").val(),
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[2, "desc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "bet_id"},
                    {"data": "game_date"},
                    {"data": "platform"},
                    {"data": "username"},
                    {"data": "game"},
                    {"data": "game_type"},
                    {"data": "total_bets"},
                    {"data": "total_wins"},
                    {"data": "rebate_status"}
                ],
                createdRow: function (row, data, index) {
                    if (data['totalbets'] >= 30000) {
                        $(row).addClass('danger');
                    }
                },
                columnDefs: [
                    {
                        'targets': 4,
                        'render': function (data, type, row) {
                            if (row['user_observe']) {
                                return app.getColorHtml(row.username, 'label-danger', false);
                            } else {
                                return row.username;
                            }
                        }
                    },
                    {
                        'targets': -1,
                        'render': function (data, type, row) {
                            var str = '--';
                            if(row.rebate_status == 2){
                                str = '<span class="text-danger">返水错误</span>';
                            }else if(row.rebate_status == 1){
                                str = '<span class="text-success">已返水</span>';
                            }else if(row.rebate_status == 0){
                                str = '<span class="text-warning">未返水</span>';
                            }
                            return str;
                        }
                    }
                ]
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
                if (json !== undefined && json !== null && typeof json['sum'] == 'object') {
                    $("#total_sum").find('th').eq(1).html(json['sum']['total_bets']);
                    $("#total_sum").find('th').eq(2).html(json['sum']['total_wins']);
                }
            });

            table.on('draw.dt', function (e, settings, json, xhr) {
                loadFadeOut();
            });

            $('#search').submit(function (event) {
                event.preventDefault();
                table.ajax.reload();
            });
            $('#refresh').click(function (event) {
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