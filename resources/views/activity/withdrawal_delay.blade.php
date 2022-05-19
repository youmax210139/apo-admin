@extends('layouts.base')
@section('title','提款补偿金审核')
@section('function','提款补偿金审核')
@section('function_link', '/activity/')
@section('here','补偿金列表')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form id="search" class="form-horizontal" action="/activity/" method="post">
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
                                    <input type="text" class="form-control" name="username" autocomplete="off" placeholder="用户名">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">创建时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" placeholder="开始时间" autocomplete="off"
                                               id='start_date' value="{{$start_date}}">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" placeholder="结束时间" autocomplete="off"
                                               id='end_date' value="{{$end_date}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="">请选择</option>
                                        <option value="0">未发放</option>
                                        <option value="2">已发放</option>
                                        <option value="1">已拒绝</option>
                                    </select>
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
                            <th data-sortable="false" class="hidden-sm">用户名</th>
                            <th data-sortable="false" class="hidden-sm">提款单ID</th>
                            <th data-sortable="false" class="hidden-sm">申请时间</th>
                            <th data-sortable="false" class="hidden-sm">出款时间</th>
                            <th data-sortable="false" class="hidden-sm">提款金额</th>
                            <th data-sortable="false" class="hidden-sm">延迟分钟数</th>
                            <th data-sortable="false" class="hidden-sm">发放比例(%)</th>
                            <th data-sortable="false" class="hidden-sm">奖金</th>
                            <th data-sortable="false" class="hidden-sm">创建时间</th>
                            <th data-sortable="false" class="hidden-sm">状态</th>
                            <th data-sortable="false" class="hidden-sm">审核人/时间</th>
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
    <div class="modal fade modal-default" id="modal-verify" tabIndex="-1">
        <div class="modal-dialog modal-default modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">活动审核</h4>
                </div>
                <div class="modal-body ">
                    <table class="table table-bordered table-condensed" style="margin-bottom: 0">
                        <tbody>
                        <tr>
                            <th scope="row" class="text-right">用户名:</th>
                            <td><span id="modal_username"></span></td>
                            <th scope="row" class="text-right">发放比例(%):</th>
                            <td><span id="modal_percent"></span></td>
                            <th scope="row" class="text-right">奖金:</th>
                            <td><span id="modal_prize"></span></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-right">审核结果:</th>
                            <td colspan="3">
                                <label class="radio-inline" style="font-size: 22px; color: green">
                                    <input type="radio" name="modal_status" value="2"> 通过
                                </label>
                                <label class="radio-inline" style="font-size: 22px; color: red">
                                    <input type="radio" name="modal_status" value="1"> 拒绝
                                </label></td>
                        </tr>
                        <tr class="deal_action">
                            <th scope="row" class="text-right">备注:</th>
                            <td colspan="3">
                                <textarea class="form-control" style="resize:vertical" placeholder="请填写备注" id="modal_comment"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer deal_action" style="text-align: center">
                    <button type="button" class="btn btn-primary deal_btn">确认审核</button>
                    <input type="hidden" id="modal_id" value="">
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
                    'status': $('select[name="status"]').val(),
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
                    {"data": "username"},
                    {"data": "withdrawal_id"},
                    {"data": "apply_at"},
                    {"data": "done_at"},
                    {"data": "amount"},
                    {"data": "delay_minutes"},
                    {"data": "percent"},
                    {"data": "prize"},
                    {"data": "created_at"},
                    {"data": "status"},
                    {"data": "verified_admin"},
                    {"data": "action"}
                ],

                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '';
                            var row_verify = {{ Gate::check('activity/withdrawaldelayverify') ? 1 : 0 }};

                            if (row_verify && row.status == 0) {
                                str = '<a attr="' + row.id + '" username="' + row.username + '" percent="' + row.percent + '" prize="' + row.prize + '" href="javascript:;" class="btn-xs text-primary verify">审核</a>';
                            } else if (row.comment != '') {
                                str = '<a attr="' + row.id + '" href="javascript:;" class="btn-xs text-success comment" onclick="alert(\'' + row.comment + '\');">备注</a>';
                            }
                            return str;
                        }
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            return row['verified_admin'] ? row['verified_admin'] + ' / ' + row['verified_at'] : '未审核';
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            if (row['status'] == 0) {
                                return app.getLabelHtml('未发放', 'label-warning');
                            } else if (row['status'] == 1) {
                                return app.getLabelHtml('已拒绝', 'label-danger');
                            } else {
                                return app.getLabelHtml('已发放', 'label-success');
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

            $('#search_btn').click(function (event) {
                event.preventDefault();
                table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
            //审核
            $("table").delegate('.verify', 'click', function () {
                $("#modal_id").val($(this).attr('attr'));
                $("#modal_username").text($(this).attr('username'));
                $("#modal_percent").text($(this).attr('percent'));
                $("#modal_prize").text($(this).attr('prize'));
                $('#modal-verify input[type="radio"]').attr('checked', false);
                $("#modal_comment").val('');
                $("#modal-verify").modal();
            });
            //确认审核
            $(".deal_btn").click(function () {
                var _this = $(this);
                var id = $("#modal_id").val()
                var status = $("#modal-verify :checked[name='modal_status']").val();
                var comment = $("#modal_comment").val();

                if (status == undefined) {
                    app.bootoast("请选择审核结果！");
                    return false;
                }
                if (status == 1 && comment == '') {
                    app.bootoast("请输入备注");
                    return false;
                }

                var putData = {
                    id: id,
                    status: status,
                    comment: comment,
                };
                ;
                _this.prop("disabled", true);
                loadShow();
                $.ajax({
                    url: "/activity/withdrawaldelayverify?id=" + id,
                    dataType: "json",
                    method: "PUT",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: putData
                }).done(function (json) {
                    _this.prop("disabled", false);
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        bootoast({
                            message: json.msg,
                            type: 'success',
                            position: 'top-center',
                            timeout: 5,
                            animationDuration: 300,
                            dismissable: true
                        });
                        table.ajax.reload();
                        $('#modal-verify').modal('hide');
                    } else {
                        bootoast({
                            message: json.msg,
                            type: 'danger',
                            position: 'top-center',
                            timeout: 8,
                            animationDuration: 300,
                            dismissable: true
                        });
                    }
                });
            });
        });
    </script>
@stop
