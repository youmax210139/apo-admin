@extends('layouts.base')
@section('title','设备盈亏日报表')
@section('function','设备盈亏日报表')
@section('function_link', '/dailydevice/')
@section('here','设备盈亏日报表')
@section('content')
    <div class="row">
        <div class="col-sm-12">
        <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/dailydevice" method="post">
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
                                    <label class="col-sm-3 control-label text-right">设备</label>
                                    <div class="col-sm-9">
                                        <select name="client_type" class="form-control">
                                            <option value="all">所有设备</option>
                                            @foreach ($source_list as $key=>$client)
                                                <option value='{{ $key }}'>{{ $client }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="created_date" class="col-sm-2 control-label">日期</label>
                                    <div class="col-sm-10">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control form_datetime" name="start_date"
                                                   id='start_date' value="{{$start_date}}" placeholder="开始日期"
                                                   autocomplete="off">
                                            <span class="input-group-addon">~</span>
                                            <input type="text" class="form-control form_datetime" name="end_date"
                                                   id='end_date' value="{{$end_date}}" placeholder="结束日期"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="btn-group col-md-4">
                            <button type="button" class="btn btn-primary col-sm-2 pull-right" id="search_btn">
                                <i class="fa fa-search" aria-hidden="true"></i>查询
                            </button>
                        </div>
                        <div class=" btn-group col-md-4">
                            <button type="reset" class="btn btn-default col-sm-2"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                        </div>
                        <div class="btn-group col-md-4">
                            <button type="submit" onclick="$('#search').submit()" class="btn btn-warning col-sm-2 pull-right" id="export">
                                <i class="fa fa-download" aria-hidden="true"></i>导出
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="box box-primary">
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">日期</th>
                            <th class="hidden-sm" data-sortable="false">设备</th>
                            <th class="hidden-sm" data-sortable="false">投注</th>
                            <th class="hidden-sm" data-sortable="false">返点</th>
                            <th class="hidden-sm" data-sortable="false">盈利</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2"><b>总计：</b></th>
                            <th id="sum_price">0</th>
                            <th id="sum_rebate">0</th>
                            <th id="sum_profit">0</th>
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
                    'client_type': $('select[name="client_type"]').val(),
                };
                return $.extend({}, data, param);
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                serverSide: true,
                order: [[0, 'desc']],
                pageLength: 50,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "date"},
                    {"data": "client_type"},
                    {"data": "total_price"},
                    {"data": "total_rebate"},
                    {"data": "total_profit"},
                ],
                columnDefs: [
                    {
                        "targets": 1,
                        "searchable": false,
                        "render": function (data, type, row) {
                            var client_types = {
                                0: "Unknown", 1: "WEB", 2: "IOS", 3: "Android", 4: "挂机", 5: 'WAP'
                            };
                            return client_types[data];
                        },
                    },
                    {
                        "targets": -1,
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
                if (typeof json['sum_total'] == 'object') {
                    $('#sum_price').html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_total']['sum_price']), 'text-green', true)
                    );
                    $('#sum_rebate').html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_total']['sum_rebate']), 'text-red', true)
                    );
                    $('#sum_profit').html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_total']['sum_profit']), json['sum_total']['sum_profit'] < 0 ? 'text-red' : 'text-green', true)
                    );
                }
            });
            table.on('draw.dt', function () {
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
