@extends('layouts.base')

@section('title','账号反查')
@section('function','账号反查')
@section('function_link', '/userquery/')
@section('here','账号反查列表')

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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="account" class="col-sm-3 control-label">银行账号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='account' autocomplete="off" placeholder="银行账号"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="account_name" class="col-sm-3 control-label">银行开户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='account_name' autocomplete="off" placeholder="银行开户名"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="qq" class="col-sm-3 control-label">QQ</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='qq' autocomplete="off" placeholder="QQ"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="telephone" class="col-sm-3 control-label">联系电话</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='telephone' autocomplete="off" placeholder="联系电话"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weixin" class="col-sm-3 col-sm-3 control-label">微信</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='weixin' autocomplete="off" placeholder="微信"/>
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
                            <button type="reset" class="btn btn-default col-sm-2"></i>重置</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-body">
            <table id="tags-table" class="table table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th data-sortable="false">用户名</th>
                    <th data-sortable="false">所属总代</th>
                    <th data-sortable="false">银行</th>
                    <th data-sortable="false">卡号</th>
                    <th data-sortable="false">省份/城市</th>
                    <th data-sortable="false">是否冻结</th>
                    <th data-sortable="false">冻结类型</th>
                    <th data-sortable="false">是否测试账户</th>
                    <th data-sortable="false">QQ</th>
                    <th data-sortable="false">联系电话</th>
                    <th data-sortable="false">微信</th>
                    <th data-sortable="false">最后修改时间</th>
                    <th data-sortable="false">添加时间</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        $(function () {
            var get_params = function (data) {
                var param = {
                    'account': $("input[name='account']").val(),
                    'account_name': $("input[name='account_name']").val(),
                    'qq': $("input[name='qq']").val(),
                    'telephone': $("input[name='telephone']").val(),
                    'weixin': $("input[name='weixin']").val(),
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
                    {"data": "username"},
                    {"data": "top_username"},
                    {"data": "bank_name"},
                    {"data": "bank_account"},
                    {"data": "province"},
                    {"data": "frozen"},
                    {"data": "frozen"},
                    {"data": "user_group_id"},
                    {"data": "qq"},
                    {"data": "telephone"},
                    {"data": "weixin"},
                    {"data": "updated_at"},
                    {"data": "created_at"},
                ],
                columnDefs: [
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
                        'targets': 4,
                        'render': function (data, type, row) {
                            return row['province'] ? row['province'] + '/' + row['city'] : '';
                        }
                    },
                    {
                        'targets': 5,
                        'render': function (data, type, row) {
                            return row['frozen'] ? '是' : '否';
                        }
                    },
                    {
                        'targets': 6,
                        'render': function (data, type, row) {
                            var rtn;
                            switch (row['frozen']) {
                                case 1:
                                    rtn = '完全冻结';
                                    break;
                                case 2:
                                    rtn = '可登录，不可投注，不可充提';
                                    break;
                                case 3:
                                    rtn = '不可投注，可充提';
                                    break;
                                default:
                                    rtn = '否';
                            }
                            return rtn;
                        }
                    },
                    {
                        'targets': 7,
                        'render': function (data, type, row) {
                            return row['user_group_id'] == 2 ? '是' : '否';
                        }
                    },
                ],
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
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
        });
    </script>
@stop