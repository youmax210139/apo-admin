@extends('layouts.base')

@section('title','提现申请')
@section('function','提现申请')
@section('function_link', '/withdrawal/')
@section('here','提现申请')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/withdrawal/" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="export" value="1"/>
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
                                <label for="start_date" class="col-sm-3 control-label">申请时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input autocomplete="off" type="text" class="form-control form_datetime" name="start_date"
                                               id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input autocomplete="off"  type="text" class="form-control form_datetime" name="end_date"
                                               id='end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="risk_start_date" class="col-sm-3 control-label">风控时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input  autocomplete="off" type="text" class="form-control form_datetime" name="risk_start_date"
                                               id='risk_start_date' value="" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input  autocomplete="off" type="text" class="form-control form_datetime" name="risk_end_date"
                                               id='risk_end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="done_start_date" class="col-sm-3 control-label">出款时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input  autocomplete="off" type="text" class="form-control form_datetime" name="done_start_date"
                                               id='done_start_date' value="" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input  autocomplete="off" type="text" class="form-control form_datetime" name="done_end_date"
                                               id='done_end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">会员</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name='username' placeholder="会员名"/>
                                </div>
                                <div class="col-sm-4">
                                    <select id="agent" name="agent" class="form-control">
                                        <option value="">选择总代</option>
                                        @foreach($zongdai_list as $b)
                                            <option value="{{$b->username}}">{{$b->username}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="operate_type" class="col-sm-3 control-label">提现类型</label>
                                <div class="col-sm-9">
                                    <select name="operate_type" class="form-control">
                                        <option value="">全部</option>
                                        <option value="1">手工提现</option>
                                        <option value="2">第三方提现</option>
                                        <option value="3">软件提现</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="withdrawal_id" class="col-sm-3 control-label">订单号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='withdrawal_id' placeholder="订单号"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ip" class="col-sm-3 control-label">IP</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='ip' placeholder="IP"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bank" class="col-sm-3 control-label">虚拟货币类型</label>
                                <div class="col-sm-9">
                                    <select name="bank" class="form-control">
                                        <option value="">请选择虚拟货币类型</option>
                                        @foreach($banks as $b)
                                            <option value="{{$b->id}}">{{$b->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cashier" class="col-sm-3 control-label">出纳员</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='cashier' placeholder="出纳员"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="calculate_total" class="col-sm-5 control-label">全部总计</label>
                                <div class="col-sm-7">
                                    <select name="calculate_total" class="form-control">
                                        <option value="1" checked="checked">否</option>
                                        <option value="2">是</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="status" class="col-sm-5 control-label">出款状态</label>
                                <div class="col-sm-7">
                                    <select name="status" class="form-control">
                                        <option value="-1">全部</option>
                                        @foreach($status_labels as $status_id => $status_label)
                                            <option value="{{$status_id}}"
                                                    @if($status_id == 0) selected @endif>{{$status_label}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="risk_status" class="col-sm-5 control-label">风控审核</label>
                                <div class="col-sm-7">
                                    <select name="risk_status" class="form-control">
                                        <option value="-1">全部</option>
                                        @foreach($risk_status_labels as $risk_status_id => $risk_status_label)
                                            <option value="{{$risk_status_id}}"
                                                    @if($risk_status_id == 1) selected @endif>{{$risk_status_label}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">用户组</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="0">所有组别</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}" @if($item->id==1) selected @endif>{{ $item->name }}</option>
                                        @endforeach
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
                        <button type="submit" class="btn btn-warning margin" id="export_btn">
                            <i class="fa fa-download" aria-hidden="true"></i>导出
                        </button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="auto_refresh" checked>
                    <select name="refresh_delay">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="30" selected>30</option>
                        <option value="60">60</option>
                    </select>秒自动刷新
                    <audio id="mediaElementID" style="height: 10px" src="/assets/sound/tksq.mp3" preload="auto" controls></audio></label>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false"></th>
                            <th class="hidden-sm" data-sortable="false">订单号</th>
                            <th class="hidden-sm" data-sortable="false">会员</th>
                            <th class="hidden-sm" data-sortable="false">总代</th>
                            <th class="hidden-sm" data-sortable="false">用户组</th>
                            <th width="100" class="hidden-sm" data-sortable="false">银行</th>
                            <th class="hidden-sm" data-sortable="false">开户名</th>
                            <th class="hidden-sm" data-sortable="false">银行卡号</th>
                            <th class="hidden-sm" data-sortable="true">金额</th>
                            <th class="hidden-sm" data-sortable="true">实际出款金额</th>
                            <th class="hidden-sm" data-sortable="true">实际虚拟币金额</th>
                            <th class="hidden-sm" data-sortable="true">汇率</th>
                            <th width="130" class="hidden-sm" data-sortable="true">申请时间</th>
                            <th class="hidden-sm" data-sortable="false">风险审核</th>
                            <th class="hidden-sm" data-sortable="false">风控</th>
                            <th width="130" class="hidden-sm" data-sortable="true">风控时间</th>
                            <th width="130" class="hidden-sm" data-sortable="false">风控耗时</th>
                            <th width="90" class="hidden-sm" data-sortable="false">出款状态</th>
                            <th width="130" class="hidden-sm" data-sortable="true">出款时间</th>
                            <th width="130" class="hidden-sm" data-sortable="false">出款耗时</th>
                            <th class="hidden-sm" data-sortable="false">出纳</th>
                            <th class="hidden-sm" style="min-width: 120px" data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8" class="text-right"><b>本页总计： </b></th>
                                <th></th>
                                <th></th>
                                <th colspan="8"></th>
                            </tr>
                            <tr id="total_sum" style="display: none;">
                                <th colspan="8" class="text-right"><b>全部总计： </b></th>
                                <th></th>
                                <th></th>
                                <th colspan="8"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
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
                    <h4 class="modal-title">手动出款</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer" style="text-align: center">
                    <div class="deal_action">
                        <button type="button" flag="1" style="float: left" class="btn btn-success btn-lg deal_btn">出款成功</button>
                        <button type="button" flag="2" style="float: right"  class="btn btn-danger btn-lg  deal_btn">出款失败</button>
                    </div>
                    <div class="dealthird_action">
                        <button type="button" class="btn btn-success btn-lg dealthird_btn">提交至第三方</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-claim" tabIndex="-1">
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
                        确认认领该笔出款吗？
                    </p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="modal-claim-id">
                    <input type="hidden" id="modal-claim-flag">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-success" id="modal-claim-confirm">
                        <i class="fa fa-check-circle-o"></i> 确认
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Select/css/select.dataTables.min.css"/>
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>
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
        var input_date_field = ['#start_date','#end_date','#done_start_date','#done_end_date','#risk_start_date','#risk_end_date'];
        for( var x in input_date_field ){
            layConfig.elem = input_date_field[x];
            laydate(layConfig);
        }
        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'agent': $("select[name='agent']").val(),
                    'status': $("select[name='status']").val(),
                    'risk_status': $("select[name='risk_status']").val(),
                    'bank': $("select[name='bank']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'done_start_date': $("input[name='done_start_date']").val(),
                    'done_end_date': $("input[name='done_end_date']").val(),
                    'risk_start_date': $("input[name='risk_start_date']").val(),
                    'risk_end_date': $("input[name='risk_end_date']").val(),
                    'operate_type': $("select[name='operate_type']").val(),
                    'ip': $("input[name='ip']").val(),
                    'cashier': $("input[name='cashier']").val(),
                    'calculate_total': $('select[name="calculate_total"]').val(),
                    'withdrawal_id':$("input[name='withdrawal_id']").val(),
                    'user_group_id':$('select[name="user_group_id"]').val(), //用户组
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
                    {"data": "id"},
                    {"data": "username"},
                    {"data": "top_username"},
                    {"data": "user_group_name"},
                    {"data": "bank_name"},
                    {"data": "bank_account_name"},
                    {"data": "bank_account"},
                    {"data": "amount"},
                    {"data": "amount"},
                    {"data": "real_third_amount"},
                    {"data": "virtual_rate"},
                    {"data": "created_at"},
                    {"data": "risk_status"},
                    {"data": "verifier_username"},
                    {"data": "risk_done_at"},
                    {"data": "risk_done_time_consuming"},
                    {"data": "status"},
                    {"data": "done_at"},
                    {"data": "done_time_consuming"},
                    {"data": "cashier_username"},
                    {"data": "action"}
                ],
                createdRow: function (row, data, index) {
                    if (data['amount'] >= parseInt({{get_config('op_alert_amount',30000)}})) {
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
                        'targets': 9,
                        'render': function (data, type, row) {
                            if( row['status'] != 1 ){
                                return ;
                            }
                            return Number(parseFloat(data) + parseFloat(row['user_fee'])).toFixed(2);
                        }
                    },
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '------';

                            if (row.risk_status == 1) {
                                if (row.status == 0 || row.status == 5  || (row.status == 3 && row.cashier_username == '{{auth()->user()->username}}' )   ) {//等待出款或是自动出款失败需要重新出款
                                    str = '<span style="margin-right: 10px;cursor: pointer" dealing="' + row.cashier_username + '" attr="' + row.id + '" href="javascript:;" class=" btn btn-sm btn-info deal">人工</span>';
                                    if (row.is_third) {
                                        str += '<span style="margin-right: 10px;cursor: pointer" dealing="' + row.cashier_username + '" attr="' + row.id + '" href="javascript:;" class="btn btn-sm btn-default dealthird">第三方</span>';
                                    }
                                } else if (row.status == 1 || row.status == 2) {
                                    str = '<a attr="' + row.id + '" href="#" class="X-Small btn-xs text-primary detail">详情</a>';
                                } else if (row.status == 3 && row.cashier_username != '{{auth()->user()->username}}'){
                                    str = '<span class="text-primary">' + row.cashier_username + ' 操作中</span>';
                                } else if (row.status == 4) {
                                    if(row.operate_type == 1){
                                        str = row.cashier_username + " 出款中";
                                    }else if(row.operate_type == 2){
                                        if(row.operate_status == 11){
                                            //如果看到这一条说明 operate_status 与 status 没有一起更新
                                            str = '<span class="text-success">' + row.third_name + ' 出款成功</a>';
                                        }else if (row.operate_status == 12){
                                            //如果看到这一条说明 operate_status 与 status 没有一起更新
                                            str = '<span class="text-danger">' + row.third_name + ' 出款失败</a>';
                                        }else if (row.operate_status == 13){
                                            if (row.third_check_count > 0) {
                                                str = '<span class="text-info">' + row.third_name + ' 确认(' + row.third_check_count + ')次';
                                            } else {
                                                str = '<span class="text-success">' + row.third_name + ' 等待确认';
                                            }
                                        }else if (row.operate_status == 14){
                                            //如果看到这一条说明 operate_status 与 status 没有一起更新
                                            str = '<span class="text-danger">' + row.third_name + ' 提交失败</a>';
                                        }else if (row.operate_status == 15){
                                            if (row.third_add_count > 0) {
                                                str = '<span class="text-info">' + row.third_name + ' 尝试' + row.third_add_count + '次';
                                            } else {
                                                str = '<span class="text-success">' + row.third_name + ' 等待提交';
                                            }
                                        }else{
                                            str = '<span class="text-danger">' + '无效的处理状态['+row.operate_status+']';
                                        }
                                    }else{
                                        str = '<span class="text-danger">' + '无效的处理类型['+row.operate_type+']';
                                    }
                                }else {
                                    str = '<span class="text-danger">' + '无效的状态['+row.status+']';
                                }
                            }else if (row.risk_status == 2) {
                                str = '<a attr="' + row.id + '" href="javascript:;" class="X-Small btn-xs text-primary reason" data-refused_msg="'+row.refused_msg+'" data-risk_remark="'+row.risk_remark+'">原因</a>';
                            }
                            return str;
                        }
                    },
                    {
                        'targets': -5,
                        'render': function (data, type, row) {
                            var status_txt = row['status_label'];
                            var status_level = 'warning ';
                            if (row['status'] == 0 || row['status'] == 5 ) {
                                status_level = 'warning';
                            } else if (row['status'] == 1) {
                                status_level = 'success';
                            } else if (row['status'] == 2) {
                                status_level = "danger";
                            } else if (row['status'] == 3) {
                                status_level = "primary";
                            } else if (row['status'] == 4) {
                                status_level = "info";
                            } else{
                                status_level = "danger";
                                status_txt = "未知状态";
                            }

                            if( row['third_name'] != null && row['third_name']!= '' && row['operate_type'] == '2' ){
                                status_txt = row['third_name']+status_txt;
                            }

                            return app.getLabelHtml(
                                status_txt,
                                'label-' + status_level
                            );
                        }
                    },
                    {
                        'targets': -9,
                        'render': function (data, type, row) {
                            var status_txt = row['risk_status_label'];
                            var status_level = 'danger';
                            if (row['risk_status'] == 0) {
                                status_level = 'warning';
                            } else if (row['risk_status'] == 1) {
                                status_level = 'success';
                            } else if (row['risk_status'] == 2) {
                                status_level = "danger";
                            } else if (row['risk_status'] == 3) {
                                status_level = "primary";
                            }
                            return app.getLabelHtml(
                                status_txt,
                                'label-' + status_level
                            );
                        },
                    },
                    {
                        'targets':-6,
                        'render':function(data,type,row){
                            if (row.risk_done_at && typeof row.done_at != "undefined"){
                                var time_consuming = compare_time(row.created_at,row.risk_done_at);
                                return time_consuming;
                            }
                            return '';

                        }
                    },
                    {
                        'targets':-3,
                        'render':function(data,type,row){
                            if (row.done_at && typeof row.done_at != "undefined"){

                                var time_consuming = compare_time(row.risk_done_at,row.done_at);
                                return time_consuming;
                            }
                            return '';
                        }
                    }
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                    var money = 0;
                    var real_money = 0;
                    for(item in data){
                        money    += parseFloat(data[item].amount);
                        if( data[item].status == 1 ){
                            real_money += parseFloat(data[item].amount) + parseFloat(data[item].user_fee);
                        }
                    }
                    $(tfoot).find('th').eq(1).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(money), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(2).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(real_money), 'text-red', true)
                    );
                }
            });
            $("table").delegate('.deal', 'click', function () {
                var id = $(this).attr('attr');
                if ($(this).attr("dealing") != "" ) {
                    confirmDeal(id);
                    return ;
                }

                $('#modal-claim-id').val(id);
                $('#modal-claim-flag').val('deal');
                $('#modal-claim').modal('show');
            });
            $("table").delegate('.reason', 'click', function () {
                var msg = "<span style='color:red'>风控拒绝原因：</span>"+$(this).attr('data-refused_msg')+"<br/><span style='color:red'>风控备注：</span>"+$(this).attr('data-risk_remark');
                BootstrapDialog.alert(msg);
            });
            $(".deal_btn").click(function () {
                var _this = $(this);
                var flag = $(this).attr('flag');
                if (flag == 2) {
                    if ($("textarea[name='remark']").val() == '') {
                        BootstrapDialog.alert('请输入备注!');
                        return false;
                    }
                } else if (flag == 1) {
                    if ($("select[name='bank_id']").val() == '') {
                        BootstrapDialog.alert('请选择出款银行!');
                        return false;
                    }
                    if ($("input[name='bank_order_no']").val() == '') {
                        BootstrapDialog.alert('请输入外部流水号!');
                        return false;
                    }
                }


                _this.prop("disabled", true);
                loadShow();
                $.ajax({
                    url: "/withdrawal/deal?id=" + $("input[name='withdrawalid']").val(),
                    dataType: "json",
                    method: "PUT",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: $("input[name='withdrawalid']").val(),
                        user_fee: $("input[name='user_fee']").val(),
                        platform_fee:$("input[name='platform_fee']").val(),
                        remark: $("textarea[name='remark']").val(),
                        bank_order_no: $("input[name='bank_order_no']").val(),
                        bank_id: $("select[name='bank_id']").val(),
                        flag: flag,
                        user_fee_option:$("input[name='user_fee_option']").val(),
                    }
                }).done(function (json) {
                    _this.prop("disabled", false);
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {

                        $.notify({
                            title: '<strong>提示!</strong>',
                            message: '操作成功.'
                        },{
                            type: 'success'
                        });
                        table.ajax.reload();
                        $('#modal-deal').modal('hide');
                    } else {
                        BootstrapDialog.alert(json.msg);
                    }
                });
            });
            $("table").delegate('.dealthird', 'click', function () {
                var id = $(this).attr('attr');
                if ($(this).attr("dealing") != "" ) {
                    confirmDealthird(id);
                    return ;
                }

                $('#modal-claim-id').val(id);
                $('#modal-claim-flag').val('third');
                $('#modal-claim').modal('show');
            });
            $(".dealthird_btn").click(function () {
                var _this = $(this);
                if ($("select[name='withdrawalapi']").val() == '') {
                    BootstrapDialog.alert('请选择出款接口!');
                    return false;
                }
                _this.prop("disabled", true);
                loadShow();
                $.ajax({
                    url: "/withdrawal/dealthird",
                    dataType: "json",
                    method: "PUT",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: $("input[name='withdrawalid']").val(),
                        withdrawalapi: $("select[name='withdrawalapi']").val()
                    }
                }).done(function (json) {
                    _this.prop("disabled", false);
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        BootstrapDialog.show({
                            title: '操作成功',
                            message: json.msg,
                            onshown: function (dialogRef) {
                                setTimeout(function () {
                                    dialogRef.close();
                                }, 1000);
                            }
                        });
                        table.ajax.reload();
                        $('#modal-deal').modal('hide');
                    } else {
                        BootstrapDialog.alert({
                            title: '操作失败',
                            message: json.msg,
                            type: BootstrapDialog.TYPE_WARNING,
                            closable: true,
                            draggable: true,
                            buttonLabel: '关闭'
                        });
                    }
                });
            });

            $("table").delegate('.detail', 'click', function () {
                var id = $(this).attr('attr');
                loadShow();
                $.ajax({
                    url: "/withdrawal/detail",
                    dataType: "json",
                    method: "GET",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: id
                    }
                }).done(function (json) {
                    $(".dealthird_action").hide();
                    $(".deal_action").hide();
                    $("#modal-deal").removeClass("modal-success").addClass("modal-default");
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        $("#modal-deal .modal-title").html("提现纪录明细【# " + id + " 】");
                        $("#modal-deal .modal-body").html(json.data);
                        $("#modal-deal").modal({backdrop: 'static', keyboard: false});
                    } else {
                        $.notify({
                            title: '<strong>提示!</strong>',
                            message: json.msg
                        },{
                            type: 'danger'
                        });
                    }
                });
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
                if (json !== undefined && json !== null) {
                    if( typeof json['sum_amount'] == 'object'){
                        $("#total_sum").find('th').eq(1).html(
                            app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['total']), 'text-red', true)
                        );
                        $("#total_sum").find('th').eq(2).html(
                            app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(json['sum_amount']['real_amount']), 'text-red', true)
                        );
                    }else{
                        $("#total_sum").find('th').eq(1).html('');
                        $("#total_sum").find('th').eq(2).html('');
                    }

                }
            });

            $('#search_btn').click(function () {
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
            $('#export_btn').click(function(){
                $('#search').submit();
            });

            new Clipboard('.copy'); //复制到剪贴板

            //计算合计
            $("select[name='calculate_total']").change(function () {
                if ($(this).val() == 1) {
                    $('#total_sum').hide();
                } else {
                    $('#total_sum').show();
                }
            });

            function confirmDeal( id )
            {
                loadShow();
                $.ajax({
                    url: "/withdrawal/deal?id=" + id,
                    dataType: "json",
                    method: "get",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: id
                    }
                }).done(function (json) {
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        table.ajax.reload();
                        $(".dealthird_action").hide();
                        $(".deal_action").show();
                        //$("#modal-deal").removeClass("modal-success").addClass("modal-default");
                        $("#modal-deal .modal-title").html("人工出款");
                        $("#modal-deal .modal-body").html(json.data);
                        $("#modal-deal").modal({backdrop: 'static', keyboard: false});
                    } else {
                        $.notify({
                            title: '<strong>提示!</strong>',
                            message: json.msg
                        },{
                            type: 'danger'
                        });
                    }
                });
            }

            function confirmDealthird( id )
            {
                //var id = $(this).attr('attr');
                loadShow();
                $.ajax({
                    url: "/withdrawal/dealthird",
                    dataType: "json",
                    method: "GET",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: id
                    }
                }).done(function (json) {
                    $(".dealthird_action").show();
                    $(".deal_action").hide();
                    //$("#modal-deal").removeClass("modal-default").addClass("modal-success");
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        table.ajax.reload();
                        $(".deal_action").hide();
                        $("#modal-deal .modal-title").html("第三方出款");
                        $("#modal-deal .modal-body").html(json.data);
                        $("#modal-deal").modal({backdrop: 'static', keyboard: false});

                    } else {
                        $.notify({
                            title: '<strong>提示!</strong>',
                            message: json.msg
                        },{
                            type: 'danger'
                        });
                    }
                });
            }

            $('#modal-claim-confirm').click(function(){
                $('#modal-claim').modal('hide');
                var id= $('#modal-claim-id').val();
                var flag = $('#modal-claim-flag').val();
                if( id == ''){
                    BootstrapDialog.alert('ID为空!');
                    return false;
                }
                if( flag == 'third' ){
                    confirmDealthird(id);
                }else{
                    confirmDeal(id);
                }
            });
            table.on('xhr.dt', function ( e, settings, json, xhr ) {
                if($("select[name='status']").val()==0 || $("select[name='status']").val()==5) {
                    var mediaElement = document.getElementById('mediaElementID');
                    if(json.data.length>0){
                        mediaElement.play();
                    }else{
                        mediaElement.pause();
                    }
                }
            } )
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
        function compare_time(faultDate, completeTime) {
            var stime = Date.parse(new Date(faultDate));
            var etime = Date.parse(new Date(completeTime));
            // 两个时间戳相差的毫秒数
            var usedTime = etime - stime;
            if(usedTime<1){
                return '';
            }
            // 计算相差的天数
            var days = Math.floor(usedTime / (24 * 3600 * 1000));
            // 计算天数后剩余的毫秒数
            var leave1 = usedTime % (24 * 3600 * 1000);
            // 计算出小时数
            var hours = Math.floor(leave1 / (3600 * 1000));
            // 计算小时数后剩余的毫秒数
            var leave2 = leave1 % (3600 * 1000);
            // 计算相差分钟数
            var minutes = Math.floor(leave2 / (60 * 1000));
            var seconds = Math.floor(leave2 % (60 * 1000))/1000;
            var time = days + "天" + hours + "时" + minutes + "分" + seconds + '秒';
            return time;
        }
    </script>
@stop
