@extends('layouts.base')
@section('title','管理员管理')
@section('function','管理员管理')
@section('function_link', '/admin/')
@section('here','管理员列表')
@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('admin/create'))
                <a href="/admin/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加管理员
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
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form id="search" class="form-horizontal" action="/admin/" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

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
                            <div class="form-group search_username">
                                <label class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username" placeholder="用户名">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">角色</label>
                                <div class="col-sm-9">
                                    <select name="role_id" class="form-control">
                                        <option value="0">角色列表</option>
                                        @foreach($role_list as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">IP 地址</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="ip" placeholder="用户 IP 地址">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->

            <div class="box box-primary">

                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">用户昵称</th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">角色</th>
                            <th class="hidden-sm">添加时间</th>
                            <th class="hidden-sm">最近登录时间</th>
                            <th class="hidden-sm">最近登录IP</th>
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
                        确认要删除这个用户吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-common" tabIndex="-1">
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
                        <span id="modal-text"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="commonForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
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
            var get_params = function (data) {
                var param = {
                    'ip': $("input[name='ip']").val(),
                    'username': $("input[name='username']").val(),
                    'role_id': $('select[name="role_id"]').val(),
                };

                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "usernick"},
                    {"data": "username"},
                    {"data": "role_name"},
                    {"data": "created_at"},
                    {"data": "last_time"},
                    {"data": "last_ip"},
                    {"data": "action"}
                ],
                createdRow: function (row, data, index) {
                    if (data['is_locked']) {
                        $(row).addClass('danger');
                    }
                },
                columnDefs: [
                    {
                        'targets': -1, "render": function (data, type, row) {
                            var row_edit = {{Gate::check('admin/edit') ? 1 : 0}};
                            var row_lock = {{Gate::check('admin/lock') ? 1 :0}};
                            var row_google_key = {{Gate::check('admin/googlekey') ? 1 :0}};
                            var row_delete = {{Gate::check('admin/delete') ? 1 :0}};
                            var str = '';

                            //编辑
                            if (row_edit) {
                                str += '<a style="margin:3px;" href="/admin/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                            }
                            //谷歌
                            if (row_google_key) {
                                if (row['google_key']) {
                                    str += '<a style="margin:3px;"  href="javascript:;" attr="' + row['id'] + '" class="googleBtn X-Small btn-xs text-danger "><i class="fa fa-google"></i>解绑登录器</a>';
                                }
                            }
                            //冻结
                            if (row_lock) {
                                if (row['is_locked']) {
                                    str += '<a style="margin:3px;" href="javascript:;" attr=' + row['id'] + '" class="lockBtn X-Small btn-xs text-danger "><i class="fa fa-lock"></i> 解冻</a>';
                                } else {
                                    str += '<a style="margin:3px;" href="javascript:;" attr="' + row['id'] + '" class="lockBtn X-Small btn-xs text-success "><i class="fa fa-unlock"></i> 冻结</a>';

                                }
                            }
                            //删除
                            if (row_delete) {
                                str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
                            }

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

            $('#search_btn').click(function (event) {
                event.preventDefault();
                table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            $("table").delegate('.delBtn', 'click', function () {
                var id = $(this).attr('attr');
                $('.deleteForm').attr('action', '/admin/?id=' + id);
                $("#modal-delete").modal();
            });
            $("table").delegate('.lockBtn', 'click', function () {
                var id = $(this).attr('attr');
                $('.commonForm').attr('action', '/admin/lock?id=' + id);
                $("#modal-text").html("确定要[" + $(this).text() + "]该管理员？");
                $("#modal-common").modal();
            });
            $("table").delegate('.googleBtn', 'click', function () {
                var id = $(this).attr('attr');
                $('.commonForm').attr('action', '/admin/googlekey?id=' + id);
                $("#modal-text").html("确定要解绑该管理员谷歌登录器？");
                $("#modal-common").modal();
            });
        });
    </script>
@stop
