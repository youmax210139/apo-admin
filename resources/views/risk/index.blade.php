@extends('layouts.base')

@section('title','风控提款审核')
@section('function','风控提款审核')
@section('function_link', '/risk/')
@section('here','审核列表')

@section('content')
    <div class="row">
        <div class="col-md-12">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">会员</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="会员名"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">申请时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date"
                                               id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date"
                                               id='end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="verifier_username" class="col-sm-3 col-sm-3 control-label">风控</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='verifier_username' placeholder="风控管理员名称"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="-1">全部</option>
                                        @foreach($status_labels as $status_id => $status_label)
                                            <option value="{{$status_id}}" @if($status_id === 0) selected @endif>
                                                {{$status_label}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="btn-group col-md-6">
                            <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i
                                        class="fa fa-search" aria-hidden="true"></i>查询
                            </button>
                        </div>
                        <div class=" btn-group col-md-6">
                            <button type="reset" class="btn btn-default col-sm-2"></i>重置</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--搜索框 End-->

    <div class="checkbox">
        <label>
            <input type="checkbox" name="auto_refresh" checked> <select name="refresh_delay">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="30" selected>30</option>
                <option value="60">60</option>
            </select>秒自动刷新
        <audio id="mediaElementID" style="height: 10px" src="/assets/sound/tkdsh.mp3" preload="auto" controls></audio></label>
    </div>
    <div class="box box-primary">
        <div class="box-body">
            @include('partials.errors')
            @include('partials.success')
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th class="hidden-sm" data-sortable="false"></th>
                    <th class="hidden-sm" data-sortable="false">ID</th>
                    <th class="hidden-sm" data-sortable="false">会员</th>
                    <th class="hidden-sm" data-sortable="false">总代</th>
                    <th class="hidden-sm" data-sortable="false">组别</th>
                    <th class="hidden-sm" data-sortable="false">金额</th>
                    <th class="hidden-sm" data-sortable="false">申请时间</th>
                    <th class="hidden-sm" data-sortable="false">上次提款时间</th>
                    <th class="hidden-sm" data-sortable="false">上次提款金额</th>
                    <th class="hidden-sm" data-sortable="false">充值总额</th>
                    <th class="hidden-sm" data-sortable="false">投注总额</th>
                    <th class="hidden-sm" data-sortable="false">返奖总额</th>
                    <th class="hidden-sm" data-sortable="false">赔率</th>
                    <th class="hidden-sm" data-sortable="false">充投比</th>
                    <th class="hidden-sm" data-sortable="false">状态</th>
                    <th class="hidden-sm" data-sortable="false">操作</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <style>
        body {
            padding-right: 0px !important;
        }
        *.modal-open {
            overflow-y: scroll;
            padding-right: 0 !important;
        }
        #modal-deal .modal-body th, #modal-deal .modal-body td {
            height: 28px;
            line-height: 28px;
        }

        .dealthird_action {
            display: none
        }

        #tags-table_info .select-info {
            display: none;
        }
    </style>
    <div class="modal fade modal-default" id="modal-deal" tabIndex="-1">
        <div class="modal-dialog modal-default modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提款审核</h4>
                </div>
                <div class="modal-body ">
                </div>
                <div class="modal-footer deal_action">
                    <button type="button" class="btn btn-default pull-left btn-lg" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary btn-lg deal_btn">确认审核</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Select/css/select.dataTables.min.css"/>
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
                    'username': $("input[name='username']").val(),
                    'status': $("select[name='status']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'verifier_username':$("input[name='verifier_username']").val()
                };
                return $.extend({}, data, param);
            };

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
                    {"data": "id"},
                    {"data": "username"},
                    {"data": "top_username"},
                    {"data": "user_group_id"},
                    {"data": "amount"},
                    {"data": "created_at"},
                    {"data": "last_withdrawal_at"},
                    {"data": "last_withdrawal_amount"},
                    {"data": "deposit_total"},
                    {"data": "bet_price"},
                    {"data": 'bet_bonus'},
                    {"data": 'yl'},
                    {"data": 'ctb'},
                    {"data": "status"},
                    {"data": "action"}
                ],
                createdRow: function (row, data, index) {
                    if (data['amount'] >= 30000) {
                        $(row).addClass('danger');
                    }
                },
                select: {
                    style: 'single'
                },
                columnDefs: [
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            if (row['user_observe']) {
                                return app.getColorHtml(row.username, 'label-danger', false);
                            } else {
                                return row.username;
                            }
                        }
                    },
                    {
                        'targets': 4,
                        'render': function (data, type, row) {
                            if(data==1){
                                return app.getLabelHtml('正式', 'label-success');
                            }
                            if(data==2){
                                return app.getLabelHtml('测试', 'label-warning', false);
                            }else{
                                return app.getLabelHtml('试玩', 'label-danger', false);
                            }

                        }
                    },
                    {
                        'targets': 5,
                        'render': function (data, type, row) {

                            return app.getColorHtml(row.amount, 'text-red', false);

                        }
                    },
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '------';
                            if (row.status == 0 || row.status == 4) {
                                str = '<a attr="' + row.id + '" href="javascript:;" class="btn-xs text-primary deal">审核</a>';
                            } else if (row.status == 1 || row.status == 2) {
                                str = str = '<a attr="' + row.id + '" href="javascript:;" class="btn-xs text-primary detail">详情</a>';
                            }else if (row.status == 3) {
                                if (row.verifier_username == '{{$adminuser}}') {
                                    str = '<a attr="' + row.id + '" href="javascript:;" class="btn-xs text-primary deal">审核</a>';
                                } else {
                                    str = row.verifier_username + ' 审核中';
                                }
                            }
                            return str;
                        }
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            var status_txt;
                            var status_level;
                            if (row['status'] == 0) {
                                status_txt = '等待审核';
                                status_level = "warning";
                            } else if (row['status'] == 1) {
                                status_txt = '通过审核';
                                status_level = "success";
                            } else if (row['status'] == 2) {
                                status_txt = '拒绝出款';
                                status_level = "danger";
                            } else if (row['status'] == 3) {
                                status_txt = '正在审核';
                                status_level = 'primary';
                            } else if (row['status'] == 4) {
                                status_txt = '自动检查中';
                                status_level = 'primary';
                            }
                            return app.getLabelHtml(
                                status_txt,
                                'label-' + status_level
                            );
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            var str;
                            if(row.deposit_total > 0){
                                var str =  Number(row.bet_price / row.deposit_total * 100).toFixed(2);
                            }

                            return str;
                        }
                    },
                    {
                        'targets': -4,
                        'render': function (data, type, row) {
                            var str = '';
                            if(row.bet_price > 0){
                                str =  Number(row.bet_bonus/row.bet_price).toFixed(2);
                                str = '<a attr="' + row.id + '" href="javascript:;" class=" btn-xs text-primary bet_list">'+str+'</a>';
                            }
                            return str;
                        }
                    }
                ]
            });
            $("table").delegate('.detail', 'click', function () {
                var _this = $(this);
                var id = $(this).attr('attr');
                loadShow();
                $.ajax({
                    url: "/risk/detail",
                    dataType: "json",
                    method: "get",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: id
                    }
                }).done(function (json) {
                    $(".modal-title").html('审核明细 [ # '+id+' ]');
                    $(".deal_action").hide();
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        $("#modal-deal .modal-body").html(json.data);
                        $("#modal-deal").modal({backdrop: 'static', keyboard: false});
                    } else {
                        app.bootoast(json.msg);
                    }
                });
            });
            $("table").delegate('.deal', 'click', function () {
                var _this = $(this);
                var id = $(this).attr('attr');
                loadShow();
                $.ajax({
                    url: "/risk/deal?id=" + id,
                    dataType: "json",
                    method: "get",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: id
                    }
                }).done(function (json) {
                    $(".modal-title").html('提款审核 [ # '+id+' ]');
                    $(".deal_action").show();
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        $("#modal-deal .modal-body").html(json.data);
                        $("#modal-deal").modal({backdrop: 'static', keyboard: false});
                    } else {
                        app.bootoast(json.msg);
                    }
                });
            });
            $(".deal_btn").click(function () {
                var _this = $(this);
                var flag = $("#modal-deal :checked[name='status']").val();
                if (typeof(flag) == "undefined") {
                    BootstrapDialog.alert('请选择审核结果');
                    return false;
                }
                if (flag == 'refused') {
                    if ($("input[name='refused_reason']").val() == '') {
                        //app.bootoast("请填写或者选择拒绝原因！");
                        BootstrapDialog.alert('请填写或者选择拒绝原因!');
                        return false;
                    }
                }
                // if ($("#modal-deal textarea[name='remark']").val() == '') {
                //     app.bootoast("请输入备注");
                //     return false;
                // }
                BootstrapDialog.confirm({
                    message: "确认审核【" + (flag == 'passed' ? '通过' : '拒绝') + "】该笔提现吗？",
                    type: BootstrapDialog.TYPE_WARNING,
                    closable: true,
                    draggable: true,
                    btnCancelLabel: '取消',
                    btnOKLabel: '确认提交',
                    btnOKClass: 'btn-warning',
                    callback: function(result) {
                        if(result) {
                            $(_this).prop("disabled", true);
                            loadShow();
                            $.ajax({
                                url: "/risk/deal?id=" + $("input[name='risk_id']").val(),
                                dataType: "json",
                                method: "PUT",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: {
                                    id: $("input[name='risk_id']").val(),
                                    status: flag,
                                    remark: $("textarea[name='remark']").val(),
                                    refused_reason: $("input[name='refused_reason']").val()
                                }
                            }).done(function (json) {
                                $(_this).prop("disabled", false);
                                loadFadeOut();
                                if (json.status == 0) {
                                    BootstrapDialog.alert("审核【" + (flag == 'passed' ? '通过' : '拒绝') + "】提现申请成功！");
                                    table.ajax.reload();
                                } else {
                                    BootstrapDialog.alert("审核【" + (flag == 'passed' ? '通过' : '拒绝') + "】提现申请失败！");
                                }
                                $('#modal-deal').modal('hide');
                            });
                        }
                    }});
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });
            table.on('xhr.dt', function ( e, settings, json, xhr ) {
                if($("select[name='status']").val()==0) {
                    var mediaElement = document.getElementById('mediaElementID');
                    if(json.data.length>0){
                        mediaElement.play();
                    }else{
                        mediaElement.pause();
                    }
                }
            } )
            $('#search').submit(function (event) {
                event.preventDefault();
                table.ajax.reload();
            });
            $('#refresh').click(function (event) {
                event.preventDefault();
                table.ajax.reload();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            //30秒自动刷新
            var auto_refresh_interval='';
            $('input[name=auto_refresh]').change(function(){
                if( $(this).prop('checked') ){
                    auto_refresh();
                }else{
                    if( auto_refresh_interval == undefined || auto_refresh_interval == null ) return;
                    clearTimeout(auto_refresh_interval);
                }
            });
            auto_refresh();
            function auto_refresh(){

                if( auto_refresh_interval != undefined && auto_refresh_interval != null ) clearTimeout(auto_refresh_interval);
                table.ajax.reload();
                auto_refresh_interval = setTimeout(function () {
                    auto_refresh();
                },$("select[name='refresh_delay']").val()*1000);
            }
        });
    </script>
@stop
