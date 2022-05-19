@extends('layouts.base')

@section('title','开奖管理')

@section('function','开奖管理')
@section('function_link', '/errorissue/')

@section('here','开奖异常')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <style>
        table.dataTable.table-condensed thead > tr > th {
            text-align: center;
            vertical-align: middle;
        }

        table.dataTable.table-condensed tbody > tr > td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">

        </div>
    </div>
    @include('partials.errors')
    @include('partials.success')

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">

                <div class="box-body">
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info"></i> 提示!</h4>
                        1, 官方提前开奖 [ 撤销派奖+系统撤单 ];
                        2, 录入号码错误 [ 撤销派奖+重新判断中奖+重新派奖 ];
                        3, 官方未开奖
                    </div>
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>

                            <th data-sortable="false">编号</th>
                            <th data-sortable="false">提交时间</th>
                            <th data-sortable="false">彩种</th>
                            <th data-sortable="false">奖期</th>
                            <th data-sortable="false">类型</th>

                            <th data-sortable="false">管理员</th>

                            <th data-sortable="false">异常信息</th>
                            <th data-sortable="false">旧验证</th>
                            <th data-sortable="false">旧扣款</th>
                            <th data-sortable="false">旧返点</th>
                            <th data-sortable="false">旧判奖</th>
                            <th data-sortable="false">旧派奖</th>
                            <th data-sortable="false">旧追号</th>
                            <th data-sortable="false">撤派</th>
                            <th data-sortable="false">撤单</th>
                            <th data-sortable="false">操作</th>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
                        确认要<span class="row_isSell_text">录入号码</span>吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="disableForm" method="POST" action="/draw/EnterCode">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">

                        <input type="hidden" name="code" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-info">
                            确认
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
                    {"data": "write_time"},
                    {"data": "lottery_name"},
                    {"data": "issue"},
                    {"data": "error_type"},

                    {"data": "write_admin"},

                    {"data": "action"},

                    {"data": "old_code_status"},
                    {"data": "old_deduct_status"},
                    {"data": "old_rebate_status"},
                    {"data": "old_checkbonus_status"},
                    {"data": "old_bonus_status"},
                    {"data": "old_tasktoproject_status"},
                    {"data": "cancel_bonus_status"},
                    {"data": "repeal_status"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': 2, "render": function (data, type, row) {
                            var str = row.lottery_name + ' <span class="text-gray text-bold">'+ row.lottery_ident +'</span>';
                            return str;
                        }
                    },
                    {
                        'targets': -1, "render": function (data, type, row) {
                            var row_delete = {{Gate::check('errorissue/reset') ? 1 :0}};
                            var str = '';

                            if (row_delete) {
                                str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-success"><i class="fa fa-refresh"></i> 重置</a>';
                            }

                            return str;

                        }
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['repeal_status']]==undefined?'忽略': status_text[row['repeal_status']],
                                'text-' + (status_css[row['repeal_status']]==undefined?'green': status_css[row['repeal_status']])
                            );
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['cancel_bonus_status']]==undefined?'忽略': status_text[row['cancel_bonus_status']],
                                'text-' + (status_css[row['cancel_bonus_status']]==undefined?'green': status_css[row['cancel_bonus_status']])
                            );
                        }
                    },
                    {
                        'targets': -4,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['old_tasktoproject_status']],
                                'text-' + status_css[row['old_tasktoproject_status']]
                            );
                        }
                    },
                    {
                        'targets': -5,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['old_bonus_status']],
                                'text-' + status_css[row['old_bonus_status']]
                            );
                        }
                    },
                    {
                        'targets': -6,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['old_checkbonus_status']],
                                'text-' + status_css[row['old_checkbonus_status']]
                            );
                        }
                    },
                    {
                        'targets': -7,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['old_rebate_status']],
                                'text-' + status_css[row['old_rebate_status']]
                            );
                        }
                    },
                    {
                        'targets': -8,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['old_deduct_status']],
                                'text-' + status_css[row['old_deduct_status']]
                            );
                        }
                    },
                    {
                        'targets': -9,
                        'render': function (data, type, row) {
                            var status_text = ['未开始', '进行中', '已完成'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[row['old_code_status']],
                                'text-' + status_css[row['old_code_status']]
                            );
                        }
                    },
                    {
                        'targets': -10,
                        'render': function (data, type, row) {
                           if(row.error_type==1){
                               return '实际开奖时间['+row.open_time+']';
                           }
                            if(row.error_type==2){
                                return '[<span class="text-red">'+row.old_code+'</span>] -> [<span class="text-red">'+row.code+'</span>]';
                            }
                            return '----'
                        }
                    },
                    {
                        'targets': 1,
                        'render': function (data, type, row) {

                            return data;
                        }
                    },
                    {
                        'targets': 4,
                        'render': function (data, type, row) {
                            var status_text = ['','提前开奖', '号码录错', '官方未开奖'];
                            var status_css = ['','orange', 'red', 'maroon'];
                            return app.getLabelHtml(
                                status_text[row['error_type']],
                                'text-' + status_css[row['error_type']]
                            );
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
                $(".delBtn").click(function () {
                    var id = $(this).attr('attr');
                    BootstrapDialog.confirm('确定要重置异常状态吗?', function(result){
                        if(result) {
                            $.ajax({
                                url: '/errorissue/reset',
                                dataType: "json",
                                method: "POST",
                                data:{id:id},
                            }).done(function (json) {
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    });
                });
            });


        });
        //开售，停售
        $("#enter_code").click(function () {
            $("input[name='code']").val($("#code").val());
            $("#modal-disable").modal();
        });
    </script>
@stop
