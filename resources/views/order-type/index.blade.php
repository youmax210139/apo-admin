@extends('layouts.base')

@section('title','账变类型')

@section('function','账变类型')
@section('function_link', '/orderType/')

@section('here','类型列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('orderType/create'))
                <a href="/orderType/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加类型
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th class="hidden-sm">ID</th>
                            <th class="hidden-sm">唯一标识</th>
                            <th data-sortable="false">名称</th>
                            <th data-sortable="false">类型</th>
                            <th data-sortable="false">前台显示</th>
                            <th data-sortable="false">余额加减</th>
                            <th data-sortable="false">冻结金额加减</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-delete" tabIndex="-1">
        <div class="modal-dialog modal-danger">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        <i class="fa fa-question-circle fa-lg"></i>
                        确认要删除这个权限吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i> 确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @stop

            @section('js')
             <script src="/assets/js/app/common.js" charset="UTF-8"></script>
             <script>
                    $(function () {
                        var table = $("#tags-table").DataTable({
                        	language:app.DataTable.language(),
                            order: [[0, "asc"]],
                            serverSide: true,
                            pageLength:25,
                            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                            // 要 ajax(url, type) 必须加这两参数
                            ajax: app.DataTable.ajax(),
                            "columns": [
                                {"data": "id"},
                                {"data": "ident"},
                                {"data": "name"},
                                {"data": "category"},
                                {"data": "display"},
                                {"data": "operation"},
                                {"data": "hold_operation"},
                                {"data": "action"}
                            ],
                            columnDefs: [
                                {
                                    'targets': -1, "render": function (data, type, row) {
                                    var row_edit = {{Gate::check('ordertype/edit') ? 1 : 0}};
                                    var row_delete = {{Gate::check('ordertype/delete') ? 1 :0}};
                                    var str = '';

                                    //编辑
                                    if (row_edit) {
                                        str += '<a style="margin:3px;" href="/ordertype/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                    }

                                    //删除
                                    if (row_delete) {
                                        str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
                                    }

                                    return str;

                                }
                                },
                                {
                                    "targets": 3,
                                    "searchable": false,
                                    "render": function (data, type, row) {
                                        return row.category==1?"彩票账变":(row.category==2?"充提账变":"第三方账变");
                                    }
                                },
                                {
                                    "targets": 4,
                                    "searchable": false,
                                    "render": function (data, type, row) {
                                        return  app.getLabelHtml(row.display==0?"否":"是",row.display==0?"label-danger":"label-success");
                                    }
                                },
                                {
                                    "targets": 5,
                                    "searchable": false,
                                    "render": function (data, type, row) {
                                        return  app.getLabelHtml(row.operation==0?"无操作":(row.operation==1?"增":"减"),row.operation==0?"label-default":(row.operation==1?"label-success":"label-danger"));
                                    }
                                },
                                {
                                    "targets": 6,
                                    "searchable": false,
                                    "render": function (data, type, row) {
                                        return  app.getLabelHtml(row.hold_operation==0?"无操作":(row.hold_operation==1?"增":"减"),row.hold_operation==0?"label-default":(row.hold_operation==1?"label-success":"label-danger"));
                                    }
                                }
                            ]
                        });

                        table.on('preXhr.dt', function () {
                            loadShow();
                        });

                        table.on('draw.dt', function () {
//                            table.column(0).nodes().each(function (cell, i) {
//                                cell.innerHTML = i + 1;
//                            });
                            loadFadeOut();
                        });

                        $("table").delegate('.delBtn', 'click', function () {
                            var id = $(this).attr('attr');
                            $('.deleteForm').attr('action', '/orderType/?id=' + id);
                            $("#modal-delete").modal();
                        });
                    });
                </script>
@stop