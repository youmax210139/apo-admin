@extends('layouts.base')

@section('title','公告管理')

@section('function','公告管理')
@section('function_link', '/notice/')

@section('here','公告列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">

        </div>

        <div class="col-md-6 text-right">
    @if(Gate::check('notice/create'))
        <a href="/notice/create" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加公告 </a>
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
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th data-sortable="false" class="hidden-sm"></th>
                    <th class="hidden-sm">排序</th>
                    <th data-sortable="false">标题</th>
                    <th class="hidden-sm">发布时间</th>
                    <th class="hidden-sm">结束时间</th>
                    <th class="hidden-sm" data-sortable="false">发布人</th>
                    <th class="hidden-sm" data-sortable="false">审核人 / 时间</th>
                    <th class="hidden-sm" data-sortable="false">弹出提示</th>
                    <th class="hidden-sm" data-sortable="false">置顶</th>
                    <th class="hidden-sm" data-sortable="false">状态</th>
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

<div class="modal fade" id="modal-show" tabIndex="-1">
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
                确认要<span class="row_show_text"></span>该公告吗?
            </p>
        </div>
        <div class="modal-footer">
            <form class="showForm" method="POST">
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

<div class="modal fade" id="modal-verify" tabIndex="-1">
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
                    确认要<span class="row_verify_text"></span>该公告吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="verifyForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle-o"></i> 确认
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
                	language:app.DataTable.language(),
                    order: [[1, "asc"]],
                    serverSide: true,
                    iDisplayLength :25,
                    // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                    // 要 ajax(url, type) 必须加这两参数
                    ajax: app.DataTable.ajax(),
                    "columns": [
                        {"data": "id"},
                        {"data": "sort"},
                        {"data": "subject"},
                        {"data": "published_at"},
                        {"data": "end_at"},
                        {"data": "created_admin"},
                        {"data": "verified_admin"},
                        {"data": "is_alert"},
                        {"data": "is_top"},
                        {"data": "is_show"},
                        {"data": "action"}
                    ],
                    columnDefs: [
                        {
                            'targets': -1,
                            "render": function (data, type, row) {
                                var row_edit = {{ Gate::check('notice/edit') ? 1 : 0 }};
                                var row_verify = {{ Gate::check('notice/verify') ? 1 : 0 }};
                                var row_show = {{ Gate::check('notice/show') ? 1 : 0 }};
                                var row_alert = {{ Gate::check('notice/alert') ? 1 : 0 }};
                                var row_del = {{ Gate::check('notice/del') ? 1 : 0 }};
                                var row_verify_class = row['verified_admin'] ? 'text-info' : 'text-success';
                                var row_verify_icon = row['verified_admin'] ? 'fa-ban' : 'fa-check-circle-o';
                                var row_verify_text = row['verified_admin'] ? '取消审核' : '审核' ;
                                var row_show_class = row['is_show'] ? 'text-danger' : 'text-success';
                                var row_show_icon = row['is_show'] ? 'fa-ban' : 'fa-check-circle-o';
                                var row_show_text = row['is_show'] ? '隐藏' : '显示' ;

                                var row_alert_class = row['is_alert'] ? 'text-danger' : 'text-success';
                                var row_alert_icon = row['is_alert'] ? 'fa-ban' : 'fa-check-circle-o';
                                var row_alert_text = row['is_alert'] ? '取消弹出' : '弹出' ;

                                var str = '';

                                //下级菜单
                                var common_class = 'X-Small btn-xs ';

                                //编辑
                                if (row_edit) {
                                    a_attr = {
                                        'class' : common_class + 'text-success',
                                        'href': '/notice/edit?id=' + row['id']
                                    };
                                    str += app.getalinkHtml('编辑',a_attr, 'fa-edit');
                                }

                                //是否审核
                                if (row_verify) {
                                    a_attr = {
                                        'class' : common_class + 'verify ' + row_verify_class,
                                        'attr': row['id'],
                                        'href': '#',
                                        'is_verified' : row_verify_text
                                    };
                                    //如果这里的html简单，直接写原生的html。如果html代码和js混一起比较复杂点的，使用app.getalinkHtml()
                                    str += app.getalinkHtml(row_verify_text,a_attr, row_verify_icon);
                                }

                                //显示
                                if (row_show) {
                                    a_attr = {
                                        'class' : common_class + 'is_show ' + row_show_class,
                                        'attr': row['id'],
                                        'href': '#',
                                        'is_show' : row_show_text
                                    };
                                    str += app.getalinkHtml(row_show_text,a_attr, row_show_icon);
                                }
                                //弹出
                                if (row_alert) {
                                    a_attr = {
                                        'class' : common_class + 'is_alert ' + row_alert_class,
                                        'attr': row['id'],
                                        'href': '#',
                                        'is_alert' : row_alert_text
                                    };
                                    str += app.getalinkHtml(row_alert_text,a_attr, row_alert_icon);
                                }
                                //删除
                                if (row_del) {

                                        a_attr = {
                                            'class' : common_class + 'text-danger del',
                                            'attr': row['id'],
                                            'href': '#',
                                        };
                                        str += app.getalinkHtml('删除',a_attr, 'fa-edit');

                                }
                                return str;
                        	}
                        },
                        {
                            'targets': 3,
                            'render': function (data, type, row) {
                                var date = new Date();
                                var month = date.getMonth()+1;
                                var day = date.getDate();
                                var hours = date.getHours();
                                var minutes = date.getMinutes();
                                var seconds = date.getSeconds();
                                if(month < 10) month = '0' + month;
                                if(day < 10) day = '0' + day;
                                if(hours < 10) hours = '0' + hours;
                                if(minutes < 10) minutes = '0' + minutes;
                                if(seconds < 10) seconds = '0' + seconds;

                                var now_date = date.getFullYear()
                                    + '-' + month
                                    + '-' + day
                                    + ' ' + hours
                                    + ':' + minutes
                                    + ':' + seconds;
                                if(now_date < row['published_at']) {
                                    return app.getLabelHtml(
                                        row['published_at'],
                                        'label-default'
                                    );
                                } else {
                                    return row['published_at'];
                                }
                            }
                        },
                        {
                            'targets': 4,
                            'render': function (data, type, row) {
                                var date = new Date();
                                var month = date.getMonth()+1;
                                var day = date.getDate();
                                var hours = date.getHours();
                                var minutes = date.getMinutes();
                                var seconds = date.getSeconds();
                                if(month < 10) month = '0' + month;
                                if(day < 10) day = '0' + day;
                                if(hours < 10) hours = '0' + hours;
                                if(minutes < 10) minutes = '0' + minutes;
                                if(seconds < 10) seconds = '0' + seconds;

                                var now_date = date.getFullYear()
                                    + '-' + month
                                    + '-' + day
                                    + ' ' + hours
                                    + ':' + minutes
                                    + ':' + seconds;
                                if(now_date > row['end_at']) {
                                    return app.getLabelHtml(
                                        row['end_at'],
                                        'label-default'
                                    );
                                } else {
                                    return row['end_at'];
                                }
                            }
                        },
                        {
                            'targets': -3,
                            'render': function (data, type, row) {
                                return app.getLabelHtml(
                                    row['is_top'] ? '是':'否',
                                    'label-'+(row['is_top'] ? 'success' : 'info')
                                );
                            }
                        },
                        {
                            'targets': -4,
                            'render': function (data, type, row) {
                                return app.getLabelHtml(
                                    row['is_alert'] ? '是':'否',
                                    'label-'+(row['is_alert'] ? 'success' : 'warning')
                                );
                            }
                        },
                        {
                            'targets': -2,
                            'render': function (data, type, row) {
                                return app.getLabelHtml(
                                    row['is_show'] ? '显示' : '隐藏',
                                    'label-' + (row['is_show'] ? 'success' : 'danger')
                                );
                            }
                        },
                         {
                            'targets': -5,
                            'render': function (data, type, row) {
                                return app.getLabelHtml(
                                    row['verified_admin'] ? row['verified_admin']+' / '+row['verified_at']:'未审核',
                                    'label-'+(row['verified_admin'] ? 'primary' : 'danger')
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
                });

                //审核
                $("table").delegate('.verify', 'click', function () {
                    var id = $(this).attr('attr');
                    $(".row_verify_text").text($(this).attr('is_verified'));
                    $('.verifyForm').attr('action', '/notice/verify?id=' + id);
                    $("#modal-verify").modal();
                });

                //隐藏
                $("table").delegate('.is_show', 'click', function () {
                    var id = $(this).attr('attr');
                    $(".row_show_text").text($(this).attr('is_show'));
                    $('.showForm').attr('action', '/notice/show?id=' + id);
                    $("#modal-show").modal();
                });
                //弹出
                $("table").delegate('.is_alert', 'click', function () {
                    var id = $(this).attr('attr');
                    $(".row_show_text").text($(this).attr('is_alert'));
                    $('.showForm').attr('action', '/notice/alert?id=' + id);
                    $("#modal-show").modal();
                });
                //弹出
                $("table").delegate('.del', 'click', function () {
                    var id = $(this).attr('attr');
                    $(".row_show_text").text('删除');
                    $('.showForm').attr('action', '/notice/del?id=' + id);
                    $("#modal-show").modal();
                });
            });
        </script>
@stop