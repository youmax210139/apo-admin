@extends('layouts.base')

@section('title','自动账变')

@section('function','自动账变')
@section('function_link', '/shadowfund/')

@section('here','自动账变')

{{--@section('pageDesc','DashBoard')--}}

@section('content')


    <div class="row">
        <div class="col-md-12">

            <!--搜索框 Start-->
            <div class="box box-primary">
                <form id="search" class="form-horizontal" action="/shadowfund/" method="post">
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
                        <div class="form-group">
                            <label class="col-sm-3 control-label">用户名</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="username" value="{{$username}}" placeholder="用户名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">账变时间</label>
                            <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="start_time" value="{{ $start_time }}"  id='start_time'   placeholder="开始时间">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="end_time" value="{{ $end_time }}"  id='end_time'  placeholder="结束时间">
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->

            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">可用金额</th>
                            <th class="hidden-sm">冻结金额</th>
                            <th class="hidden-sm">积分</th>
                            <th class="hidden-sm">可用金额变化</th>
                            <th class="hidden-sm">冻结金额变化</th>
                            <th class="hidden-sm">积分变化</th>
                            <th class="hidden-sm">时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>

                        <tfoot>
                        <tr id="page_total">
                            <th colspan="5" class="text-right"><b>本页总计： </b></th>
                            <th class="text-left"><b>收入：<span id="page_balance_diff_in" class="text-green"></span>&nbsp;&nbsp;支出：<span id="page_balance_diff_out" class="text-red"></span></b></th>
                            <th class="text-left"><b>收入：<span id="page_hold_balance_diff_in" class="text-green"></span>&nbsp;&nbsp;支出：<span id="page_hold_balance_diff_out" class="text-red"></span></b></th>
                            <th class="text-left"><b>收入：<span id="page_points_diff_in" class="text-green"></span>&nbsp;&nbsp;支出：<span id="page_points_diff_out" class="text-red"></span></b></th>
                            <th></th>
                        </tr>
                        <tr id="all_total">
                            <th colspan="5" class="text-right"><b>全部总计： </b></th>
                            <th class="text-left"><b>收入：<span id="all_balance_diff_in" class="text-green"></span>&nbsp;&nbsp;支出：<span id="all_balance_diff_out" class="text-red"></span></b></th>
                            <th class="text-left"><b>收入：<span id="all_hold_balance_diff_in" class="text-green"></span>&nbsp;&nbsp;支出：<span id="all_hold_balance_diff_out" class="text-red"></span></b></th>
                            <th class="text-left"><b>收入：<span id="all_points_diff_in" class="text-green"></span>&nbsp;&nbsp;支出：<span id="all_points_diff_out" class="text-red"></span></b></th>
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
        var layConfig ={
            elem: '#start_time',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex:2
        };
        laydate(layConfig);

        layConfig.elem = '#end_time';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'username'       : $("input[name='username']").val(),
                    'start_time'     : $("input[name='start_time']").val(),
                    'end_time'       : $("input[name='end_time']").val(),
                };

                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
            	language:app.DataTable.language(),
                bSort:false,
                serverSide: true,
                pageLength:50,
                searching:false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "username"},
                    {"data": "balance"},
                    {"data": "hold_balance"},
                    {"data": "points"},
                    {"data": "balance_diff"},
                    {"data": "hold_balance_diff"},
                    {"data": "points_diff"},
                    {"data": "created_at"}
                ],
                columnDefs: [
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            if(row.points_diff > 0) {
                                return '<span class="text-green text-bold">+' + row.points_diff + '</span>';
                            } else if (row.points_diff < 0) {
                                return '<span class="text-danger text-bold">' + row.points_diff + '</span>';
                            } else {
                                return '<span class="text-black text-bold">' + row.points_diff + '</span>';
                            }
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            if(row.hold_balance_diff > 0) {
                                return '<span class="text-green text-bold">+' + row.hold_balance_diff + '</span>';
                            } else if(row.hold_balance_diff < 0) {
                                return '<span class="text-danger text-bold">' + row.hold_balance_diff + '</span>';
                            } else {
                                return '<span class="text-black text-bold">' + row.hold_balance_diff + '</span>';
                            }
                        }
                    },
                    {
                        'targets': -4,
                        'render': function (data, type, row) {
                            if(row.balance_diff > 0) {
                                return '<span class="text-green text-bold">+' + row.balance_diff + '</span>';
                            } else if(row.balance_diff < 0) {
                                return '<span class="text-danger text-bold">' + row.balance_diff + '</span>';
                            } else {
                                return '<span class="text-black text-bold">' + row.balance_diff + '</span>';
                            }
                        }
                    }
                ],

                "footerCallback": function (tfoot, data, start, end, display){
                    var page_balance_diff_in = page_balance_diff_out = 0;
                    var page_hold_balance_diff_in = page_hold_balance_diff_out = 0;
                    var page_points_diff_in = page_points_diff_out = 0;
                    var all_balance_diff_in = all_balance_diff = 0;
                    var all_hold_balance_diff_in = all_hold_balance_diff_out = 0;
                    var all_points_diff_in = all_points_diff_in = 0;

                    for(item in data){
                        data[item].balance_diff = parseFloat(data[item].balance_diff);
                        data[item].hold_balance_diff = parseFloat(data[item].hold_balance_diff);
                        data[item].points_diff = parseFloat(data[item].points_diff);
                        if(data[item].balance_diff > 0) {
                            page_balance_diff_in += data[item].balance_diff;
                        } else if(data[item].balance_diff < 0) {
                            page_balance_diff_out += data[item].balance_diff;
                        }
                        if(data[item].hold_balance_diff > 0) {
                            page_hold_balance_diff_in += data[item].hold_balance_diff;
                        } else if(data[item].hold_balance_diff < 0) {
                            page_hold_balance_diff_out += data[item].hold_balance_diff;
                        }
                        if(data[item].points_diff > 0) {
                            page_points_diff_in += data[item].points_diff;
                        } else if(data[item].balace_diff < 0) {
                            page_points_diff_out += data[item].points_diff;
                        }
                    }
                    $("#page_balance_diff_in").html('+'+page_balance_diff_in.toFixed(4));
                    $("#page_balance_diff_out").html(page_balance_diff_out.toFixed(4));
                    $("#page_hold_balance_diff_in").html('+'+page_hold_balance_diff_in.toFixed(4));
                    $("#page_hold_balance_diff_out").html(page_hold_balance_diff_out.toFixed(4));
                    $("#page_points_diff_in").html('+'+page_points_diff_in);
                    $("#page_points_diff_out").html(page_points_diff_out);

                }

            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                table.column(0).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
                loadFadeOut();
            });

            table.on('xhr.dt', function ( e, settings, json, xhr ) {
                if(typeof json.statistics != 'undefined' && typeof json.statistics.balance_diff_in != 'undefined'){
                    $("#all_balance_diff_in").html('+'+json.statistics.balance_diff_in);
                    $("#all_balance_diff_out").html(json.statistics.balance_diff_out);
                    $("#all_hold_balance_diff_in").html('+'+json.statistics.hold_balance_diff_in);
                    $("#all_hold_balance_diff_out").html(json.statistics.hold_balance_diff_out);
                    $("#all_points_diff_in").html('+'+json.statistics.points_diff_in);
                    $("#all_points_diff_out").html(json.statistics.points_diff_out);
                } else {
                    $("#all_balance_diff_in").html('+0');
                    $("#all_balance_diff_out").html(0);
                    $("#all_hold_balance_diff_in").html('+0');
                    $("#all_hold_balance_diff_out").html(0);
                    $("#all_points_diff_in").html('+0');
                    $("#all_points_diff_out").html(0);
                }
            });

            $('#search_btn').click(function(event){
                event.preventDefault();
                table.ajax.reload();
            });

        });
    </script>
@stop
