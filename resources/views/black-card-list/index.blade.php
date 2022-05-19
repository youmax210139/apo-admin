@extends('layouts.base')

@section('title','银行卡黑名单管理')

@section('function','银行卡黑名单管理')
@section('function_link', '/blackcardlist/')

@section('here','银行卡黑名单列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('blackcardlist/create'))
                <a href="javascript:;" class="btn btn-primary btn-md" id="add_black_card"><i class="fa fa-plus-circle"></i> 添加银行卡黑名单</a>
                <a href="/blackcardlist/create" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 批量添加</a>
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
        <div class="col-xs-12">
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
                                <label for="created_date" class="col-sm-3 control-label">账户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='account_name' placeholder="账户名" />
                                </div>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="frozen_date" class="col-sm-3 control-label">卡号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='account' placeholder="卡号" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="balance" class="col-sm-3 control-label">类型</label>
                                <div class="col-sm-9">
                                    <select name="type" class="form-control">
                                        <option value="0">所有类型</option>
                                        <option value="1">系统</option>
                                        <option value="2">共用</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn">
                            <i class="fa fa-search" aria-hidden="true"></i>查询
                        </button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                    </div>
                </form>
            </div>

            <div class="box box-primary">

                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">账户名</th>
                            <th class="hidden-sm">卡号</th>
                            <th class="hidden-sm">类型</th>
                            <th class="hidden-sm">备注</th>
                            <th class="hidden-sm">加入时间</th>
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
                        确认要删除该银行卡黑名单吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST" >
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade modal-default" id="modal_add_black_card" tabIndex="-1">
        <div class="modal-dialog modal-default modal-xs">
            <div class="modal-content">
                <form method="post" action="/blackcardlist/create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">添加银行卡黑名单</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-striped" id="user_level_table">
                        <tbody>
                            <tr>
                                <td class="text-right" width="120">账户名</td>
                                <td class="text-left">
                                    <input type="text" class="form-control" name='account_name' placeholder="账户名" />
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" width="120">卡号</td>
                                <td class="text-left">
                                    <input type="text" class="form-control" name='account' placeholder="卡号" />
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" width="120">备注</td>
                                <td class="text-left">
                                    <textarea name="remark" class="form-control" style="width:100%;height:80px;"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-success" id="user_level_save">保存</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script>
    $(function () {
        var get_params = function (data) {
            var param = {
                'account_name': $("input[name='account_name']").val(),
                'account': $("input[name='account']").val(),
                'type': $("select[name='type']").val(),
            };
            return $.extend({}, data, param);
        }

        var table = $("#tags-table").DataTable({
            language:app.DataTable.language(),
            order: [[0, "asc"]],
            pageLength: 25,
            serverSide: true,
            searching: false,
            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
            // 要 ajax(url, type) 必须加这两参数
            ajax: app.DataTable.ajax(null, null, get_params),
            "columns": [
                {"data": "id"},
                {"data": "account_name"},
                {"data": "account"},
                {"data": "type"},
                {"data": "remark"},
                {"data": "created_at"},
                {"data": "action"},
            ],
            columnDefs: [
                {
                    'targets': 3,
                    "render": function (data, type, row) {
                        return (data === 2)?'共用':'系统';
                    }
                },
                {
                    'targets': -1,
                    "render": function (data, type, row) {
                        var row_delete = {{Gate::check('blackcardlist/delete') ? 1 :0}};
                        var str = '';

                        //删除
                        if (row_delete) {
                            str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
                        }

                        return str;
                    }
                }
            ]
        });

        table.on('preXhr.dt', function () {
            loadShow();
        });
        table.on('draw.dt', function () {
            loadFadeOut();
        });

        $('#search_btn').click(function () {
            event.preventDefault();
            table.ajax.reload();
        });
        $("table").delegate('.delBtn', 'click', function () {
            var id = $(this).attr('attr');
            $('.deleteForm').attr('action', '/blackcardlist/?id=' + id);
            $("#modal-delete").modal();
        });

        $('#add_black_card').click(function(){
            $('#modal_add_black_card').modal('show');
        });
    });
</script>
@stop
