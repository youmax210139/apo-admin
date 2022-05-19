@extends('layouts.base')
@section('title','奖池管理')
@section('function','奖池管理')
@section('function_link', '/activity/')
@section('here','奖池列表')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">奖期</th>
                            <th class="hidden-sm">奖池比例(%)</th>
                            <th class="hidden-sm">奖金总额</th>
                            <th class="hidden-sm">人均奖金</th>
                            <th class="hidden-sm">状态</th>
                            <th class="hidden-sm">审核人/时间</th>
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

                    <h4 class="modal-title">奖金审核</h4>
                </div>
                <div class="modal-body ">
                    <table class="table table-bordered table-condensed" style="margin-bottom: 0">
                        <tbody>
                        <tr>
                            <th width="100" scope="row" class="text-right">奖期:</th>
                            <td><span id="modal_date"></span></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-right">用户列表:</th>
                            <td><span id="modal_userlist"></span></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-right">人均奖金:</th>
                            <td><span id="modal_prize"></span></td>
                        </tr>
                        <tr class="tr_status" style="display: none;">
                            <th scope="row" class="text-right">审核状态:</th>
                            <td><input type="radio" name="modal_status" value="1"> 通过<input type="radio" name="modal_status" value="2" style="margin-left:15px;"> 拒绝</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-primary deal_btn" style="display: none">确认审核</button>
                    <input type="hidden" id="modal_id" value="">
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
                order: [[0, "desc"]],
                serverSide: true,
                pageLength: 100,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null),
                "columns": [
                    {"data": "id"},
                    {"data": "date"},
                    {"data": "percent"},
                    {"data": "total_prize"},
                    {"data": "prize"},
                    {"data": "frozen"},
                    {"data": "verified_admin_user_id"},
                    {"data": "action"}
                ],

                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '';

                            if (row['frozen'] == 1 && row['status'] == 0) {
                                str = '<a show_btn=1 attr="' + row.id + '" userlist="' + row.userlist + '" prize="' + row.prize + '" date="' + row.date + '" href="javascript:;" class="btn-xs text-primary verify">审核</a>';
                            } else {
                                str = '<a show_btn=0 attr="' + row.id + '" userlist="' + row.userlist + '" prize="' + row.prize + '" date="' + row.date + '" href="javascript:;" class="btn-xs text-primary verify">查看</a>';
                            }

                            return str;
                        }
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            if (row['status'] == 0) {
                                return app.getLabelHtml('未审核', 'label-warning');
                            } else if (row['status'] == 1) {
                                return app.getLabelHtml(row['verified_admin'] + '(' + row['verified_at'] + ')已同意', 'label-success');
                            } else {
                                return app.getLabelHtml(row['verified_admin'] + '(' + row['verified_at'] + ')已拒绝', 'label-danger');
                            }
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            if (row['frozen'] == 0) {
                                return app.getLabelHtml('未完成', 'label-warning');
                            } else {
                                return app.getLabelHtml('已完成', 'label-success');
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

            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            //审核
            $("table").delegate('.verify', 'click', function () {
                var show_btn = $(this).attr('show_btn');
                if (show_btn == 1) {
                    $(".deal_btn").show();
                    $(".tr_status").show();
                } else {
                    $(".deal_btn").hide();
                    $(".tr_status").hide();
                }

                $("#modal_id").val($(this).attr('attr'));
                $("#modal_date").text($(this).attr('date'));
                $("#modal_userlist").text($(this).attr('userlist'));
                $("#modal_prize").text($(this).attr('prize'));
                $("#modal-verify").modal();
            });

            //确认审核
            $(".deal_btn").click(function () {
                var _this = $(this);
                var id = $("#modal_id").val()
                var status = $("#modal-verify :checked[name='modal_status']").val();

                if (status == undefined) {
                    app.bootoast("请选择审核状态！");
                    return false;
                }

                var putData = {
                    status: status
                };
                ;
                _this.prop("disabled", true);
                loadShow();
                $.ajax({
                    url: "/activity/PrizePoolVerify?id=" + id,
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