@extends('layouts.base')

@section('title','角色管理')

@section('function','角色管理')
@section('function_link', '/role/')

@section('here','角色列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('role/create'))
                <a href="/role/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加角色
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
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">角色名称</th>
                            <th class="hidden-sm">角色描述</th>
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
    <form class="exportForm" id="export" method="POST" >
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="export" value="1">
    </form>
    <div class="modal fade" id="modal-import" tabIndex="-1">
        <div class="modal-dialog modal-primary">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">上传CSV文件</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <form class="importForm" method="POST" enctype="multipart/form-data" onsubmit="return checkForm()">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="import" value="1">
                        <input type="file"   name="import_file" id="import_file" value="" accept=".csv">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default"> 保存</button>
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
                                {"data": "name"},
                                {"data": "description"},
                                {"data": "action"}
                            ],
                            columnDefs: [
                                {
                                    'targets': -1, "render": function (data, type, row) {
                                    var row_edit = {{Gate::check('role/edit') ? 1 : 0}};
                                    var row_delete = {{Gate::check('role/delete') ? 1 :0}};
                                    var str = '';

                                    //编辑
                                    if (row_edit) {
                                        str += '<a style="margin:3px;" href="/role/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                    }

                                    //删除
                                    if (row_delete) {
                                        str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
                                    }

                                    //角色管理员列表
                                    str += '<a href="/admin/?role_id=' + row['id'] + '" class="X-Small btn-xs text-primary ">查看成员</a>';

                                    //导出
                                    str += '<a style="margin:3px;"href="#" attr="' + row['id'] + '" class="expBtn X-Small btn-xs text-success"><i class="fa fa-download"></i> 导出</a>';

                                    //导入
                                    str += '<a style="margin:3px;"href="#" attr="' + row['id'] + '" name="' +  row['name'] + '" class="inpBtn X-Small btn-xs text-primary"><i class="fa fa-upload" aria-hidden="true"></i> 导入</a>';

                                    return str;

                                }
                                }
                            ]
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

                        $("table").delegate('.delBtn', 'click', function () {
                            var id = $(this).attr('attr');
                            $('.deleteForm').attr('action', '/role/?id=' + id);
                            $("#modal-delete").modal();
                        });

                        $("table").delegate('.expBtn', 'click', function () {
                            var id = $(this).attr('attr');
                            $('.exportForm').attr('action', '/role/index?id=' + id);
                            $('#export').submit();
                        });

                        $("table").delegate('.inpBtn', 'click', function ()
                        {
                            var id = $(this).attr('attr');
                            var name = $(this).attr('name');
                            $(".modal-title").html('导入 [  '+name+' ]'+'角色');
                            $('.importForm').attr('action', '/role/index?id=' + id);
                            $("#modal-import").modal();
                        });
                    });
                    function checkForm() {
                        var import_file = $("#import_file").val();
                        if(import_file == '') {
                            BootstrapDialog.alert("选择上传CSV文件");
                            return false;
                        } else {
                            return true;
                        }
                     }
                </script>
@stop
