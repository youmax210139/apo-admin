@extends('layouts.base')

@section('title','登录日志查询')

@section('function','登录日志查询')
@section('function_link', '/loginlog/')

@section('here','登录日志列表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/loginlog/" method="post">
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
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" />
                                </div>
                            </div>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type" class="col-sm-3 control-label">操作系统</label>
                                <div class="col-sm-9">
                                    <select name="os" class="form-control">
                                        <option value="">全部</option>
                                        <option value="Windows">Windows</option>
                                        <option value="Android">Android</option>
                                        <option value="iOS">iOS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="action" class="col-sm-3 col-sm-3 control-label">IP</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='ip' placeholder="IP" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type" class="col-sm-3 control-label">查询类型</label>
                                <div class="col-sm-9">
                                    <select name="type" class="form-control">
                                        <option value="0" selected="selected">用户</option>
                                        <option value="1">管理员</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                        <button type="submit" class="btn btn-warning margin"><i class="fa fa-download" aria-hidden="true"></i>导出</button>
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
                        <th data-sortable="false">流水号</th>
                        <th data-sortable="false">用户名</th>
                        <th data-sortable="false">域名</th>
                        <th data-sortable="false">国家/地区</th>
                        <th data-sortable="false">浏览器</th>
                        <th data-sortable="false">浏览器版本</th>
                        <th data-sortable="false">操作系统</th>
                        <th data-sortable="false">设备</th>
                        <th data-sortable="false">IP</th>
                        <th data-sortable="false">时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                'username': $("input[name='username']").val(),
                'start_date': $("input[name='start_date']").val(),
                'end_date': $("input[name='end_date']").val(),
                'type': $("select[name='type']").val(),
                'ip': $("input[name='ip']").val(),
                'os': $("select[name='os']").val(),
            };
            return $.extend({}, data, param);
        };

        var table = $("#tags-table").DataTable({
            language:app.DataTable.language(),
            order: [],
            serverSide: true,
            searching: false,
            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
            // 要 ajax(url, type) 必须加这两参数
            ajax: app.DataTable.ajax(null, null, get_params),
            "columns": [
                {"data": "id"},
                {"data": "username"},
                {"data": "domain"},
                {"data": "province"},
                {"data": "browser"},
                {"data": "browser_version"},
                {"data": "os"},
                {"data": "device"},
                {"data": "ip"},
                {"data": "created_at"},
            ],
            columnDefs: [
                {
                    'targets': 1,
                    'render': function (data, type, row) {
                        if (row['user_observe']) {
                            return app.getColorHtml(row.username, 'label-danger', false);
                        } else {
                            return row.username;
                        }
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
        $('#search_btn').click(function (event) {
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