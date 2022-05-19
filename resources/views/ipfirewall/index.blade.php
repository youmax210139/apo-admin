@extends('layouts.base')

@section('title','后台IP白名单')

@section('function','后台IP白名单')
@section('function_link', '/ipfirewall/')

@section('here','后台IP白名单')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('ipfirewall/create'))
                <a href="/ipfirewall/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加IP记录
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
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">IP</th>
                            <th class="hidden-sm">备注</th>
                            <th class="hidden-sm">操作管理员</th>
                            <th class="hidden-sm">添加时间</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="delete_by_select" id="delete_by_select" value="删除所选" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-msg" tabIndex="-1">
        <div class="modal-dialog">
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
                        <span id="tips_content"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">确定</button>
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
                        <span id="delete_tips_content">确认要删除IP记录吗?</span>
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="id" id="select_ids" value="">
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
             <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Select/css/select.dataTables.min.css" />
             <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css" />
             <script src="/assets/plugins/datatables/extensions/Select/js/dataTables.select.min.js" charset="UTF-8"></script>
             <script src="/assets/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js" charset="UTF-8"></script>
             <script src="/assets/js/app/common.js" charset="UTF-8"></script>
             <script>
                    $(function () {
                        var table = $("#tags-table").DataTable({
                        	language:app.DataTable.language(),
                            order: [[0, "asc"]],
                            pageLength: 25,
                            serverSide: true,
                            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                            // 要 ajax(url, type) 必须加这两参数
                            ajax: app.DataTable.ajax(),
                            "columns": [
                                {"data": "id"},
                                {"data": "ip"},
                                {"data": "remark"},
                                {"data": "admin"},
                                {"data": "created_at"},
                                {"data": "action"}
                            ],
                            columnDefs: [
                                {
                                    'targets': -1, "render": function (data, type, row) {
                                    var row_edit = {{Gate::check('ipfirewall/edit') ? 1 : 0}};
                                    var row_delete = {{Gate::check('ipfirewall/delete') ? 1 :0}};
                                    var str = '';

                                    //编辑
                                    /*
                                    if (row_edit) {
                                        str += '<a style="margin:3px;" href="/ipfirewall/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                    }
                                    */

                                    //删除
                                    if (row_delete) {
                                        str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
                                    }

                                    return str;

                                }
                                }
                            ],
                            dom: "<'row'<'col-sm-6'Bl><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            select: {
                                style: 'multi'
                            },
                            buttons: [
                                {
                                    text: '全选',
                                    action: function () {
                                        if( table.rows( { selected: true } ).count() == table.rows().count() ){
                                            table.rows().deselect();
                                        }else{
                                            table.rows().select();
                                        }
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
                            $('.deleteForm').attr('action', '/ipfirewall/?id=' + id);
                            $("#modal-delete").modal();
                        });
                        $("#delete_by_select").bind('click', function () {
                            var select_rows = table.rows( { selected: true } );
                            var id_array = select_rows.data().pluck( 'id' ).toArray();
                            if(id_array.length==0){
                                $("#tips_content").html("请选择需要删除的IP记录");
                                $("#modal-msg").modal();
                                return false;
                            }
                            $("#delete_tips_content").html("确认要删除选中的IP记录吗？");
                            $('#select_ids').val(id_array.join(','));
                            $('.delete_form').attr('action', '/ipfirewall/');
                            $("#modal-delete").modal();
                        });
                    });

                </script>
@stop