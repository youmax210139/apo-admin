@extends('layouts.base')

@section('title','奖期管理')

@section('function','奖期管理')
@section('function_link', '/lottery/issue/')

@section('here','奖期列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('lottery/issuecreate'))
                <a href="/lottery/issuecreate?lottery_id={{ $lottery_id }}" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加奖期
                </a>
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
                            <label for="username" class="col-sm-3 col-sm-3 control-label">期号</label>
                            <div class="col-sm-6">
                                <input type="text" value="{{request()->get('issue','')}}" class="form-control" name="issue" placeholder="期号">
                                <input type="hidden" name="lottery_id" value="{{request()->get('lottery_id')}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="created_date" class="col-sm-3 control-label">销售时间</label>
                            <div class="col-sm-9">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="sale_start" id="sale_start" value="" placeholder="开始时间">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="sale_end" id="sale_end" value="" placeholder="结束时间">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="user_type_id" class="col-sm-3 control-label">开号状态</label>
                            <div class="col-sm-9">
                                <select name="code_status" class="form-control">
                                    <option value="all">不限制</option>
                                    <option value="0">未写入</option>
                                    <option value="1">待验证</option>
                                    <option value="2">已验证</option>
                                    <option value="3">官方未开奖</option>
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
                        <button type="reset" class="btn btn-default col-sm-2"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-xs-12">
            <div class="box box-primary">

                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th data-sortable="false" class="hidden-sm">彩种</th>
                            <th data-sortable="false" class="hidden-sm">期号</th>
                            <th data-sortable="false" class="hidden-sm">周期开始时间</th>
                            <th data-sortable="false" class="hidden-sm">周期结束时间</th>
                            <th data-sortable="false" class="hidden-sm">最后撤单时间</th>
                            <th data-sortable="false" class="hidden-sm">开奖号码</th>
                            <th data-sortable="false" class="hidden-sm">号码</th>
                            <th data-sortable="false" class="hidden-sm">扣款</th>
                            <th data-sortable="false" class="hidden-sm">返点</th>
                            <th data-sortable="false" class="hidden-sm">中奖</th>
                            <th data-sortable="false" class="hidden-sm">派奖</th>
                            <th data-sortable="false" class="hidden-sm">追号</th>
                            <th data-sortable="false" class="hidden-sm">报表</th>
                            <th data-sortable="false" class="hidden-sm">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="row">
                          <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="delete_by_select" id="delete_by_select" value="删除所选" />
                          </div>
                          <div class="col-xs-4">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="start_time" id='start_time' autocomplete="off" placeholder="开始时间">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="end_time" id='end_time' autocomplete="off" placeholder="结束时间">
                                </div>
                          </div>
                          <div class="col-xs-2">
                            <input type="button" class="btn btn-primary" value="删除所选时间范围的奖期" name="delete_by_date" id="delete_by_date" />
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-msg" tabIndex="-1">
        <div class="modal-dialog">
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
                        <span id="tips_content"></span>
                    </p>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">确定</button>
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
                        <span id="delete_tips_content"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="delete_form" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="select_ids" id="select_ids" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade modal-default" id="modal-update-code" tabIndex="-1">
        <div class="modal-dialog modal-default modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">修改开奖号码</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-striped">
                        <tbody>
                        <tr>
                            <td class="text-right" width="190">彩种</td>
                            <td class="text-left">
                                <b>{{$lottery->name}}</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">奖期</td>
                            <td class="text-left">
                                <b class="upate_issue"></b>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">当前开奖号码</td>
                            <td class="text-left">
                                <b class="now_code"></b>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">新开奖号码</td>
                            <td class="text-left">
                                <input type="text" value="" id="new_code">
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <input type="hidden" id="edit_issue_id" value=""/>
                    <button type="button" class="btn btn-success" id="issue_update_code_save">保存设置</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
@stop

    @section('js')
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Select/css/select.dataTables.min.css" />
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css" />
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/datatables/extensions/Select/js/dataTables.select.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script src="/assets/plugins/datatables/plugs/Pagination/js/redirect.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig ={
            elem: '#start_time',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex:2
        };
        laydate(layConfig);

        layConfig.elem = '#end_time';
        laydate(layConfig);
        var layConfig1 ={
            elem: '#sale_start',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex:2
        };
        laydate(layConfig1);

        layConfig1.elem = '#sale_end';
        laydate(layConfig1);
        $(function () {
            var get_params = function (data) {
                var param = {
                    'issue'    : $('input[name="issue"]').val(),
                    'sale_start'    : $('input[name="sale_start"]').val(),
                    'sale_end'    : $('input[name="sale_end"]').val(),
                    'code_status'    : $('select[name="code_status"]').val(),
                };
                return $.extend({}, data, param);
            }
            var table = $("#tags-table").DataTable({
                language:app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                pageLength:50,
                searching:false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax('issue?lottery_id={{ $lottery_id }}',null,get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "lottery_name"},
                    {"data": "issue"},
                    {"data": "sale_start"},
                    {"data": "sale_end"},
                    {"data": "cancel_deadline"},
                    {"data": "code"},
                    {"data": "code_status"},
                    {"data": "deduct_status"},
                    {"data": "rebate_status"},
                    {"data": "check_bonus_status"},
                    {"data": "bonus_status"},
                    {"data": "task_to_project_status"},
                    {"data": "report_status"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': 7,
                        "render": function (data, type, row) {
                            var status_comments = {"0" : "未录入" , "1" : "写入待验证" , "2" : "已验证",'3':'官方未开奖' };
                            var status_style = {"0" : "text-blue" , "1" : "text-warning" , "2" : "text-green",'3':'text-red' };
                            //var str = row['code_status'] == 0  ? "未录入" : (row['code_status'] == 1?'写入待验证':(row['code_status'] == 2?'已验证':'官方未开奖'));
                            return app.getLabelHtml( status_comments[data], status_style[data]);
                        },
                    },
                    {
                        'targets': [8,9,10,11,12],
                        "render": function (data, type, row) {
                            var status_comments = {"0" : "未开始" , "1" : "进行中" , "2" : "已经完成" };
                            var status_style = {"0" : "text-blue" , "1" : "text-red" , "2" : "text-green" };
                            return app.getLabelHtml( status_comments[data], status_style[data]);
                        },
                    },
                    {
                        'targets': 13,
                        "render": function (data, type, row) {
                            data = (data>0 && data<5)?1:data;
                            var status_comments = {"-1" : "重新计算中" , "0" : "未开始" , "1" : "进行中("+row.report_status+")" , "5" : "已经完成" };
                            var status_style = {"0" : "text-blue" , "1" : "text-red" , "5" : "text-green" };
                            return app.getLabelHtml( status_comments[data], status_style[data]);
                        },
                    },

                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '';
                            var a_attr = {
                                'class' : ' X-Small btn-xs edit-status',
                                'issue_id': row['id'],
                                'issue': row['issue'],
                                'href': '#none'
                            };
                            str += app.getalinkHtml('重置状态',a_attr, 'fa-edit');
                            @if($lottery->special==1 && $lottery->special_config->hand_coding==1)
                             a_attr = {
                                'class' : ' X-Small btn-xs edit-code',
                                'issue_id': row['id'],
                                'issue': row['issue'],
                                'code':row['code'],
                                'href': '#none'
                            };
                            str += app.getalinkHtml('修改号码',a_attr, 'fa-edit');
                            @endif
                            return str;

                        },
                    }

                ],
                pagingType: "redirect",
                dom: "<'row'<'col-sm-6'Bl><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                select: {
                    style: 'multi'
                },
                buttons: [
                    {
                        text: '全选',
                        action: function () {
                            if( table.rows( { selected: true } ).count() == table.rows().count() ){
                                table.rows().deselect();
                            }else{
                                table.rows().select();
                            }
                        }
                    }
                ]
            });


            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                /*table.column(0).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });*/
                loadFadeOut();
            });

            $('#search_btn').click(function(event){
                event.preventDefault();
                table.ajax.reload();
            });
            //批量删除
            $("#delete_by_select").bind('click', function () {
                var select_rows = table.rows( { selected: true } );
                var id_array = select_rows.data().pluck( 'id' ).toArray();
                if(id_array.length==0){
                    $("#tips_content").html("请选择需要删除的奖期");
                    $("#modal-msg").modal();
                    return false;
                }
                $("#delete_tips_content").html("确认要删除选中的奖期吗？");
                $('#select_ids').val(id_array.join(','));
                $('.delete_form').attr('action', '/lottery/issuedelete?delete_by=select');
                $("#modal-delete").modal();
            });

            //按日期删除
            $("#delete_by_date").bind('click', function () {
                var start_time = $('#start_time').val();
                var end_time = $('#end_time').val();
                if(!start_time || !end_time){
                    $("#tips_content").html("请输入要删除的起始和截止日期");
                    $("#modal-msg").modal();
                    return false;
                }
                $("#delete_tips_content").html("确认要删除 "+start_time+" - "+end_time+"  之间的所有奖期吗？");
                $('#select_ids').val('');
                $('.delete_form').attr('action', '/lottery/issuedelete?delete_by=date&lottery_id={{ $lottery_id }}&start_time='+start_time+"&end_time="+end_time);
                $("#modal-delete").modal();
            });
            //修改号码
            $(document).on('click','.edit-code', function () {
                $(".upate_issue").html($(this).attr('issue'));
                $(".now_code").html($(this).attr('code'));
                $("#new_code").val('');
                $("#edit_issue_id").val($(this).attr('issue_id'));
                $("#modal-update-code").modal();
            });
            $("#issue_update_code_save").click(function () {

                loadShow();
                $.ajax({
                    url: "/lottery/editcode",
                    dataType: "json",
                    method: "post",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: $("#edit_issue_id").val(),
                        code: $("#new_code").val()
                    }
                }).done(function (json) {
                    loadFadeOut();
                    $('#modal-update-code').modal('hide');
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        table.draw(false);
                        $.notify({
                            message: json.msg
                        },{
                            type: 'success'
                        });
                    } else {
                        $.notify({
                            title: '<strong>提示!</strong>',
                            message: json.msg
                        },{
                            type: 'danger'
                        });
                    }
                }).fail(function () {
                    loadFadeOut();
                    $('#modal-update-code').modal('hide');
                    $.notify({
                        title: '<strong>提示!</strong>',
                        message: "请求失败"
                    },{
                        placement: {
                            from: "top",
                            align: "center"
                        },
                        type: 'danger'
                    });
                });
            });
            $("table").delegate('.edit-status', 'click', function () {
                var issue_id = $(this).attr('issue_id');
                BootstrapDialog.alert({
                    title: '警告',
                    message: '确认需要重置奖期状态吗?<br>此操作将重置奖期状态为[<b style="color:red">未判奖，未派奖，未扣款，未返点，未追号，报表未开始</b>]',
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    draggable: true, // <-- Default value is false
                    buttonLabel: '确认提交', // <-- Default value is 'OK',
                    callback: function(result) {
                        if(result){
                            $.ajax({
                                url: '/lottery/resetissuestatus',
                                dataType: "json",
                                method: "POST",
                                data:{id:issue_id},
                            }).done(function (json) {
                                if (json.status == 0) {
                                    table.draw(false);
                                }
                                BootstrapDialog.alert({
                                    title: json.status==0?'成功':'失败',
                                    message: json.msg,
                                    type: json.status==0?BootstrapDialog.TYPE_SUCCESS:BootstrapDialog.TYPE_DANGER, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                });
                            });
                        }
                    }
                });
            });
        });
    </script>
@stop
