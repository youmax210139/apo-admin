@extends('layouts.base')

@section('title','活动管理')

@section('function','活动管理')
@section('function_link', '/activity/')

@section('here','活动列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">

        </div>

        <div class="col-md-6 text-right">
    @if(Gate::check('activity/create'))
        <a href="/activity/create" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加活动 </a>
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
<div class="col-sm-12">
    <div class="box box-primary">
        @include('partials.errors')
        @include('partials.success')
        <div class="box-body">
            <table id="tags-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th data-sortable="false" class="hidden-sm"></th>
                    <th class="hidden-sm">唯一标识</th>
                    <th class="hidden-sm">活动名称</th>
                    <th class="hidden-sm">显示排序</th>
                    <th class="hidden-sm">开始时间</th>
                    <th class="hidden-sm">结束时间</th>
                    <th class="hidden-sm">进行状态</th>
                    <th class="hidden-sm">启用状态</th>
                    <th class="hidden-sm">发放方式</th>
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


<!--禁用活动项-->
<div class="modal fade" id="modal-status" tabIndex="-1">
    <div class="modal-dialog modal-primary">
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
                    确认要<span class="row_statustext"></span>该活动吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="statusForm" method="POST">
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
                var parent_id = $('#parent_id').attr('attr');
                var table = $("#tags-table").DataTable({
                	language:app.DataTable.language(),
                    order: [[0, "asc"]],
                    serverSide: true,
                    iDisplayLength :25,
                    // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                    // 要 ajax(url, type) 必须加这两参数
                    searching:false,
                    ajax: app.DataTable.ajax(),
                    "columns": [
                        {"data": "id"},
                        {"data": "ident","orderable":false},
                        {"data": "name","orderable":false},
                        {"data": "sort","orderable":false},
                        {"data": "start_time","orderable":false},
                        {"data": "end_time","orderable":false},
                        {"data": "process_status","orderable":false},
                        {"data": "status","orderable":false},
                        {"data": "draw_method","orderable":false},
                        {"data": "action"}
                    ],
                    columnDefs: [
                        {
                            'targets': -1,
                            "render": function (data, type, row) {
                                var row_edit = {{ Gate::check('activity/edit') ? 1 : 0 }};
                                var row_status = {{ Gate::check('activity/status') ? 1 : 0 }};
                                var row_export = {{ Gate::check('activity/record') ? 1 : 0 }};
                                var row_reglink = {{ Gate::check('activity/reglink') ? 1 : 0 }};
                                var row_prizepool = {{ Gate::check('activity/prizepool') ? 1 : 0 }};
                                var row_jackpot = {{ Gate::check('activity/jackpot') ? 1 : 0 }};
                                var row_withdrawaldelay = {{ Gate::check('activity/withdrawaldelay') ? 1 : 0 }};
                                var row_sendaward = {{ Gate::check('activity/sendaward') ? 1 : 0 }};
                                var row_statusclass = !row['status'] ? 'text-success' : 'text-danger';
                                var row_statusicon = !row['status'] ? 'fa-check-circle-o' : 'fa-ban';
                                var row_statustext = !row['status'] ? '启用' : '禁用' ;

                                var str = '';

                                //下级菜单
                                var a_attr = null;
                                var common_class = 'X-Small btn-xs ';

                                //编辑
                                if (row_edit) {
                                    a_attr = {
                                        'class' : common_class + 'text-success',
                                        'href': '/activity/edit?id=' + row['id']
                                    };
                                    str += app.getalinkHtml('编辑',a_attr, 'fa-edit');
                                }

                                //邀请码注册统计
                                if (row_reglink && row['ident'] == 'reglink') {
                                    a_attr = {
                                        'class' : common_class + 'text-primary',
                                        'mounttabs':'',
                                        'title':'邀请码注册统计',
                                        'href': '/activity/reglink'
                                    };
                                    str += app.getalinkHtml('注册统计',a_attr, 'fa-list');
                                }

                                //奖池管理
                                if (row_prizepool && row['ident'] == 'prizepool') {
                                    a_attr = {
                                        'class' : common_class + 'text-primary',
                                        'mounttabs':'',
                                        'title':'奖池管理',
                                        'href': '/activity/prizepool'
                                    };
                                    str += app.getalinkHtml('奖池管理',a_attr, 'fa-list');
                                }

                                //幸运大奖池
                                if (row_jackpot && row['ident'] == 'jackpot') {
                                    a_attr = {
                                        'class' : common_class + 'text-primary',
                                        'mounttabs':'',
                                        'title':'幸运大奖池管理',
                                        'href': '/activity/jackpot'
                                    };
                                    str += app.getalinkHtml('幸运大奖池管理',a_attr, 'fa-list');
                                }

                                //补偿金列表
                                if (row_withdrawaldelay && row['ident'] == 'withdrawaldelay') {
                                    a_attr = {
                                        'class' : common_class + 'text-primary',
                                        'mounttabs':'',
                                        'title':'补偿金列表',
                                        'href': '/activity/withdrawaldelay'
                                    };
                                    str += app.getalinkHtml('补偿金列表',a_attr, 'fa-list');
                                }

                                //导出中奖名单
                                if (row_export) {
                                    a_attr = {
                                        'class' : common_class + 'text-success',
                                        'mounttabs':'',
                                        'title':row['name'],
                                        'href': '/activity/record?id=' + row['id']
                                    };
                                    str += app.getalinkHtml('领取记录',a_attr, 'fa-list');
                                }
                                //导出中奖名单
                                if (row_sendaward && row['draw_method']==1) {
                                    a_attr = {
                                        'class' : common_class + 'text-red',
                                        'title':'发放礼金',
                                        'href': '/activity/sendaward?id=' + row['id']
                                    };
                                    str += app.getalinkHtml('发放礼金',a_attr, 'fa-gift');
                                }
                                //是否禁用
                                if (row_status) {
                                    a_attr = {
                                        'class' : common_class + 'status ' + row_statusclass,
                                        'attr': row['id'],
                                        'href': '#',
                                        'status' : row_statustext
                                    };
                                    //如果这里的html简单，直接写原生的html。如果html代码和js混一起比较复杂点的，使用app.getalinkHtml()
                                    str += app.getalinkHtml(row_statustext,a_attr, row_statusicon);
                                }

                                return str;
                        	}
                        },
                        {
                            'targets': -2,
                            'render': function (data, type, row) {
                                if (row['draw_method'] == 0) {
                                    return app.getLabelHtml('用户领取', 'label-primary');
                                } else if (row['draw_method'] == 1) {
                                    return app.getLabelHtml('管理员发放', 'label-success');
                                } else if (row['draw_method'] == 2) {
                                    return app.getLabelHtml('自动发放', 'label-danger');
                                } else if (row['draw_method'] == 3) {
                                    return app.getLabelHtml('充值触发', 'label-danger');
                                } else {
                                    return app.getLabelHtml('提现触发', 'label-danger');
                                }
                            }
                        },
                        {
                            'targets': -3,
                            'render': function (data, type, row) {
                                return app.getLabelHtml(
                                    row['status'] ? '启用':'禁用',
                                    'label-'+(row['status'] ? 'success' : 'danger')
                                );
                            }
                        },
                        {
                            'targets': -4,
                            'render': function (data, type, row) {
                                if (row['process_status'] == '进行中') {
                                    label_class = 'label-primary';
                                } else if (row['process_status'] == '已过期' || row['process_status'] == '将来进行') {
                                    label_class = 'label-warning';
                                } else {
                                    label_class = 'label-danger';
                                }
                                return app.getLabelHtml(row['process_status'], label_class);
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

                //禁用活动
                $("table").delegate('.status', 'click', function () {
                    var id = $(this).attr('attr');
                    $(".row_statustext").text($(this).attr('status'));
                    $('.statusForm').attr('action', '/activity/status?id=' + id);
                    $("#modal-status").modal();
                });
            });
        </script>
@stop