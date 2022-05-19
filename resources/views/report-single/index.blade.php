@extends('layouts.base')
@section('title','单项查询报表')
@section('function','单项查询报表')
@section('function_link', '/reportsingle/')
@section('here','单项查询报表')
@section('content')
    <div class="row">
        <div class="col-sm-12">
        @include('partials.errors')
        @include('partials.success')
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
                                <label class="col-sm-3 control-label text-right">数据类型</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="data_type" id="data_type">
                                        <option value="0">工资</option>
                                        <!--
                                        <option value="1">彩票[原始]</option>
                                        <option value="2">彩票[压缩]</option>
                                        <option value="3">活动</option>
                                        <option value="4">充值</option>
                                        <option value="5">提现</option>
                                        -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search_type" class="col-sm-3 control-label">查询类型</label>
                                <div class="col-sm-9">
                                    <select name="search_type" class="form-control">
                                        <option value="0">个人</option>
                                        <option value="1">团队</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" autocomplete="off"/>
                                </div>
                                <label class="control-label checkbox-inline col-sm-4" style="padding-top: 7px;">
                                    <input type="checkbox" name="include_all" value="1"/>
                                    包含所有下级
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_time" class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="start_time" id="start_time" placeholder="开始时间" autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control " name="end_time" id="end_time" placeholder="结束时间" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_min" class="col-sm-3 control-label">金额</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="amount_min" placeholder="最低金额" autocomplete="off">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="amount_max" placeholder="最高金额" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-3 control-label">用户组别</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="0">全部</option>
                                        <option value="1">正式组</option>
                                        <option value="2">测试组</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn">
                            <i class="fa fa-search" aria-hidden="true"></i>查询
                        </button>
                        <button type="reset" class="btn btn-default margin">
                            <i class="fa fa-refresh" aria-hidden="true"></i>重置
                        </button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->
            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th class="hidden-sm">ID</th>
                            <th class="hidden-sm" data-sortable="false">用户ID</th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" data-sortable="false">用户级别</th>
                            <th class="hidden-sm" data-sortable="false">用户组别</th>
                            <th class="hidden-sm">金额</th>
                            <th class="hidden-sm">生成时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5" class="text-right"><b>本页总计： </b></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right"><b>全部总计： </b></th>
                            <th id="total_sum"></th>
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
                    'data_type': $('select[name="data_type"]').val(),
                    'search_type': $('select[name="search_type"]').val(),
                    'username': $("input[name='username']").val(),
                    'include_all': $("input[name='include_all']:checked").val(),
                    'user_group_id': $('select[name="user_group_id"]').val(),
                    'start_time': $("input[name='start_time']").val(),
                    'end_time': $("input[name='end_time']").val(),
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                };
                return $.extend({}, data, param);
            }

            function getColumns() {
                var data_type = $('select[name="data_type"]').val();
                switch (data_type) {
                    case '0':
                        return [
                            {"data": "id"},
                            {"data": "user_id"},
                            {"data": "username"},
                            {"data": "user_type_name"},
                            {"data": "user_group_name"},
                            {"data": "amount"},
                            {"data": "created_at"},
                        ];
                    case '1':
                        return [
                            {"data": "id"},
                            {"data": "user_id"},
                            {"data": "username"},
                            {"data": "user_type_name"},
                            {"data": "user_group_name"},
                            {"data": "price"},
                            {"data": "bonus"},
                            {"data": "rebate"},
                            {"data": "created_at"},
                        ];
                    case '2':
                        return [
                            {"data": "id"},
                            {"data": "user_id"},
                            {"data": "username"},
                            {"data": "user_type_name"},
                            {"data": "user_group_name"},
                            {"data": "price"},
                            {"data": "bonus"},
                            {"data": "rebate"},
                            {"data": "created_at"},
                        ];
                    case '3':
                        return [
                            {"data": "id"},
                            {"data": "user_id"},
                            {"data": "username"},
                            {"data": "user_type_name"},
                            {"data": "user_group_name"},
                            {"data": "bonus"},
                            {"data": "created_at"},
                        ];
                    case '4':
                    case '5':
                        return [
                            {"data": "id"},
                            {"data": "user_id"},
                            {"data": "username"},
                            {"data": "user_type_name"},
                            {"data": "user_group_name"},
                            {"data": "amount"},
                            {"data": "platform_fee"},
                            {"data": "created_at"},
                        ];
                }
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                serverSide: true,
                order: [[1, "asc"]],
                pageLength: 50,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": getColumns(),
                "footerCallback": function (tfoot, data, start, end, display) {
                    var amount = 0;

                    for (x in data) {
                        var row = data[x];
                        amount += parseFloat(row['amount']);
                    }

                    $(tfoot).find('th').eq(1).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(amount)
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
                if (json['amount'] !== undefined && json !== null) {
                    $('#total_sum').html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['amount'])
                    );
                }
            });

            $('#data_type').bind('change', function () {
                $("#tags-table").DataTable({
                    ajax: app.DataTable.ajax(null, null, get_params),
                    "columns": getColumns()
                });
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
