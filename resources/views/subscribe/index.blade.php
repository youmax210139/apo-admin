@extends('layouts.base')

@section('title','订阅管理')
@section('function','订阅管理')
@section('function_link', '/subscribe/')
@section('here','订阅列表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/subscribe/" method="post">
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
                                <label class="col-sm-3 col-sm-3 control-label">邮箱</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='email' placeholder="邮箱" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="created_start_date" class="col-sm-3 control-label">订阅时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="created_start_date"
                                               id='created_start_date' value="" placeholder="开始时间" autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="created_end_date"
                                               id='created_end_date' value="" placeholder="结束时间" autocomplete="off">
                                    </div>
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
                        <button type="button" class="btn btn-warning margin" id="export_btn">
                            <i class="fa fa-download" aria-hidden="true"></i>导出
                        </button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->
            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">ID</th>
                            <th class="hidden-sm" data-sortable="false">邮箱</th>
                            <th class="hidden-sm" data-sortable="false">订阅时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
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
            elem: '#created_start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);
        layConfig.elem = '#created_end_date';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'email': $("input[name='email']").val(),
                    'created_start_date': $("input[name='created_start_date']").val(),
                    'created_end_date': $("input[name='created_end_date']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                iDisplayLength: 100,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "email"},
                    {"data": "created_at"}
                ]
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

            $('#export_btn').click(function () {
                $('#search').submit();
            });

            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
        });
    </script>
@stop