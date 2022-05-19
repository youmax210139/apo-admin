@extends('layouts.base')
@section('title','盈亏报表')
@section('function','盈亏报表')
@section('function_link', '/thirdgameprofitloss/')
@section('here','盈亏报表')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" action="/thirdgameprofitloss/" method="post">
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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="platform" class="col-sm-3 control-label">第三方</label>
                                    <div class="col-sm-9">
                                        <select name="platform" id="platform" class="form-control">
                                            <option value="all">请选择</option>
                                            @foreach($platforms as $platform)
                                                <option value="{{ $platform['ident'] }}">{{ $platform['name'] }}
                                                    [ {{ $platform['ident'] }} ]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date" class="col-sm-3 control-label">投注时间</label>
                                    <div class="col-sm-9">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control form_datetime" name="start_date"
                                                   id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                            <span class="input-group-addon">~</span>
                                            <input type="text" class="form-control form_datetime" name="end_date"
                                                   id='end_date' value="{{$end_date}}" placeholder="结束时间">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="username" class="col-sm-3 control-label">用户</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name='username' placeholder="用户名"/>
                                        <input type="hidden" name='get_parent' value="0"/>
                                        <input type="hidden" name='id' value="{{ $id }}"/>
                                        <input type="hidden" name="is_search" value="0"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_group_id" class="col-sm-3 control-label">组别</label>
                                    <div class="col-sm-9">
                                        <select name="user_group_id" id="user_group_id" class="form-control">
                                            <option value="">所有组别</option>
                                            @foreach($user_group as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name='id' value="{{ $id }}"/>
                                <input type="hidden" name="is_search" value="0"/>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">显示零结算用户</label>
                                    <div class="col-sm-9">
                                        <select name="show_zero" class="form-control">
                                            <option value="1">是</option>
                                            <option value="0" selected="selected">否</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name='id' value="{{ $id }}"/>
                                <input type="hidden" name="is_search" value="0"/>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="submit" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                        <button type="submit" class="btn btn-warning margin"><i class="fa fa-download" aria-hidden="true"></i>导出</button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->

            <div class="box box-primary">
                <style>
                    .total {
                        font-weight: bold;
                    }

                    .total > td:first-child {
                        text-align: right;
                    }
                </style>
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct">
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
                    'show_zero': $("select[name='show_zero']").val(),
                    'get_parent': $("input[name='get_parent']").val(),
                    'username': $("input[name='username']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'platform': $("select[name='platform']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'is_search': $("input[name='is_search']").val(),
                    'id': $("input[name='id']").val()
                };
                return $.extend({}, data, param);
            };

            function colorRender(data, type, row) {
                var color = data >= 0 ? '#12ca12' : 'red';
                return "<span style='color:" + color + ";'>" + data + "</span>";
            }

            var columns = {
                default: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "real_win", "title": "最终结算", "render": colorRender},
                    {"data": "user_id", "title": "操作"}
                ],
                ky: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "real_win", "title": "最终结算 [平台盈亏-返水]", "render": colorRender},
                    {"data": "chou_shui", "title": "系统抽水"},
                    {"data": "user_id", "title": "操作"}
                ],
                vgqp: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "real_win", "title": "最终结算 [平台盈亏-返水]", "render": colorRender},
                    {"data": "chou_shui", "title": "系统抽水"},
                    {"data": "user_id", "title": "操作"}
                ],
                lcqp: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "real_win", "title": "最终结算 [平台盈亏-返水]", "render": colorRender},
                    {"data": "chou_shui", "title": "系统抽水"},
                    {"data": "user_id", "title": "操作"}
                ],
                lgqp: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "real_win", "title": "最终结算 [平台盈亏-返水]", "render": colorRender},
                    {"data": "chou_shui", "title": "系统抽水"},
                    {"data": "user_id", "title": "操作"}
                ],
                ggqipai: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "real_win", "title": "最终结算 [平台盈亏-返水]", "render": colorRender},
                    {"data": "chou_shui", "title": "系统抽水"},
                    {"data": "user_id", "title": "操作"}
                ],
                vr: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "ds", "title": "打赏"},
                    {"data": "real_win", "title": "最终结算", "render": colorRender},
                    {"data": "user_id", "title": "操作"}
                ],
                fhleli: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "ds", "title": "打赏"},
                    {"data": "real_win", "title": "最终结算", "render": colorRender},
                    {"data": "user_id", "title": "操作"}
                ],
                wml: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "ds", "title": "打赏"},
                    {"data": "real_win", "title": "最终结算", "render": colorRender},
                    {"data": "user_id", "title": "操作"}
                ],
                leg: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "real_win", "title": "最终结算 [平台盈亏-返水]", "render": colorRender},
                    {"data": "chou_shui", "title": "系统抽水"},
                    {"data": "user_id", "title": "操作"}
                ],
                all: [
                    {"data": "username", "title": "用户名"},
                    {"data": "bet", "title": "总投注"},
                    {"data": "user_win", "title": "中奖金额"},
                    {"data": "admin_deduct", "title": "管理员扣减"},
                    {"data": "win", "title": "平台盈亏", "render": colorRender},
                    {"data": "fd", "title": "返水"},
                    {"data": "ds", "title": "打赏"},
                    {"data": "chou_shui", "title": "抽水"},
                    {"data": "real_win", "title": "最终结算", "render": colorRender},
                    {"data": "user_id", "title": "操作"}
                ]

            };
            var columnDefs = [
                {
                    'targets': 0,
                    'render': function (data, type, row) {
                        if (row['user_observe']) {
                            return app.getColorHtml(row.username, 'label-danger', false);
                        } else {
                            return row.username;
                        }
                    }
                },
                {
                    'targets': -1,
                    'orderable': false,
                    'render': function (data, type, row) {
                        var current_user = $("input[name='username']").val();
                        var str = '';
                        str = data > 0 && current_user != row.username ? '<a href="javascript:void(0);" username="' + row.username + '" >查看下级</a>' : '';
                        return str;
                    }
                }
            ];
            var createdRow = function (row, data, dataIndex) {
                if (data.username === '合计') {
                    $(row).addClass('total').addClass('no-sort').removeAttr("role");
                }
            };

            function response(data) {
                if (!data) {
                    return;
                }

                if (typeof data.total !== 'undefined') {
                    var column = getColum();
                    var row = {};
                    var name = '';
                    for (var i = 0; i < column.length; i++) {
                        name = column[i].data;
                        if (typeof data.total[name] !== 'undefined') {
                            row[name] = data.total[name];
                        } else {
                            row[name] = '';
                        }
                    }
                    row["username"] = "合计";
                    data.data.push(row);
                }
            }

            var setting = {
                language: app.DataTable.language(),
                dom: 'ftip',
                order: [],
                pageLength: 50,
                searching: false,
                ajax: app.DataTable.ajax(null, null, get_params, response),
                columns: columns.all,
                columnDefs: columnDefs,
                createdRow: createdRow,

            };
            var table = $("#tags-table").DataTable(setting);

            function getColum() {
                var platform = $('#platform').val().toLowerCase();
                var column = [];
                if (platform && typeof columns[platform] !== 'undefined') {
                    column = columns[platform];
                } else {
                    column = columns.default;
                }
                return column;
            }

            function research(param) {
                if (typeof param.username !== "undefined") {
                    $("input[name='username']").val(param.username);
                }
                $('#search_btn').click();
            }

            $('#platform').bind('change', function () {
                var column = getColum();
                var _setting = {
                    language: app.DataTable.language(),
                    dom: 'ftip',
                    order: [],
                    pageLength: 50,
                    searching: true,
                    ajax: app.DataTable.ajax(null, null, get_params, response),
                    columns: column,
                    columnDefs: columnDefs,
                    createdRow: createdRow,
                };
                table.destroy();
                table = $("#tags-table").empty().DataTable(_setting);
            });
            $('#tags-table').on('click', 'a[username]', function () {
                research({username: $(this).attr('username')});
            });

            $('#search_btn').click(function (event) {
                event.preventDefault();
                table.ajax.reload();
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
        });
    </script>
@stop