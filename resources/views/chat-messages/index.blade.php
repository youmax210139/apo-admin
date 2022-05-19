@extends('layouts.base')
@section('title','上下级聊天查询')
@section('function','上下级聊天查询')
@section('function_link', '/chatmessage/')
@section('here','上下级聊天查询')

@section('content')
<div class="row">
<div class="col-sm-12">
    <div class="box box-primary">
        <form id="search" class="form-horizontal" action="/message/" method="post">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">发送时间</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="start_date" value="" id='start_date' placeholder="开始时间">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="end_date" value="" id='end_date' placeholder="结束时间">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group search_username">
                        <label class="col-sm-4 control-label">发件人</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="sender" placeholder="发件人">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group search_username">
                        <label class="col-sm-4 control-label">收件人</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="receiver" placeholder="收件人">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group search_username">
                        <label class="col-sm-3 control-label">消息</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="message" placeholder="消息模糊查询">
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer text-center">
                <button type="submit" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                @if(Gate::check('chatmessage/delete'))
                    <button type="button" class="btn btn-default margin" id="batch_delete">
                        <i class="fa fa-times-circle" aria-hidden="true"></i>批量删除
                    </button>
                @endif
            </div>
        </form>
    </div>
    <div class="box box-primary">
        @include('partials.errors')
        @include('partials.success')
        <div class="box-body">
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th data-sortable="false" class="hidden-sm">ID</th>
                    <th data-sortable="false">发件人</th>
                    <th data-sortable="false">收件人</th>
                    <th data-sortable="false">消息</th>
                    <th data-sortable="false">发送时间</th>
                    <th data-sortable="false">删除时间</th>
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
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Select/css/select.dataTables.min.css"/>
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css"/>
    <script src="/assets/plugins/datatables/extensions/Select/js/dataTables.select.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js" charset="UTF-8"></script>
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
                        'sender': $("input[name='sender']").val(),
                        'start_date': $("input[name='start_date']").val(),
                        'end_date': $("input[name='end_date']").val(),
                        'receiver': $("input[name='receiver']").val(),
                        'message': $("input[name='message']").val(),
                    };
                    return $.extend({}, data, param);
                }
                var table = $("#tags-table").DataTable({
                    language: app.DataTable.language(),
                    order: [[0, "desc"]],
                    serverSide: true,
                    pageLength: 50,
                    searching: false,
                    // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                    // 要 ajax(url, type) 必须加这两参数
                    ajax: app.DataTable.ajax(null, null, get_params),
                    "columns": [
                        {"data": "id"},
                        {"data": "sender"},
                        {"data": "receiver"},
                        {"data": "message"},
                        {"data": "created_at"},
                        {"data": "deleted_at"}
                    ],
                    columnDefs: [
                    ],
                    @if(Gate::check('chatmessage/delete'))
                    dom: 'Blfrtip',
                    select: {
                        style: 'multi'
                    },
                    buttons: [
                        {
                            text: '全选',
                            action: function () {
                                if (table.rows({selected: true}).count() == table.rows().count()) {
                                    table.rows().deselect();
                                } else {
                                    table.rows().select();
                                }
                            }
                        }
                    ]
                    @endif
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
                $('#search_btn').click(function () {
                    event.preventDefault();
                    table.ajax.reload();
                });

                //批量删除
                $("#batch_delete").bind('click', function () {
                    var select_rows = table.rows({selected: true});
                    var id_array = select_rows.data().pluck('id').toArray();
                    if (id_array.length == 0) {
                        return false;
                    }

                    BootstrapDialog.confirm({
                        title: '删除确认',
                        message: '要批量删除选中的 ' + id_array.length + ' 条记录吗？',
                        type: BootstrapDialog.TYPE_WARNING,
                        closable: true,
                        draggable: true,
                        btnCancelLabel: '取消',
                        btnOKLabel: '确定',
                        btnOKClass: 'btn-warning',
                        callback: function (result) {
                            if (result) {
                                document.location.href = '/chatmessage/delete?ids=' + id_array.join(',');
                            }
                        }
                    });
                });
            });
        </script>
@stop