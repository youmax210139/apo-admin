@extends('layouts.base')
@section('title','每日用户报表')
@section('function','每日用户报表')
@section('function_link', '/userdatereport/')
@section('here','每日用户报表')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/reportdateuser/" method="post">
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
                        </div>
                        <div class="col-md-4">
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn">
                            <i class="fa fa-search" aria-hidden="true"></i>查询
                        </button>
                        <button type="submit" class="btn btn-warning margin"><i class="fa fa-download" aria-hidden="true"></i>导出</button>
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
                                <th class="hidden-sm">日期</th>
                                <th class="hidden-sm" data-sortable="false">登陆用户数</th>
                                <th class="hidden-sm" data-sortable="false">充值人数</th>
                                <th class="hidden-sm" data-sortable="false">充值人次</th>
                                <th class="hidden-sm" data-sortable="false">提现人数</th>
                                <th class="hidden-sm" data-sortable="false">首次绑卡人数</th>
                                <th class="hidden-sm" data-sortable="false">首充人数</th>
                                <th class="hidden-sm" data-sortable="false">首充金额</th>
                                <th class="hidden-sm" data-sortable="false">二充人数</th>
                                <th class="hidden-sm" data-sortable="false">新增用户数</th>
                                <th class="hidden-sm" data-sortable="false">投注人数</th>
                                <th class="hidden-sm" data-sortable="false">活跃用户数</th>
                                <th class="hidden-sm" data-sortable="false">活跃代理数</th>
                                <th class="hidden-sm" data-sortable="false">第三方投注人数</th>
                                <th class="hidden-sm" data-sortable="false">第三方活跃用户数</th>
                                <th class="hidden-sm" data-sortable="false">第三方活跃代理数</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr id="total_sum" style="display: none;">
                                <th class="text-right"><b>全部总计： </b></th>
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
            format: 'YYYY-MM-DD',
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
                    'start_time': $("input[name='start_time']").val(),
                    'end_time': $("input[name='end_time']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
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
                    {"data": "data.login_user"},
                    {"data": "data.deposit_user"},
                    {"data": "data.deposit_number"},
                    {"data": "data.withdrawals_user", defaultContent:"-"},
                    {"data": "data.first_user_bind_bank", defaultContent:"-"},
                    {"data": "data.first_deposit_user"},
                    {"data": "data.first_deposit_sum", defaultContent: '-'},
                    {"data": "data.second_deposit_user"},
                    {"data": "data.new_user"},
                    {"data": "data.bet_user"},
                    {"data": "data.active_user"},
                    {"data": "data.active_proxy"},
                    {"data": "data.third_bet_user"},
                    {"data": "data.third_active_user"},
                    {"data": "data.third_active_proxy"}
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                    return ;
                }
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
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
