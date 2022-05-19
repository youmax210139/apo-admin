@extends('layouts.base')

@section('title','同投注IP反查')
@section('function','同投注IP反查')
@section('function_link', '/userbetip/')
@section('here','同投注IP反查')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <div class="box-body">
                    <form class="form-inline" id="search">
                        <div class="form-group">
                            <label for="exampleInputName2">IP地址 </label>
                            <input type="text" class="form-control" name="ip" id="ip" placeholder="IP地址">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName2">开始时间 </label>
                            <input type="text" class="form-control" value="{{$start_date}}" name="start_date" id="start_date" placeholder="">
                        </div>
                        -
                        <div class="form-group">
                            <label for="exampleInputName2">结束时间 </label>
                            <input type="text" class="form-control" value="{{$end_date}}" name="end_date" id="end_date" placeholder="">
                        </div>
                        <button type="submit" class="btn btn-primary">查询</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-body">
            <table id="tags-table" class="table table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th data-sortable="false">用户名</th>
                    <th data-sortable="false">所属总代</th>
                    <th data-sortable="false">所属组别</th>
                    <th data-sortable="false">余额</th>
                    <th data-sortable="false">登录IP</th>
                    <th data-sortable="false">次数</th>
                    <th data-sortable="false">总金额</th>
                    <th data-sortable="false">此IP最后投注时间</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        $(function () {
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
            var get_params = function (data) {
                var param = {
                    'ip': $("input[name='ip']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                };
                return $.extend({}, data, param);
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                pageLength: 50,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "username"},
                    {"data": "top_username"},
                    {"data": "user_group_id"},
                    {"data": "balance"},
                    {"data": "ip"},
                    {"data": "total"},
                    {"data": "total_price"},
                    {"data": "created_at"}
                ],
                columnDefs: [
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            return row['user_group_id'] == 1 ? '正式' : (row['user_group_id'] == 2 ? '测试' : '试玩');
                        }
                    }
                ],
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
