@extends('layouts.base')

@section('title','充值申请')
@section('function','充值申请')
@section('function_link', '/deposit/')
@section('here','申请列表')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/deposit/" method="POST">
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
                    <div class="col-md-2 app_vtc_align_baseline">
                        <div class="form-group">
                            <label class="col-sm-9">通道<span style="color: darkgray;">（按住 Ctrl 键多选）</span></label>
                            <div class="col-sm-9">
                                <select multiple="multiple" class="form-control" name='payment_channel_id[]' style="min-height:162px;min-width: 230px;">
                                    @foreach($banks as $b)
                                        <option class=" @if($b->status == false) text-danger @endif"
                                                value="{{$b->id}}">{{$b->cate_name}}-{{$b->name}}
                                            @if($b->status  == false)
                                                禁用
                                            @endif
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-2 control-label">会员</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name='username' placeholder="会员名" />
                                </div>
                                <div class="col-sm-4">
                                    <select name="include_next_level" class="form-control">
                                        <option value="0" checked="checked">当前用户</option>
                                        <option value="1" checked="checked">包含直属下级</option>
                                        <option value="2">包含所有下级</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="postscript" class="col-sm-2 control-label">订单号</label>
                                <div class="col-sm-10">
                                    <input type="text" value="{{request()->get('order_id','')}}" class="form-control" name='order_id' placeholder="订单号"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="postscript" class="col-sm-2 control-label">全部总计</label>
                                <div class="col-sm-10">
                                    <select name="calculate_total" class="form-control">
                                        <option value="1" checked="checked">否</option>
                                        <option value="2">是</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                            <div class="form-group">
                                <label for="done_start_date" class="col-sm-3 control-label">到账时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="done_start_date"
                                               id='done_start_date' value="" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="done_end_date"
                                               id='done_end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="deal_start_date" class="col-sm-3 control-label">审核时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="deal_start_date"
                                               id='deal_start_date' value="" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="deal_end_date"
                                               id='deal_end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="-1">全部</option>
                                        <option value="0" selected>充值中</option>
                                        <option value="1">已审核</option>
                                        <option value="2">充值成功</option>
                                        <option value="3">充值失败</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  for="postscript" class="col-sm-3 control-label">用户组</label>
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
                        <button type="reset" class="btn btn-default margin">
                            <i class="fa fa-refresh" aria-hidden="true"></i>重置
                        </button>
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
                    <audio id="mediaElementID" style="height: 10px" src="/assets/sound/xxtk.mp3" preload="auto" controls></audio></label>
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
                        <th class="hidden-sm" data-sortable="false">状态</th>
                        <th class="hidden-sm" data-sortable="false">会员</th>
                        <th class="hidden-sm" data-sortable="false">总代</th>
                        <th class="hidden-sm" data-sortable="false">用户组</th>
                        <th class="hidden-sm" data-sortable="false">渠道</th>
                        <th class="hidden-sm" data-sortable="false">通道</th>
                        <th class="hidden-sm" data-sortable="false">金额</th>
                        <th class="hidden-sm" data-sortable="false">虚拟币金额</th>
                        <th class="hidden-sm" data-sortable="false">汇率</th>
                        <th class="hidden-sm" data-sortable="false">用户手续费</th>
                        <th class="hidden-sm" data-sortable="false">人工附言/备注</th>
                        <th class="hidden-sm" data-sortable="false">申请时间</th>
                        <th class="hidden-sm" data-sortable="false">审核时间</th>
                        <th class="hidden-sm" data-sortable="false">到账时间</th>
                        <th class="hidden-sm" data-sortable="false">会计</th>
                        <th class="hidden-sm" data-sortable="false">出纳</th>
                        <th class="hidden-sm" data-sortable="false">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-right"><b>本页总计： </b></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="8"></th>
                        </tr>
                        <tr id="total_sum" style="display: none;">
                            <th colspan="8" class="text-right"><b>全部总计： </b></th>
                            <th></th>
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
                    <h4 class="modal-title">人工处理</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-lg deal_btn deal_action btn-success" id="add-limit-submit">确定</button>

                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Select/css/select.dataTables.min.css"/>
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>

    <script>
        laydate.skin('lynn');
        var layConfig = {
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        var input_date_field = ['#start_date','#end_date','#done_start_date','#done_end_date','#deal_start_date','#deal_end_date'];
        for( var x in input_date_field ){
            layConfig.elem = input_date_field[x];
            laydate(layConfig);
        }

        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'include_next_level': $("select[name='include_next_level']").val(),
                    'order_id': $("input[name='order_id']").val(),
                    'status': $("select[name='status']").val(),
                    'payment_channel_id' : $('select[name="payment_channel_id[]"]').val(),
                    'start_date':$("#start_date").val(),        //申请时间start
                    'end_date':$("#end_date").val(),            //申请时间end
                    'done_start_date': $("input[name='done_start_date']").val(),    //到账时间start
                    'done_end_date': $("input[name='done_end_date']").val(),        //到账时间end
                    'deal_start_date': $("input[name='deal_start_date']").val(),    //到账时间start
                    'deal_end_date': $("input[name='deal_end_date']").val(),        //到账时间end
                    'calculate_total'    : $('select[name="calculate_total"]').val(),
                    'user_group_id'    : $('select[name="user_group_id"]').val(), //用户组
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                pageLength: 50,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"visible": false},
                    {"data": "id"},
                    {"data": "status"},
                    {"data": "username"},
                    {"data": "top_username"},
                    {"data": "user_group_name"},
                    {"data": "payment_category_name"},
                    {"data": "payment_channel_name"},
                    {"data": "amount"},
                    {"data": "third_amount"},
                    {"data": "rate"},
                    {"data": "user_fee"},
                    {"data": "remark"},
                    {"data": "created_at"},
                    {"data": "deal_at"},
                    {"data": "done_at"},
                    {"data": "accountant_admin"},
                    {"data": "cash_admin"},
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
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '';
                            if (row.status == 0) {
                                @if(Gate::check('deposit/deal'))
                                    str += '<a class="btn btn-warning btn-xs deal" dealing="' + row.accountant_admin + '" attr="' + row.id + '" attr_status="' + row.status + '">人工审核</a>';
                                @endif
                            } else if (row.status == 1) {
                                @if(Gate::check('deposit/carry'))
                                    str += '<a class="btn btn-danger btn-xs deal" dealing="' + row.cash_admin + '" attr="' + row.id + '" attr_status="' + row.status + '">确定充值</a>';
                                @endif
                            } else {
                                str += '<a class="btn btn-xs detail" attr="' + row.id + '">明细</a>';
                            }
                            return str;
                        }
                    },
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            return app.renderHtml({
                                'html':'span',
                                'attr':'title='+row.id,
                                'text':row.id_encode,
                            });
                        }
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            var status_label = 'danger';
                            if (row['status'] == 0) {
                                status_label = "primary";
                            } else if (row['status'] == 1) {
                                status_label = "warning";
                            } else if (row['status'] == 2) {
                                status_label = "success";
                            } else if (row['status'] == 3) {
                                status_label = "danger";
                            }
                            return app.getLabelHtml(
                                row['status_label'],
                                'label-' + status_label
                            );
                        }
                    },
                    {
                        'targets': 3,
                        'render': function (data, type, row) {
                            if (row['user_observe']) {
                                return app.getColorHtml(row.username, 'label-danger', false);
                            } else {
                                return row.username;
                            }
                        }
                    },
                    {
                        'targets': 12,
                        'render': function (data, type, row) {
                            return row.manual_postscript + '/' + data;
                        }
                    }
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                    var money = 0;
                    var user_fee = 0;

                    for(item in data){
                        money    += parseFloat(data[item].amount);
                        user_fee += parseFloat(data[item].user_fee);
                    }

                    $(tfoot).find('th').eq(1).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(money), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(4).html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(user_fee), 'text-green', true)
                    );

                }
            });
            //打开明细窗口
            $("table").delegate('.detail', 'click', function () {
                var id = $(this).attr('attr');

                loadShow();
                $.ajax({
                    url: "/deposit/detail?id=" + id,
                    dataType: "json",
                    method: "get",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                }).done(function (json) {
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        $(".deal_action").hide();
                        $("#modal-deal").removeClass("modal-success").addClass("modal-default");
                        $("#modal-deal .modal-title").html('订单明细');
                        $("#modal-deal .modal-body").html(json.data);
                        $("#modal-deal").modal({backdrop: 'static', keyboard: false});
                    } else {
                        bootoast({
                            message: json.msg,
                            type: 'danger',
                            position: 'top-center',
                            timeout: 8,
                            animationDuration: 300,
                            dismissable: true
                        });
                    }
                });
            });
            //打开人工处理窗口
            $("table").delegate('.deal', 'click', function () {
                if ($(this).attr("dealing") != "" || confirm("确认进行人工处理吗？")) {
                    var id = $(this).attr('attr');
                    var curr_status = $(this).attr('attr_status');
                    var curr_url = "/deposit/deal?id=" + id;
                    if(curr_status == 1){
                        curr_url = "/deposit/carry?id=" + id;
                    }
                    loadShow();
                    $.ajax({
                        url: curr_url,
                        dataType: "json",
                        method: "POST",
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
                            $(".deal_action").show();
                            $("#modal-deal").removeClass("modal-success").addClass("modal-default");
                            $("#modal-deal .modal-title").html('人工处理');
                            $("#modal-deal .modal-body").html(json.data);
                            $("#modal-deal").modal({backdrop: 'static', keyboard: false});
                        } else {
                            bootoast({
                                message: json.msg,
                                type: 'danger',
                                position: 'top-center',
                                timeout: 8,
                                animationDuration: 300,
                                dismissable: true
                            });
                        }
                    });

                    new Clipboard('.copy'); //复制到剪贴板
                }
            });
            //财务处理
            $(".deal_btn").click(function () {
                var _this = $(this);
                var depositId = $("input[name='deposit_id']").val();
                var depositStatus = $("input[name='deposit_status']").val();//充值申请状态
                var putData = '';
                var putUrl = '';
                //入帐操作
                if (depositStatus == 1) {
                    putUrl = "/deposit/carry?id=" + depositId;
                    var dealResult = $("#modal-deal :checked[name='deal_result']").val();
                    if (typeof(dealResult) == "undefined") {
                        app.bootoast("请选择审核结果！");
                        return false;
                    }
                    if (dealResult == 'refused') {
                        if ($("input[name='refused_reason']").val() == '') {
                            app.bootoast("请填写或者选择拒绝原因！");
                            return false;
                        }
                    }
                    if ($("#modal-deal textarea[name='remark']").val() == '') {
                        app.bootoast("请输入备注");
                        return false;
                    }
                    if (!confirm("确认审核【" + (dealResult == 'passed' ? '通过' : '拒绝') + "】该笔充值吗？")) {
                        return false;
                    }
                    putData = {
                        id: depositId,
                        deal_result: dealResult,
                        remark: $("#modal-deal textarea[name='remark']").val(),
                        refused_reason: $("input[name='refused_reason']").val()
                    };
                } else if (depositStatus == 0) {//审核操作
                    putUrl = "/deposit/deal?id=" + depositId;
                    if ($("input[name='deal_amount']").val() == '') {
                        //alert("请输入金额");
                        app.bootoast("请输入金额！");
                        return false;
                    }
                    if ($("input[name='deal_fee']").val() == '') {
                        //alert("请输入手续费");
                        app.bootoast("请输入手续费！");
                        return false;
                    }
                    if ($("input[name='deal_postscript']").val() == '') {
                        //alert("请输入附言");
                        app.bootoast("请输入附言！");
                        return false;
                    }
                    if ($("input[name='bank_order_no']").val() == '') {
                        //alert("请输入附言");
                        app.bootoast("请输入外部流水【银行或是第三方交易流水号】！");
                        return false;
                    }
                    putData = {
                        id: depositId,
                        amount: $("input[name='deal_amount']").val(),
                        fee: $("input[name='deal_fee']").val(),
                        postscript: $("input[name='deal_postscript']").val(),
                        bank_order_no: $("input[name='bank_order_no']").val()
                    };
                } else {
                    app.bootoast("无效的状态！");
                    return false;
                }
                _this.prop("disabled", true);
                loadShow();
                $.ajax({
                    url: putUrl,
                    dataType: "json",
                    method: "PUT",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: putData
                }).done(function (json) {
                    _this.prop("disabled", false);
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.status == 0) {
                        bootoast({
                            message: "操作成功！",
                            type: 'success',
                            position: 'top-center',
                            timeout: 5,
                            animationDuration: 300,
                            dismissable: true
                        });
                        table.ajax.reload();
                        $('#modal-deal').modal('hide');
                    } else {
                        bootoast({
                            message: json.msg,
                            type: 'danger',
                            position: 'top-center',
                            timeout: 8,
                            animationDuration: 300,
                            dismissable: true
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
                if (json !== undefined && json !== null ) {
                    if( typeof json['sum_amount'] == 'object'){
                        var sum_money = json['sum_amount']['sum_money'];
                        var sum_user_fee = json['sum_amount']['sum_user_fee'];
                        $("#total_sum").find('th').eq(1).html(
                            app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_money), 'text-red', true)
                        );
                        $("#total_sum").find('th').eq(4).html(
                            app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(sum_user_fee), 'text-green', true)
                        );
                    }else{
                        $("#total_sum").find('th').eq(1).html('');
                        $("#total_sum").find('th').eq(4).html('');
                    }

                }
            });

            $('#search_btn').click(function (event) {
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
            //计算合计
            $("select[name='calculate_total']").change(function () {
                if ($(this).val() == 1) {
                    $('#total_sum').hide();
                } else {
                    $('#total_sum').show();
                }
            });
            table.on('xhr.dt', function ( e, settings, json, xhr ) {
                if($("select[name='status']").val()==0 || $("select[name='status']").val()==-1) {
                    var mediaElement = document.getElementById('mediaElementID');
                    if(json.alert){
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
    </script>
@stop
