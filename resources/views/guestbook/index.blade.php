@extends('layouts.base')
@section('title','留言管理')
@section('function','留言管理')
@section('function_link', '/guestbook/')
@section('here','留言列表')
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
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="title" class="col-sm-3 col-sm-3 control-label">主题</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='title' placeholder="主题"/>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="created_date" class="col-sm-3 control-label">添加时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="created_start_date"
                                               id='created_start_date' value="" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="created_end_date"
                                               id='created_end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>

                    <div class="box-footer">
                        <div class="btn-group col-md-6">
                            <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i
                                        class="fa fa-search" aria-hidden="true"></i>查询
                            </button>
                        </div>
                        <div class=" btn-group col-md-6">
                            <button type="reset" class="btn btn-default col-sm-2"><i class="fa fa-refresh"
                                                                                     aria-hidden="true"></i>重置
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">称呼</th>
                            <th class="hidden-sm">通讯软件</th>
                            <th class="hidden-sm">通讯软件帐号</th>
                            <th class="hidden-sm">邮箱</th>
                            <th class="hidden-sm">银行卡姓名</th>
                            <th class="hidden-sm">主题</th>
                            <th class="hidden-sm">创建时间</th>
                            <th class="hidden-sm">状态</th>
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
    <script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#created_start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
        };
        laydate(layConfig);

        layConfig.elem = '#created_end_date';
        laydate(layConfig);
        $(function () {
            var get_params = function (data) {
                var param = {
                    'created_start_date': $("input[name='created_start_date']").val(),
                    'created_end_date': $("input[name='created_end_date']").val(),
                    'title': $("input[name='title']").val(),
                };
                return $.extend({}, data, param);
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                iDisplayLength: 25,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                searching: false,
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "appellation"},
                    {"data": "app_name"},
                    {"data": "app_account"},
                    {"data": "email"},
                    {"data": "account_name"},
                    {"data": "title"},
                    {"data": "created_at"},
                    {"data": "status"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            if (row['status'] == 1) {
                                return '不需处理';
                            } else if (row['status'] == 2) {
                                return '已处理';
                            } else {
                                return '待处理';
                            }
                        }
                    },
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var row_edit = {{ Gate::check('guestbook/edit') ? 1 : 0 }};
                            var row_detail = {{ Gate::check('guestbook/detail') ? 1 : 0 }};

                            var str = '';

                            var a_attr = null;
                            var common_class = 'X-Small btn-xs ';

                            //详情
                            if (row_detail) {
                                a_attr = {
                                    'class': common_class + 'text-success',
                                    'href': '/guestbook/detail?id=' + row['id']
                                };
                                str += app.getalinkHtml('详情', a_attr, 'fa-list');
                            }

                            //编辑
                            if (row_edit) {
                                a_attr = {
                                    'class': common_class + 'text-success',
                                    'href': '/guestbook/edit?id=' + row['id']
                                };
                                str += app.getalinkHtml('编辑', a_attr, 'fa-edit');
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
            $('#search').submit(function (event) {
                event.preventDefault();
                table.ajax.reload();
            });
        });
    </script>
@stop
