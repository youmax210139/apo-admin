@extends('layouts.base')

@section('title','分红契约报表')

@section('function','分红契约报表')
@section('function_link', '/dividendreport/')

@section('here','分红契约报表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            <!--
            @if(Gate::check('dividendreport/create'))
                <a href="javascript:;" class="btn btn-primary btn-md" id="add_black_card"><i class="fa fa-plus-circle"></i> 添加分红契约</a>
            @endif
            -->
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
                                <label for="username" class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' id="username" placeholder="用户名" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">上级用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='parent_username' id="parent_username" placeholder="上级用户名" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user_level" class="col-sm-3 control-label">用户级别</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='user_level' id="user_level" placeholder="用户级别:'1级代理'>1" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">记录时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_time" value="{{ $start_date }}"  id='start_time'   placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_time" value="{{ $end_date }}"  id='end_time'  placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="calculate_total" class="col-sm-3 control-label">全部总计</label>
                                <div class="col-sm-9">
                                    <select name="calculate_total" class="form-control">
                                        <option value="1" checked="checked">否</option>
                                        <option value="2">是</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dividend_type" class="col-sm-3 control-label">分红类型</label>
                                <div class="col-sm-9">
                                    <select name="dividend_type" class="form-control">
                                        <option value="0" selected>所有模式</option>
                                        <option value="2">B线[比例模式]</option>
                                        <option value="1">A线[佣金模式]</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="all">所有</option>
                                        <option value="1">已发放</option>
                                        <option value="2">发放中</option>
                                        <option value="3">上级审核</option>
                                        <option value="4" selected>后台审核</option>
                                        <option value="5">已取消</option>
                                        <option value="6">不符合条件</option>
                                        <option value="7">非分红周期</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="send_type" class="col-sm-3 control-label">发放类型</label>
                                <div class="col-sm-9">
                                    <select name="send_type" class="form-control">
                                        <option value="all">所有</option>
                                        <option value="1">系统发放</option>
                                        <option value="2">用户发放</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="period" class="col-sm-3 control-label">结算周期</label>
                                <div class="col-sm-9">
                                    <select name="period" class="form-control">
                                        <option value="all">所有</option>
                                        <option value="1">日分红</option>
                                        <option value="4">周分红</option>
                                        <option value="5">10日分红</option>
                                        <option value="2">半月分红</option>
                                        <option value="3">月分红</option>
                                        <option value="11">浮动日分红</option>
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
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover " >
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th data-sortable="false" class="hidden-sm">用户名</th>
                            <th data-sortable="false" class="hidden-sm">级别</th>
                            <th data-sortable="false" width="70" class="hidden-sm">开始时间</th>
                            <th data-sortable="false" width="70" class="hidden-sm">结束时间</th>
                            <th data-sortable="false" class="hidden-sm">状态</th>
                            <!--
                            <th data-sortable="false" class="hidden-sm">销量</th>
                            <th data-sortable="false" class="hidden-sm">奖金</th>
                            <th data-sortable="false" class="hidden-sm">返点</th>
                            <th data-sortable="false" class="hidden-sm">充值手续费</th>
                            <th data-sortable="false" class="hidden-sm">提现手续费</th>
                            <th data-sortable="false" class="hidden-sm">日工资</th>
                            <th data-sortable="false" class="hidden-sm">活动金</th>
                            -->
                            <th data-sortable="false" class="hidden-sm">周期团队盈亏</th>
                            <th data-sortable="false" class="hidden-sm">活跃人数</th>
                            <!--
                            <th data-sortable="false" class="hidden-sm">彩票活跃人数</th>
                            -->
                            <th data-sortable="false" class="hidden-sm">分红比例</th>
                            <th data-sortable="false" class="hidden-sm">分红应派金额</th>
                            <th data-sortable="false" class="hidden-sm">分红类型</th>
                            <th data-sortable="false" class="hidden-sm">分红模式</th>
                            <th data-sortable="false" class="hidden-sm">发放时间</th>
                            <th data-sortable="false" width="130">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="6" class="text-right"><b>本页总计： </b></th>
                            <th ></th>
                            <th colspan="2"></th>
                            <th></th>
                            <th colspan="4"></th>
                        </tr>
                        <tr id="total_sum" style="display: none;">
                            <th colspan="6" class="text-right"><b>全部总计： </b></th>
                            <th ></th>
                            <th colspan="2"></th>
                            <th></th>
                            <th colspan="4"></th>
                        </tr>
                        </tfoot>
                    </table>
                    <div class="row">
                        @if(Gate::check('dividendreport/check'))
                        <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="check_by_select" id="check_by_select"
                                   value="批量通过"/>
                        </div>
                        <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="refuse_by_select" id="refuse_by_select"
                                   value="批量拒绝"/>
                        </div>
                        @endif
                        @if(Gate::check('dividendreport/delete'))
                        <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="delete_by_select" id="delete_by_select"
                                   value="批量删除"/>
                        </div>
                        @endif
                        {{$msg_remark}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--同意与拒绝模态框-->
    <div class="modal fade" id="modal_check" tabIndex="-1">
        <div class="modal-dialog ">
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
                        确认要<span id="status_title"></span>用户【<span id="modal_username"></span>】分红么?
                        <br>当前状态：<span id="old_status_name"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="checkForm" method="POST" onsubmit="return changeStatus(event);">
                        <input type="hidden" value="" id="checkform_id">
                        <input type="hidden" value="" id="checkform_status">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger" id="modal_submit">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--查看明细模态框-->
    <div class="modal fade" id="modal_detail" tabIndex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <!--删除模态框-->
    <div class="modal fade" id="modal_delete" tabIndex="-1">
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
                        确认要删除这条分红记录吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST" onsubmit="return delete_row(event);">
                        <input type="hidden" value="" id="delete_id">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i> 确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--批量处理模态框-->
    <div class="modal fade" id="modal-form" tabIndex="-1">
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
                    <form class="modal_form" method="POST"  onsubmit="return batch_processing(event);">
                        <input type="hidden" name="select_ids" id="select_ids" value="">
                        <input type="hidden" name="batch_pro_type" id="batch_pro_type" value="1">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" id="modal_submit_by_select" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
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
    var table = '';
    $(function () {
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

        var get_params = function (data) {
            var param = {
                'username': $("input[name='username']").val(),
                'start_time': $("input[name='start_time']").val(),
                'end_time': $("input[name='end_time']").val(),
                'status': $("select[name='status']").val(),
                'send_type': $("select[name='send_type']").val(),
                'calculate_total': $('select[name="calculate_total"]').val(),
                'parent_username': $('input[name="parent_username"]').val(),
                'period': $("select[name='period']").val(),
                'user_level': $('input[name="user_level"]').val(),
                'dividend_type': $('select[name="dividend_type"]').val(),
            };
            return $.extend({}, data, param);
        }

        table = $("#tags-table").DataTable({
            language:app.DataTable.language(),
            pageLength: 25,
            serverSide: true,
            searching: false,
            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
            // 要 ajax(url, type) 必须加这两参数
            ajax: app.DataTable.ajax(null, null, get_params),
            "columns": [
                {"data": "id"},
                {"data": "username"},
                {"data": "user_type_name"},
                {"data": "start_date"},
                {"data": "end_date"},
                {"data": "status"},
                /*
                {"data": "total_price"},
                {"data": "total_bonus"},
                {"data": "total_rebate"},
                {"data": "total_deposit_fee"},
                {"data": "total_withdrawal_fee"},
                {"data": "total_wage"},
                {"data": "total_activity"},
                */
                {"data": "total_profit"},
                {"data": "total_daus"},
                /*
                {"data": "lottery_daus"},
                */
                {"data": "rate"},
                {"data": "amount"},
                {"data": "type"},
                {"data": "mode"},
                {"data": "send_at"},
                {"data": "null"},
            ],
            columnDefs: [
                {
                    'targets': 2,
                    "render": function (data, type, row) {
                        return app.getLabelHtml(data, 'label-primary');
                    }
                },
                {
                    'targets': 5,
                    "render": function (data, type, row) {
                        var label = 'label-warning';
                        if( data == 1){
                            label = 'label-success';
                            data = '已发放';
                        }else if( data==2 ){
                            label = 'label-warning';
                            data = '发放中';
                        }else if( data==3 ){
                            label = 'label-default';
                            data = '上级审核';
                        }else if( data==4 ){
                            label = 'label-danger';
                            data = '后台审核';
                        }else if( data==5 ){
                            label = 'label-default';
                            data = '已取消';
                        }else if( data==6 ){
                            label = 'label-default';
                            data = '不符合条件';
                        }else if( data==7 ){
                            label = 'label-default';
                            data = '非分红周期';
                        }
                        return app.getLabelHtml(data, label);
                    }
                },
                {
                    'targets': -4,
                    "render": function (data, type, row) {
                        var label = 'label-warning';
                        var text = 'B线[比例模式]';
                        if (row.type == 1 ) {
                            label = 'label-success';
                            text = 'A线[佣金模式]';
                        }
                        return app.getLabelHtml(text, label);
                    }
                },
                {
                    'targets': -3,
                    "render": function (data, type, row) {
                        if( row.type == 1 ){
                            return '';
                        }
                        var label = 'label-warning';
                        var text = '不累计';
                        if (row.mode == 1 ) {
                            label = 'label-success';
                            text = '累计';
                        }
                        return app.getLabelHtml(text, label);
                    }
                },
                {
                    'targets': -1,
                    "render": function (data, type, row) {
                        var str = '';
                        @if(Gate::check('dividendreport/detail'))
                            str += '<a href="javascript:;" data-id="' + row.id + '" class="X-Small btn-xs text-info dividend_detail">明细</a>';
                        @endif
                        @if(Gate::check('dividendreport/check'))
                            if( row.status == 4 || row.status == 3 ){
                                str += '<a href="javascript:;" data-id= "' + row.id + '"  data-old-status="' + row.status + '"  data-status="2" data-username="'+row.username+'" class="X-Small btn-xs text-primary dividend_check">通过</a>';
                                str += '<a href="javascript:;" data-id="' + row.id + '"  data-old-status="' + row.status + '" data-status="5" data-username="'+row.username+'" class="X-Small btn-xs text-primary dividend_check">拒绝</a>';
                            }
                        @endif
                        @if(Gate::check('dividendreport/delete'))
                            if( row.status == 4|| row.status == 3 ){
                                str += '<a href="javascript:;" data-id="' + row.id + '" class="X-Small btn-xs text-info dividend_delete">删除</a>';
                            }
                        @endif
                        return str;
                    }
                }
            ],

            @if(Gate::check('dividendreport/check') || Gate::check('dividendreport/delete'))
            dom: "<'row'<'col-sm-6'Bl><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
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
            loadFadeOut();
        });

        table.on('xhr.dt', function (e, settings, json, xhr) {
            if (json !== undefined && json !== null) {
                if(typeof json['sum_amount'] == 'object'){
                    $("#total_sum").find('th').eq(1).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['total_profit']), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(3).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['amount']), 'text-red', true)
                    );
                }else{
                    $("#total_sum").find('th').eq(1).html('');
                    $("#total_sum").find('th').eq(3).html('');
                }
            }
        });

        $('#search_btn').click(function () {
            event.preventDefault();
            table.ajax.reload();
        });

        //计算合计
        $("select[name='calculate_total']").change(function () {
            if ($(this).val() == 1) {
                $('#total_sum').hide();
            } else {
                $('#total_sum').show();
            }
        });

        //单个明细
        $("table").delegate('.dividend_detail', 'click', function () {
            var id = $(this).attr('data-id');
            console.log(id);
            loadShow();
            $.ajax({
                url: "/dividendreport/detail",
                dataType: "html",
                method: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    id: id
                },
                success: function(html){
                    console.log(html);
                    $("#modal_detail  .modal-content").html(html);
                    $('#modal_detail').modal('show');
                    loadFadeOut();

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                    if(textStatus == 'Forbidden'){
                        app.bootoast("明细访问权限不足！",'danger',5000);
                    }else{
                        app.bootoast(textStatus,'danger',5000);
                    }
                    window.location.reload();
                }
            });
        });


        //单个审核
        $("table").delegate('.dividend_check', 'click', function () {
            var id = $(this).attr('data-id');
            var old_status = $(this).attr('data-old-status');//原先的状态
            var status = $(this).attr('data-status');
            var username = $(this).attr('data-username');
            if( old_status == 3 ){
                $('#old_status_name').text('上级审核');
            }else if(old_status == 4){
                $('#old_status_name').text('后台审核');
            }else{
                $('#old_status_name').text('未知');
            }
            if( status == 2 ){
                $('#modal_check').children('.modal-dialog').removeClass('modal-danger').addClass('modal-success');
                $('#status_title').text('通过');
                $('#modal_submit').removeClass('btn-danger').addClass('btn-success');
            }else{
                $('#modal_check').children('.modal-dialog').removeClass('modal-success').addClass('modal-danger');
                $('#status_title').text('拒绝');
                $('#modal_submit').removeClass('btn-success').addClass('btn-danger');
            }
            $('#modal_username').text(username);
            $('#checkform_id').val(id);
            $('#checkform_status').val(status);

            $('#modal_check').modal('show');
        });
        //单个删除
        $("table").delegate('.dividend_delete', 'click', function () {
            $('#delete_id').val($(this).attr('data-id'));
            $('#modal_delete').modal('show');
        });

        //批量删除
        $("#delete_by_select").bind('click', function () {
            $("#modal-form  .modal-dialog").removeClass('modal-success').addClass('modal-danger');
            $('#modal_submit_by_select').removeClass('btn-success').addClass('btn-danger');
            $('#batch_pro_type').val(3);
            var select_rows = table.rows({selected: true});
            var id_array = select_rows.data().pluck('id').toArray();
            if (id_array.length == 0) {
                $("#tips_content").html("请选择需要删除的记录");
                $("#modal-msg").modal();
                return false;
            }
            $("#delete_tips_content").html("要批量删除选中的 <b> "+id_array.length+" </b>条记录吗？");
            $('#select_ids').val(id_array.join(','));
            $('.modal_form').attr('action', '/dividendreport/delete?delete_by=select');
            $("#modal-form").modal();
        });

        //批量确认通过
        $("#check_by_select").bind('click', function () {
            $("#modal-form  .modal-dialog").removeClass('modal-danger').addClass('modal-success');
            $('#modal_submit_by_select').removeClass('btn-danger').addClass('btn-success');
            $('#batch_pro_type').val(1);
            var select_rows = table.rows({selected: true});
            var id_array = select_rows.data().pluck('id').toArray();
            if (id_array.length == 0) {
                $("#tips_content").html("请选择需要确认的记录");
                $("#modal-msg").modal();
                return false;
            }
            $("#delete_tips_content").html("要批量通过选中的 <b>"+id_array.length+"</b> 条记录吗？");
            $('#select_ids').val(id_array.join(','));
            $('.modal_form').attr('action', '/dividendreport/check?check_by=select');
            $("#modal-form").modal();
        });
        //批量拒绝
        $("#refuse_by_select").bind('click', function () {
            $("#modal-form  .modal-dialog").removeClass('modal-success').addClass('modal-danger');
            $('#modal_submit_by_select').removeClass('btn-success').addClass('btn-danger');
            $('#batch_pro_type').val(2);
            var select_rows = table.rows({selected: true});
            var id_array = select_rows.data().pluck('id').toArray();
            if (id_array.length == 0) {
                $("#tips_content").html("请选择需要确认的记录");
                $("#modal-msg").modal();
                return false;
            }
            $("#delete_tips_content").html("要批量拒绝选中的 <b>"+id_array.length+"</b> 条记录吗？");
            $('#select_ids').val(id_array.join(','));
            $('.modal_form').attr('action', '/dividendreport/check?check_by=select&status=3');
            $("#modal-form").modal();
        });
    });

    /**
     * 批量处理
     */
    function batch_processing(evt){
        var after_status,ajax_data,ajax_method,ajax_url,batch_pro_type;
        evt.preventDefault();
        evt.returnValue = false; // 兼容IE6~8
        batch_pro_type = $('#batch_pro_type').val();
        console.log($('#batch_pro_type').val());
        $('#modal-form').modal('hide');
        loadShow();
        if(batch_pro_type == 3){
            ajax_url = '/dividendreport/';
            ajax_method = 'delete';
            ajax_data = {
                'id': $('#select_ids').val(),
            };
        }else if ( batch_pro_type == 1 || batch_pro_type == 2){
            if(batch_pro_type == 1){
                after_status = 2;
            }else{
                after_status = 5;
            }
            ajax_url = '/dividendreport/check';
            ajax_method = 'put';
            ajax_data = {
                'id': $('#select_ids').val(),
                'status': after_status,
            };
        }else{
            loadFadeOut();
            return app.bootoast('无效操作'+batch_pro_type,'danger');
        }
        $.ajax({
            url: ajax_url,
            dataType: "json",
            method: ajax_method,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: ajax_data
        }).done(function (json) {
            loadFadeOut();
            if (json.hasOwnProperty('code') && json.code == '302') {
                window.location.reload();
            }
            table.ajax.reload();
            // 清空数据
            $('#select_ids').val('');
            app.bootoast(json.msg,(json.status==0)?'success':'danger');
        });

        return false;
    }
    function delete_row(  evt){
        evt.preventDefault();
        evt.returnValue = false; // 兼容IE6~8

        $('#modal_delete').modal('hide');

        loadShow();
        $.ajax({
            url: '/dividendreport/',
            dataType: "json",
            method: "delete",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                'id': $('#delete_id').val(),
            }
        }).done(function (json) {
            loadFadeOut();
            if (json.hasOwnProperty('code') && json.code == '302') {
                window.location.reload();
            }
            table.ajax.reload();

            // 清空数据
            $('#delete_id').val('');

            app.bootoast(json.msg,(json.status==0)?'success':'danger');
        });

        return false;
    }


    function changeStatus(  evt){
        evt.preventDefault();
        evt.returnValue = false; // 兼容IE6~8
        $('#modal_check').modal('hide');
        console.log($('#checkform_status').val());
        loadShow();
        $.ajax({
            url: "/dividendreport/check",
            dataType: "json",
            method: "put",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                'id': $('#checkform_id').val(),
                'status': $('#checkform_status').val(),
            }
        }).done(function (json) {
            loadFadeOut();
            if (json.hasOwnProperty('code') && json.code == '302') {
                window.location.reload();
            }
            table.ajax.reload();

            // 清空数据
            $('#checkform_id').val('');
            $('#checkform_status').val('');

            app.bootoast(json.msg,(json.status==0)?'success':'danger');
        });

        return false;
    }



</script>

@stop
