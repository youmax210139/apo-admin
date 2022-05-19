@extends('layouts.base')
@section('title','系统异常监控')
@section('function','系统异常监控')
@section('function_link', '/monitor/')
@section('here','事件列表')
@section('content')
    <div class="row">
        <div class="col-sm-12">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" id='end_date' value="{{$end_date}}" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="btn-group col-md-6">
                                    <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn">查询</button>
                                </div>
                                <div class=" btn-group col-md-6">
                                    <button type="reset" class="btn btn-default col-sm-2">重置</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box box-primary">
        @include('partials.errors')
        @include('partials.success')
        <div class="box-body">
            <table id="tags-table" class="table table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th data-sortable="false">事件类型</th>
                    <th data-sortable="false">描述</th>
                    <th data-sortable="false">时间</th>
                    <th data-sortable="false">操作</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal-detail" tabIndex="-1" role="dialog">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content"></div>
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
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                };
                return $.extend({}, data, param);
            };

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [],
                serverSide: true,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "type"},
                    {"data": "description"},
                    {"data": "created_at"},
                    {"data": null}
                ],
                columnDefs: [
                    {
                        'targets': -1, "render": function (data, type, row) {
                            return '<a href="/monitor/detail?id=' + row['id'] + '" class="X-Small btn-xs text-success " data-target="#modal-detail" data-toggle="modal"><i class="fa fa-file-text-o"></i> 详情</a>';
                        }
                    }
                ]
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

            $('#modal-detail').on('show.bs.modal', function () {
                loadShow();
            });

            $('#modal-detail').on('hidden.bs.modal', function () {
                $(this).find(".modal-content").html('');
                $(this).removeData();
            });

            $("#modal-detail").on('loaded.bs.modal', function () {//数据加载完成后删除loading
                loadFadeOut();
            });
        });
    </script>
@stop
