@extends('layouts.base')

@section('title','系统日志查询')

@section('function','系统日志查询')
@section('function_link', '/syslog/')

@section('here','系统日志查询')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">

                    <div class="box-body">
                        <table id="tags-table"
                               class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                            <thead>
                            <tr>
                                <th >文件名</th>
                                <th >大小</th>
                                <th >修改时间</th>
                                <th data-sortable="false">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($directories as $file)
                                <tr>
                                    <td>
                                        <span class="text-primary">
                                            {{$file}}
                                        </span></td>
                                    <td>
                                        <span>
                                            --
                                        </span></td>
                                    <td>
                                        <span>
                                            {{date('Y-m-d H:i:s',Storage::disk('logs')->lastModified($file))}}
                                        </span></td>
                                    <td>
                                        <a href="/syslog/?path={{$file}}" class="X-Small btn-xs text-primary ">查看</a>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($files as $file)
                            <tr>
                                <td>
                                    <span class="text-primary" style="text-decoration: underline">
                                        {{$file}}
                                    </span></td>
                                <td>
                                        <span>
                                            {{number_format(Storage::disk('logs')->size($file)/1024)}} KB
                                        </span></td>
                                <td>
                                        <span>
                                            {{date('Y-m-d H:i:s',Storage::disk('logs')->lastModified($file))}}
                                        </span></td>
                                <td>
                                    <a href="/syslog/download/?file={{$file}}" class="X-Small btn-xs text-primary ">下载</a>
                                    <a href="/syslog/download/?file={{$file}}&flag=view" class="X-Small btn-xs text-primary ">查看</a>
                                </td>
                            </tr>
                                @endforeach
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
                    'path': $("input[name='path']").val(),
                };
                return $.extend({}, data, param);
            };

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [],
                serverSide: true,
                searching: false,
                pageLength: 50,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "username"},
                    {"data": "path"},
                    {"data": "request"},
                    {"data": "created_at"},
                    {"data": null}
                ],
                columnDefs: [
                    {
                        'targets': -1, "render": function (data, type, row) {
                        return '<a href="/requestlog/detail?id=' + row['id'] + '&type=' + row['type'] + '" class="X-Small btn-xs text-success " data-target="#modal-detail" data-toggle="modal"><i class="fa fa-file-text-o"></i> 详情</a>';
                        ;
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