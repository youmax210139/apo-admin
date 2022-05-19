@extends('layouts.base')

@section('title','彩票单期盈亏报表')

@section('function','彩票单期盈亏报表')
@section('function_link', '/Lotterysinglesale/')

@section('here','彩票单期盈亏报表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
        @include('partials.errors')
        @include('partials.success')
        <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" method="post" action="/Lotterysinglesale">
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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">彩种</label>
                                    <div class="col-sm-9">
                                        <select name="lottery_id" class="form-control lottery_id">
                                            <option value="0">所有彩种</option>
                                            @foreach ($lottery_list as $lottery)
                                                <option value='{{ $lottery->id }}' ident='{{ $lottery->ident }}'>{{ $lottery->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="created_date" class="col-sm-2 control-label">时间</label>
                                    <div class="col-sm-10">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control form_datetime" name="start_date" id='start_date' value="{{$start_date}}" placeholder="开始时间" autocomplete="off">
                                            <span class="input-group-addon">~</span>
                                            <input type="text" class="form-control form_datetime" name="end_date" id='end_date' value="" placeholder="结束时间" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">用户组</label>
                                    <div class="col-sm-9">
                                        <select name="user_group_id" class="form-control user_group_id">
                                            <option value="0">所有组</option>
                                            <option value='1' selected>正式组</option>
                                            <option value='2'>测试组</option>
                                            <option value='3'>试玩组</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="btn-group col-md-4">
                            <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                        </div>
                        <div class=" btn-group col-md-4">
                            <button type="reset" class="btn btn-default col-sm-2"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                        </div>
                        <div class="btn-group col-md-4">
                            <button type="button" onclick="$('#search').submit()" class="btn btn-warning col-sm-2 pull-right" id="export"><i class="fa fa-download" aria-hidden="true"></i>导出</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">ID</th>
                            <th class="hidden-sm">日期</th>
                            <th class="hidden-sm" data-sortable="false">游戏</th>
                            <th class="hidden-sm" data-sortable="false">奖期</th>
                            <th class="hidden-sm" data-sortable="false">截止投注时间</th>
                            <th class="hidden-sm" data-sortable="false">开奖时间</th>
                            <th class="hidden-sm">销售总额</th>
                            <th class="hidden-sm">返点总额</th>
                            <th class="hidden-sm">返奖总额</th>
                            <th class="hidden-sm">盈亏值</th>
                            <th class="hidden-sm" data-sortable="false">开奖号码</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="6" class="text-right"><b>总计：</b></th>
                            <th id="total_sell">0</th>
                            <th id="total_rebate">0</th>
                            <th id="total_bonus">0</th>
                            <th id="total_sum">0</th>
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
        var layConfig = {
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
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'lottery_id': $('select[name="lottery_id"]').val(),
                    'user_group_id': $('select[name="user_group_id"]').val()
                };
                return $.extend({}, data, param);
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[3, "desc"]],
                serverSide: true,
                searching: false,
                pageLength: 100,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "date"},
                    {"data": "lottery_name"},
                    {"data": "issue"},
                    {"data": "sale_end"},
                    {"data": "write_time"},
                    {"data": "sum_sell"},
                    {"data": "sum_rebate"},
                    {"data": "sum_bonus"},
                    {"data": "total_sum"},
                    {"data": "code"}
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return '';
                        }
                    },
                    {
                        "targets": 9,
                        "searchable": false,
                        "render": function (data, type, row) {
                            var html = '<span class="' + (data > 0 ? 'text-green' : 'text-red') + '">' + data + '</span>';
                            return html;
                        }
                    }
                ]
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });
            table.on('xhr.dt', function (e, settings, json, xhr) {
                if (typeof json['sum_amount'] == 'object') {
                    $('#total_sell').html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['sell']), 'text-green', true)
                    );
                    $('#total_rebate').html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['rebate']), 'text-red', true)
                    );
                    $('#total_bonus').html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['bonus']), 'text-red', true)
                    );
                    $('#total_sum').html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['total_sum']), json['sum_amount']['total_sum'] < 0 ? 'text-red' : 'text-green', true)
                    );
                }
            });
            table.on('draw.dt', function () {
                table.column(0).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
                loadFadeOut();
            });
            $('#search_btn').click(function (event) {
                event.preventDefault();
                table.ajax.reload();
                return false;
            });
        });
    </script>
@stop
