@extends('layouts.base')
@section('title','彩种管理')
@section('function','彩种管理')
@section('function_link', '/lottery/')
@section('here','彩种列表')
@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('lottery/refreshcron'))
                <a href="/lottery/refreshcron" class="btn btn-success btn-md">
                    <i class="fa fa-refresh"></i> 刷新计划任务[{{ $refresh_at }}]
                </a>
            @endif
            @if(Gate::check('lottery/create'))
                <a href="/lottery/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加彩种
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
                    <div>
                        <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/lottery/'" type="button" class="btn {{0==$mcid && 0==$special?'bg-primary':''}} " style="margin: 5px">全部
                        </button>
                        @foreach($method_category_rows as $v)
                            <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/lottery/?mcid={{$v->id}}'" type="button" class="btn {{$v->id==$mcid?'bg-primary':''}} "
                                    style="margin: 5px">{{$v->name}}</button>
                        @endforeach
                        <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/lottery/?special=1'" type="button" class="btn {{$special > 0?'bg-primary':''}} " style="margin: 5px">自开彩
                        </button>
                    </div>
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">ID</th>
                            <th class="hidden-sm">中文名称</th>
                            <th class="hidden-sm">英文标识</th>
                            <th class="hidden-sm">类型</th>
                            <th class="hidden-sm">开奖类型</th>
                            <th class="hidden-sm">销售状态</th>
                            <th class="hidden-sm" data-sortable="false">维护状态</th>
                            <th class="hidden-sm" data-sortable="false">简介</th>
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
                        确认要删除该彩种吗?
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

    <!--彩种开售或停售-->
    <div class="modal fade" id="modal-disable" tabIndex="-1">
        <div class="modal-dialog modal-info">
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
                        确认要<span class="row_isSell_text"></span>吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="disableForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
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
                language: app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                pageLength: 50,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(),
                "columns": [
                    {"data": "id"},
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "ident"},
                    {"data": "category_name"},
                    {"data": "special"},
                    {"data": "status"},
                    {"data": "maintenance"},
                    {"data": "introduce_status"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var row_edit = {{Gate::check('lottery/edit') ? 1 : 0}};
                            var row_issue = {{Gate::check('lottery/issue') ? 1 :0}};
                            var row_blockmethod = {{Gate::check('lottery/blockmethod') ? 1 :0}};
                            var row_prizelevel = {{Gate::check('lottery/prizelevel') ? 1 :0}};
                            var row_setStatus = {{Gate::check('lottery/setstatus') ? 1 :0}};
                            var row_isSell_class = row['status'] ? 'text-danger' : 'text-success';
                            var row_isSell_icon = row['status'] ? 'fa-lock' : 'fa-unlock';
                            var row_isSell_text = row['status'] ? '停售' : '开售';
                            var row_isMaintenance_class = row['maintenance'] == 0 ? 'text-danger' : 'text-success';
                            var row_isMaintenance_icon = row['maintenance'] == 0 ? 'fa-wrench' : 'fa-circle-o';
                            var row_isMaintenance_text = row['maintenance'] == 0 ? '设为维护' : '解除维护';

                            var str = '';

                            var a_attr = null;
                            var common_class = 'X-Small btn-xs ';

                            //编辑
                            if (row_edit) {
                                a_attr = {
                                    'class': common_class + 'text-primiry',
                                    'href': '/lottery/edit?id=' + row['id']
                                };
                                str += app.getalinkHtml('编辑', a_attr, 'fa-edit');
                            }

                            //奖期
                            if (row_issue) {
                                a_attr = {
                                    'class': common_class + 'text-success',
                                    'href': '/lottery/issue?lottery_id=' + row['id'],
                                    'title': row.name + '奖期管理',
                                    'mounttabs': ''
                                };
                                str += app.getalinkHtml('奖期', a_attr, 'fa-flag');
                            }
                            //禁用玩法
                            if (row_blockmethod) {
                                a_attr = {
                                    'class': common_class + 'text-danger',
                                    'href': '/lottery/blockmethod?id=' + row['id']
                                };
                                str += app.getalinkHtml('禁用玩法', a_attr, 'fa-lock');
                            }
                            //禁用玩法
                            if (row_prizelevel) {
                                a_attr = {
                                    'class': common_class + 'text-primiry',
                                    'mounttabs': '',
                                    'title': row['name'] + '玩法奖金',
                                    'href': '/lottery/prizelevel?id=' + row['id']
                                };
                                str += app.getalinkHtml('玩法奖金', a_attr, 'fa-gift');
                            }
                            //开售停售
                            if (row_setStatus) {
                                a_attr = {
                                    'class': common_class + ' disable is_sell ' + row_isSell_class,
                                    'id': row['id'],
                                    'status': row['status'] ? '1' : '0',
                                    'href': '#',
                                    'isdisabled': row_isSell_text
                                };
                                str += app.getalinkHtml(row_isSell_text, a_attr, row_isSell_icon);
                            }

                            //维护状态
                            if (row_setStatus) {
                                a_attr = {
                                    'class': common_class + ' disable is_maintenance ' + row_isMaintenance_class,
                                    'id': row['id'],
                                    'maintenance': row['maintenance'],
                                    'title': row['maintenance'] ? '允许正式组、试玩组投注' : '禁止正式组、试玩组投注',
                                    'href': '#',
                                    'isdisabled': row_isMaintenance_text + (row['maintenance'] ? '（允许正式组、试玩组投注）' : '（禁止正式组、试玩组投注）')
                                };
                                str += app.getalinkHtml(row_isMaintenance_text, a_attr, row_isMaintenance_icon);
                            }

                            return str;
                        },
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(
                                row['introduce_status'] == 0 ? '未设置' : (row['introduce_status'] == 1 ? '正常' : '禁用'),
                                'label-' + (row['introduce_status'] == 0 ? 'primary' : (row['introduce_status'] == 1 ? 'success' : 'danger'))
                            );
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(
                                row['maintenance'] == 0 ? '正常' : '维护',
                                'label-' + (row['maintenance'] == 0 ? 'success' : 'danger')
                            );
                        }
                    },
                    {
                        'targets': -4,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(
                                row['status'] ? '正常' : '停售',
                                'label-' + (row['status'] ? 'success' : 'danger')
                            );
                        }
                    },
                    {
                        'targets': -5,
                        'render': function (data, type, row) {
                            return row['special'] == 0 ? '<span class="text-green">官方</span>' : (row['special'] == 1 ? '<span class="text-red">自开彩种</span>' : '<span class="text-yellow">自开秒秒彩</span>');
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

            //开售，停售
            $("table").delegate('.is_sell', 'click', function () {
                var id = $(this).attr('id');
                var status = $(this).attr('status') == '1' ? '0' : '1';
                $(".row_isSell_text").text($(this).attr('isdisabled'));
                $('.disableForm').attr('action', '/lottery/setstatus?id=' + id + "&status=" + status);
                $("#modal-disable").modal();
            });

            //维护状态
            $("table").delegate('.is_maintenance', 'click', function () {
                var id = $(this).attr('id');
                var maintenance = $(this).attr('maintenance') == '1' ? '0' : '1';
                $(".row_isSell_text").text($(this).attr('isdisabled'));
                $('.disableForm').attr('action', '/lottery/setstatus?id=' + id + "&maintenance=" + maintenance + "&action=maintenance");
                $("#modal-disable").modal();
            });
        });
    </script>
@stop
