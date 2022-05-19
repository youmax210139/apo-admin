@extends('layouts.base')

@section('title','配置管理')

@section('function','配置管理')
@section('function_link', '/config/')

@section('here','配置列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">
            @if($parent_id==0)
                <span style="margin:3px;" class="text-info">顶级菜单</span>
            @else
                <span style="margin:3px;" class="text-info">上级菜单：{{ $parent_name }}</span>
                <a style="margin:3px;" href="/config/"
                   class="btn btn-warning btn-md animation-shake reloadBtn"><i class="fa fa-mail-reply-all"></i> 返回顶级菜单
                </a>
            @endif
        </div>

    <div class="col-md-6 text-right">
        @if(Gate::check('config/refreshapp'))
            <a href="/config/refreshapp" class="btn btn-warning btn-md"><i class="fa fa-soundcloud"></i> 用户刷新页面广播 </a>
        @endif
    @if(Gate::check('config/create'))
        <a href="/config/create/?parent_id={{$parent_id}}" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加配置 </a>
    @endif
    @if(Gate::check('config/refresh'))
        <a href="/config/refresh" class="btn btn-success btn-md"><i class="fa fa-refresh"></i> 刷新配置[{{$refresh_at}}]</a>
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
                    <th class="hidden-sm">配置标题</th>
                    <th class="hidden-sm">配置名称</th>
                    <th class="hidden-sm">配置值</th>
                    <th class="hidden-sm">更新时间</th>
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

<!--删除配置项-->
<div class="modal fade" id="modal-delete" tabIndex="-1">
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
                确认要删除该配置项吗?
            </p>
        </div>
        <div class="modal-footer">
            <form class="deleteForm" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-times-circle"></i> 确认
                </button>
            </form>
        </div>
    </div>
</div>
</div>

<!--禁用配置项-->
<div class="modal fade" id="modal-disable" tabIndex="-1">
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
                    确认要<span class="row_disable_text"></span>该配置吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="disableForm" method="POST">
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
                var table = $("#tags-table").DataTable({
                	language:app.DataTable.language(),
                    order: [[0, "asc"]],
                    serverSide: true,
                    iDisplayLength :25,
                    // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                    // 要 ajax(url, type) 必须加这两参数
                    ajax: app.DataTable.ajax(),
                    "columns": [
                        {"data": "id"},
                        {"data": "title"},
                        {"data": "key"},
                        {"data": "value"},
                        {"data": "updated_at"},
                        {"data": "is_disabled"},
                        {"data": "action"}
                    ],
                    columnDefs: [
                        {
                            'targets': -1,
                            "render": function (data, type, row) {
                                var row_edit = {{ Gate::check('config/edit') ? 1 : 0 }};
                                var row_set = {{ Gate::check('config/set') ? 1 : 0 }};
                                var row_delete = {{ Gate::check('config/delete') ? 1 : 0 }};
                                var row_disable = {{ Gate::check('config/disable') ? 1 : 0 }};
                                var row_disbale_class = row['is_disabled'] ? 'text-success' : 'text-danger';
                                var row_disbale_icon = row['is_disabled'] ? 'fa-check-circle-o' : 'fa-ban';
                                var row_disable_text = row['is_disabled'] ? '启用' : '禁用' ;

                                var str = '';

                                //下级菜单
                                var a_attr = null;
                                var common_class = 'X-Small btn-xs ';
                                if (row['parent_id'] == 0) {
                                    a_attr = {
                                        href:'/config/?parent_id=' + row['id'],
                                        class:common_class + 'text-primary',
                                    };
                                    str += app.getalinkHtml('下级菜单', a_attr, 'fa-adn');
                                 }
                                if (row_set && row['parent_id'] > 0) {
                                    a_attr = {
                                        'class' : common_class + 'text-success setconfig',
                                        'href':'javascript:;',
                                        'url': '/config/set?id=' + row['id']
                                    };
                                    str += app.getalinkHtml('设置',a_attr, 'fa-edit');
                                }
                                //编辑
                                if (row_edit) {
                                    a_attr = {
                                        'class' : common_class + 'text-success',
                                        'href': '/config/edit?id=' + row['id']
                                    };
                                    str += app.getalinkHtml('编辑',a_attr, 'fa-edit');
                                }

                                //是否禁用
                                if (row_disable) {
                                    a_attr = {
                                        'class' : common_class + 'disable ' + row_disbale_class,
                                        'attr': row['id'],
                                        'href': '#',
                                        'isdisabled' : row_disable_text
                                    };
                                    //如果这里的html简单，直接写原生的html。如果html代码和js混一起比较复杂点的，使用app.getalinkHtml()
                                    str += app.getalinkHtml(row_disable_text,a_attr, row_disbale_icon);
                                }

                                //删除
                                if (row_delete) {
                                    a_attr = {
                                        'class' : common_class + 'text-danger delBtn',
                                        'attr': row['id'],
                                        'href': '#',
                                        'isdisabled' : row_disable_text
                                    };
                                    str += app.getalinkHtml('删除',a_attr, 'fa-times-circle');
                                }

                                return str;
                        	}
                        },
                        {
                            'targets': -2,
                            'render': function (data, type, row) {
                                return app.getLabelHtml(
                                    row['is_disabled'] ? '禁用':'启用',
                                    'label-'+(row['is_disabled'] ? 'danger' : 'success')
                                );
                            }
                        },
                        {
                            'targets': -4,
                            'render': function (data, type, row) {
                                return '<div style="max-width: 220px;word-wrap:break-word">'+data+'</div>';
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

                //删除
                $("table").delegate('.delBtn', 'click', function () {
                    var id = $(this).attr('attr');
                    $('.deleteForm').attr('action', '/config/?id=' + id);
                    $("#modal-delete").modal();
                });
                $("table").delegate('.setconfig', 'click', function () {
                    var url = $(this).attr('url');
                    BootstrapDialog.show({
                        title:'设置配置',
                        message: $('<div></div>').load(url),
                        buttons: [{
                            icon: 'glyphicon glyphicon-send',
                            label: '立即设置',
                            cssClass: 'btn-primary',
                            autospin: true,
                            action: function(dialogRef){
                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                $.ajax({
                                    url: url,
                                    dataType: "json",
                                    method: "POST",
                                    data:$("#setconfig-form").serialize(),
                                }).done(function (json) {
                                    dialogRef.close();
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
                        }, {
                            label: '取消',
                            action: function(dialogRef){
                                dialogRef.close();
                            }
                        }]
                    });
                });
                //禁用配置
                $("table").delegate('.disable', 'click', function () {
                    var id = $(this).attr('attr');
                    $(".row_disable_text").text($(this).attr('isdisabled'));
                    $('.disableForm').attr('action', '/config/disable?id=' + id);
                    $("#modal-disable").modal();
                });
            });
        </script>
@stop