@extends('layouts.base')

@section('title','号源客户')

@section('function','号源客户')
@section('function_link', '/drawclients/')

@section('here','客户列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('drawclients/create'))
                <a href="/drawclients/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加客户
                </a>
            @endif
        </div>
    </div>
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">

                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <div class="bg-gray text-center" style="height:36px; line-height: 36px;">
                        请求模式：@if($openapi_switch === '1')<span class="label label-success">开启</span> @else <span class="label label-danger">关闭</span> @endif
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        推送模式：@if($pushservice_switch === '1')<span class="label label-success">开启</span> @else <span class="label label-danger">关闭</span> @endif
                    </div>
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">中文名称</th>
                            <th class="hidden-sm">英文标识</th>
                            <th class="hidden-sm">请求状态</th>
                            <th class="hidden-sm">请求IP</th>
                            <th class="hidden-sm">推送状态</th>
                            <th class="hidden-sm">推送URL</th>
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
@stop

    @section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        $(function () {
            var table = $("#tags-table").DataTable({
            	language:app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                pageLength:50,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(),
                "columns": [
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "ident"},
                    {"data": "request_status"},
                    {"data": "request_ips"},
                    {"data": "push_status"},
                    {"data": "push_url"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var row_edit = {{Gate::check('drawclients/edit') ? 1 : 0}};
                            var str = '';
                            var a_attr = null;
                            var common_class = 'X-Small btn-xs ';
                            //编辑
                            if (row_edit) {
                                a_attr = {
                                    'class' : common_class + 'text-primiry',
                                    'href': '/drawclients/edit?id=' + row['id']
                                };
                                str += app.getalinkHtml('编辑',a_attr, 'fa-edit');
                            }
                            return str;
                        },
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            return showStatus(row['id'], row['push_status'], 'push');
                        }
                    },
                    {
                        'targets': -5,
                        'render': function (data, type, row) {
                            return showStatus(row['id'], row['request_status'], 'request');
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
        });
        function showStatus(id, curr_status, op_type) {
            var str='';
            if(curr_status) {
                str = '<a id="'+op_type+'_status_'+id+'" class="label label-success" title="点击禁用" onclick="opStatus('+id+', 0, \''+op_type+'\')">启用</a>';
            } else {
                str = '<a id="'+op_type+'_status_'+id+'" class="label label-danger" title="点击启用" onclick="opStatus('+id+', 1, \''+op_type+'\')">禁用</a>';
            }
            return str;
        }
        function opStatus(id, op_status, op_type) {
            var op_status_tips = op_status ? '启用':'禁用';
            var op_type_tips = op_type == 'push' ? '推送':'请求';
            var html='';
            if(confirm("您确定要 "+ op_status_tips +" 该客户的 "+ op_type_tips +" 模式吗？")) {
                $.ajax({
                    url: '/drawclients/edit',
                    dataType: "json",
                    method: "PUT",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: id,
                        op_type: op_type,
                        op_status: op_status
                    }
                }).done(function (json) {
                    if(json.status == 0) {
                        html = showStatus(id, op_status, op_type);
                        $("#"+op_type+"_status_"+id).parent().html(html);
                    } else {
                        alert("修改失败：".json.msg);
                    }
                }).fail(function () {
                    alert('修改失败');
                });
            }
        }
    </script>
@stop
