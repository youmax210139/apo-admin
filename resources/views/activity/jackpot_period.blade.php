@extends('layouts.base')
@section('title','幸运大奖池')
@section('function','幸运大奖池')
@section('function_link', '/activity/')
@section('here','幸运大奖池期号列表')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">期号</th>
                            <th class="hidden-sm">开奖号码</th>
                            <th class="hidden-sm">开奖状态</th>
                            <th class="hidden-sm">开始时间</th>
                            <th class="hidden-sm">结束时间</th>
                            <th class="hidden-sm">领取数量</th>
                            <th class="hidden-sm">销售总额</th>
                            <th class="hidden-sm">奖池(销售)</th>
                            <th class="hidden-sm">销售比例(%)</th>
                            <th class="hidden-sm">奖池(继承)</th>
                            <th class="hidden-sm">继承比例(%)</th>
                            <th class="hidden-sm">增减金额</th>
                            <th class="hidden-sm">奖池总额</th>
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
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                pageLength: 50,
                ordering: false,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax('?type=periods', null),
                "columns": [
                    {"data": "id"},
                    {"data": "period"},
                    {"data": "code"},
                    {"data": "code_status"},
                    {"data": "start_at"},
                    {"data": "end_at"},
                    {"data": "user_code_counter"},
                    {"data": "calculate_total_bet"},
                    {"data": "calculate_prize"},
                    {"data": "calculate_percent"},
                    {"data": "inheritance_prize"},
                    {"data": "inheritance_percent"},
                    {"data": "operation_prize"},
                    {"data": "prize_pool"},
                    {"data": "action"}
                ],

                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '';
                            var a_attr = null;
                            var common_class = 'X-Small btn-xs ';

                            a_attr = {
                                'class' : common_class + 'text-primary',
                                'href': '/activity/jackpot?type=no_prize&period='+ row.period
                            };
                            str += app.getalinkHtml('官方未开奖',a_attr, '');

                            a_attr = {
                                'class' : common_class + 'text-primary',
                                //'mounttabs':'',
                                'title':'幸运大奖池【'+ row.period +'】编辑增减金额',
                                'href': '/activity/jackpot?type=operation_prize&period='+ row.period
                            };
                            str += app.getalinkHtml('增减金额',a_attr, '');

                            a_attr = {
                                'class' : common_class + 'text-primary',
                                //'mounttabs':'',
                                'title':'幸运大奖池【'+ row.period +'】领取号码',
                                'href': '/activity/jackpot?type=user_code&period='+ row.period
                            };
                            str += app.getalinkHtml('领取号码',a_attr, '');

                            a_attr = {
                                'class' : common_class + 'text-primary postpone',
                                'period' : row.period,
                                'end_at' : row.end_at,
                                //'mounttabs':'',
                                'title':'【'+ row.period +'】延期一周',
                                'href': '#'
                            };
                            str += app.getalinkHtml('延期一周',a_attr, '');

                            a_attr = {
                                'class' : common_class + 'text-primary delete',
                                'period' : row.period,
                                //'mounttabs':'',
                                'title':'删除【'+ row.period +'】',
                                'href': '#'
                            };
                            str += app.getalinkHtml('删除',a_attr, '');

                            return str;
                        }
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            var str = '';
                            str = parseFloat(row.calculate_prize) + parseFloat(row.inheritance_prize) + parseFloat(row.operation_prize);
                            str = str.toFixed(4);
                            return str
                        }
                    },
                    {
                        'targets': 3,
                        'render': function (data, type, row) {
                            var str = '';
                            if(row.code_status == 0) {
                                str = '<span class="label label-default draw" period="' + row.period + '">未开奖</span>';
                            } else if (row.code_status == 1) {
                                str = '<span class="label label-success">已开奖</span>';
                            } else if (row.code_status == 2) {
                                str = '<span class="label label-warning">官方未开奖</span>';
                            } else {
                                str = row.code_status;
                            }
                            return str
                        }
                    },
                    {
                        'targets': 7,
                        'render': function (data, type, row) {
                            var str = '';
                            if(row.calculate_at == null) {
                                str = '<span title="未进行统计">' + row.calculate_total_bet + '</span>';
                            } else {
                                str = '<span title="最后统计时间：'+ row.calculate_at +'">' + row.calculate_total_bet + '</span>';
                            }

                            return str
                        }
                    },
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
            $("table").delegate('.delete', 'click', function () {
                var period = $(this).attr('period');
                BootstrapDialog.confirm({
                    title: '删除确认',
                    message: '您确认要删除【'+ period +'】吗？',
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    draggable: true, // <-- Default value is false
                    btnCancelLabel: '取消',
                    btnOKLabel : '确定',
                    btnOKClass : 'btn-warning',
                    callback: function (result) {
                        if (result) {
                            document.location.href = '/activity/jackpot?type=period_delete&period=' + period;
                        }
                    }
                });
            });
            $("table").delegate('.postpone', 'click', function () {
                var period = $(this).attr('period');
                var end_at = $(this).attr('end_at');
                BootstrapDialog.confirm({
                    title: '延期确认',
                    message: '您确认要【'+ period +'】延期一周吗？当前结束时间是 ' + end_at,
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    draggable: true, // <-- Default value is false
                    btnCancelLabel: '取消',
                    btnOKLabel : '确定',
                    btnOKClass : 'btn-warning',
                    callback: function (result) {
                        if (result) {
                            document.location.href = '/activity/jackpot?type=period_postpone&period=' + period;
                        }
                    }
                });
            });
            $("table").delegate('.draw', 'click', function () {
                var period = $(this).attr('period');
                BootstrapDialog.confirm({
                    title: '开奖确认',
                    message: '您确认【' + period + '】要开奖吗？',
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    draggable: true, // <-- Default value is false
                    btnCancelLabel: '取消',
                    btnOKLabel: '确定',
                    btnOKClass: 'btn-warning',
                    callback: function (result) {
                        if (result) {
                            document.location.href = '/activity/jackpot?type=period_draw&period=' + period;
                        }
                    }
                });
            });
        });
    </script>
@stop
