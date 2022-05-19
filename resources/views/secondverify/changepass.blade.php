@extends('layouts.base')

@section('title','人工修改密码审核')

@section('function','人工修改密码审核')

@section('here','审核列表')

{{--@section('pageDesc','DashBoard')--}}

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
                            <label for="username" class="col-sm-3 col-sm-3 control-label">会员</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='username' placeholder="会员" />

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="created_date" class="col-sm-3 control-label">添加时间</label>
                            <div class="col-sm-9">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="created_start_date" id='created_start_date' value="" placeholder="开始时间">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="created_end_date" id='created_end_date' value="" placeholder="结束时间">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="created_admin" class="col-sm-3 col-sm-3 control-label">操作员</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='created_admin' placeholder="操作员" />

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="frozen_date" class="col-sm-3 control-label">审核时间</label>
                            <div class="col-sm-9">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="verify_start_date" id='frozen_start_date' value="" placeholder="开始时间">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="verify_end_date" id='frozen_end_date' value="" placeholder="结束时间">
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="verify_admin" class="col-sm-3 col-sm-3 control-label">审核员</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='verify_admin' placeholder="审核员" />

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label">审核状态</label>
                            <div class="col-sm-9">
                                <select name="status" class="form-control">
                                    <option value="0">等待审核</option>
                                    <option value="1">通过审核</option>
                                    <option value="2">未通过审核</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="box-footer">
                    <div class="btn-group col-md-6">
                        <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                    </div>
                    <div class=" btn-group col-md-6">
                        <button type="reset" class="btn btn-default col-sm-2" ><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-body">
                 @include('partials.errors')
                 @include('partials.success')
                <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                    <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false"></th>

                            <th class="hidden-sm" data-sortable="false">状态</th>
                            <th class="hidden-sm" data-sortable="false">用户</th>
                            <th class="hidden-sm" data-sortable="false">所属总代</th>
                           
                            <th class="hidden-sm" data-sortable="false">重设密码类型</th>
                           
                            <th class="hidden-sm">操作员</th>
                            <th class="hidden-sm">审核员</th>
                            <th class="hidden-sm">审核时间</th>
                            <th class="hidden-sm" data-sortable="false">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                    确认要<span class="row_verify_text"></span>该会员密码吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="verifyForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="status" value="">
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
<script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
<script>
laydate.skin('lynn');
var layConfig = {
    elem: '#created_start_date',
    event: 'focus',
    format: 'YYYY-MM-DD hh:mm:ss',
    istime: true,
};
laydate(layConfig);

layConfig.elem = '#created_end_date';
laydate(layConfig);
layConfig.elem = '#frozen_start_date';
laydate(layConfig);
layConfig.elem = '#frozen_end_date';
laydate(layConfig);

$(function () {
    var get_params = function (data) {
        var param = {
            'created_admin': $("input[name='created_admin']").val(),
            'status': $("select[name='status']").val(),
            'created_start_date': $("input[name='created_start_date']").val(),
            'created_end_date': $("input[name='created_end_date']").val(),
            'verify_start_date': $("input[name='verify_start_date']").val(),
            'verify_end_date': $("input[name='verify_end_date']").val(),
            'verify_admin': $("input[name='verify_admin']").val(),
             'username': $("input[name='username']").val(),
        };
        return $.extend({}, data, param);
    }

    var table = $("#tags-table").DataTable({
        language: app.DataTable.language(),
        order: [[1, "asc"]],
        serverSide: true,
        pageLength: 25,
        searching: false,
        // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
        // 要 ajax(url, type, get_params) 必须加这两参数
        ajax: app.DataTable.ajax(null, null, get_params),
        "columns": [
            {"visible": false},
            {"data": "status"},
            {"data": "username"},
            {"data": "topname"},
            {"data": "ordertype"},
            
            {"data": "created_admin_name"},
            {"data": "verify_admin_name"},
            {"data": "verify_at"},
            {"data": null},
        ],
        columnDefs: [
            {
                'targets': 1,
                "render": function (data, type, row) {
                  
                    if (row.status == 1) {
                        return app.getLabelHtml( '审核通过',
                                     'label-success'
                                );
                    } else if (row.status == 2) {
                        return app.getLabelHtml(
                                   '审核未通过',
                                     'label-danger'
                                );
                      
                    }else{
                      return app.getLabelHtml(
                                   '未审核',
                                     'label-warning'
                                );   
                    }
                     
                }
            },
            {
                'targets': -1,
                "render": function (data, type, row) {
                    var str = '';
                    if (row.status == 0) {
                        str += '<a attr="' + row.id + '" is_verify="1"  href="#" class="X-Small btn-xs text-primary verify">审核通过</a>';
                        str += '<a attr="' + row.id + '" is_verify="2" href="#" class="X-Small btn-xs text-danger verify">审核未通过</a>';
                    } else {
                        str += "------"
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

    $('#search').submit(function (event) {
        event.preventDefault();
        $("input[name='is_search']").val(1);
        table.ajax.reload();
    });

    $.fn.dataTable.ext.errMode = function () {
        app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
        loadFadeOut();
    };
 //审核
                $("table").delegate('.verify', 'click', function () {
                    var id = $(this).attr('attr');
                    $(".row_verify_text").text($(this).text());
                    $('.verifyForm').attr('action', '/Verifychangepwd/verify?id=' + id);
                    $('.verifyForm input[name="status"]').val($(this).attr("is_verify"));
                    $("#modal-verify").modal();
                });

});
</script>
@stop