@extends('layouts.base')

@section('title','账变管理')
@section('function','账变管理')
@section('function_link', '/order/')
@section('here','账变列表')

@section('content')
<div class="row">
<div class="col-md-12">
    <!--搜索框 Start-->
    <div class="box box-primary">
        <form id="search" class="form-horizontal" action="/order/" method="post">
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
                <div class="col-md-2 app_vtc_align_baseline">
                    <div class="form-group">
                       <label class="control-label app_pad_btm5">账变类型<span style="color: darkgray;">（按住 Ctrl 键多选）</span></label>
                       <select multiple="multiple" class="form-control" name='order_type_ids[]' style="min-height:212px;">
                            <option value="" selected>全部</option>
                            @forelse ($order_type as $type)
                            <option value="{{ $type->id }}">@if($type->operation==1) [ + ] @elseif($type->operation==2) [ - ] @else [ h ] @endif{{ $type->name }}</option>
                            @empty
                            <option>空</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">订单编号</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name='order_no' placeholder="订单编号" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">账变时间</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="start_date" value="{{ $start_date }}"  id='start_date'   placeholder="开始时间">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="end_date" value="{{ $end_date }}"  id='end_date'  placeholder="结束时间">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label text-right app_label_pt9px">用户搜索:</label>
                        <div class="col-sm-9">
                            <div class="col-sm-6 form-control app_wauto_bnone">
                                <label class="radio-inline"><input type="radio" name="search_type" value="1" checked>手动输入</label>
                                <label class="radio-inline"><input type="radio" name="search_type" value="2">总代列表</label>
                            </div>
                            <div class="col-sm-6 form-control app_wauto_bnone">
                                <label class="checkbox-inline"><input type="checkbox" name="included_sub_agent" value="1">包含下级</label>
                                <label class="checkbox-inline lbl_no_included_zongdai" style="display: none;"><input type="checkbox" name="no_included_zongdai" value="1">不计总代</label>
                            </div>
                        </div>
                    </div>

                <div class="form-group search_username">
                    <label class="col-sm-3 control-label">用户名</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="username" value="{{$username}}" placeholder="用户名">
                    </div>
                </div>

                <div class="form-group search_agent" style="display: none;">
                    <label class="col-sm-3 control-label">总代</label>
                    <div class="col-sm-9">
                        <select name="zongdai" class="form-control" >
                            <option value=''>所有总代</option>
                            @foreach ($zongdai_list as $v)
                            <option value='{{ $v->id }}'>{{ $v->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">用户组别</label>
                    <div class="col-sm-9">
                        <select name="user_group_id" class="form-control">
                            <option value="0">所有组别</option>
                            @foreach($user_group as $item)
                                <option value="{{ $item->id }}" @if($item->id==1) selected @endif>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">管理员</label>
                    <div class="col-sm-9">
                        <select name="admin_user_id" class="form-control" >
                            <option value='0'>不限</option>
                            @foreach ($admin_list as $admin)
                                <option value='{{ $admin->id }}'>{{ $admin->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">IP 地址</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="ip" placeholder="用户 IP 地址">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">账变金额</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="amount_min" placeholder="最小金额">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="amount_max" placeholder="最大金额">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label text-right app_label_pt9px">帐变类别</label>
                        <div class="col-sm-9">
                            <div class="col-sm-6 form-control app_wauto_bnone">
                                <label class="radio-inline"><input type="radio" name="zhangbian_type" value="1">游戏帐变</label>
                                <label class="radio-inline"><input type="radio" name="zhangbian_type" value="2">充提帐变</label>
                            </div>
                        </div>
                    </div>

                    <!--start：游戏帐变相关-->
                    <div class="form-group lottery_id_div">
                        <label class="col-sm-3 control-label text-right">彩种</label>
                        <div class="col-sm-9">
                            <select name="lottery_id" class="form-control lottery_id">
                                <option value=''>所有彩种</option>
                                @foreach ($lottery_list as $k=>$lotteries)
                                    <optgroup label="{{$k}}">
                                        @foreach($lotteries as $lottery)
                                            <option value='{{ $lottery->id }}' ident='{{ $lottery->ident }}'>{{ $lottery->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group method_div" style="display: none;">
                        <label class="col-sm-3 control-label text-right method_label" style="display: none;">玩法选择</label>
                        <div class="col-sm-9">
                            <!--js生成对应玩法列表-->
                        </div>
                    </div>

                    <div class="form-group mode_div">
                        <label class="col-sm-3 control-label">投注模式</label>
                        <div class="col-sm-9">
                            <select name="mode" class="form-control" >
                                <option value=''>所有模式</option>
                                @foreach ($mode_list as $k=>$v)
                                    <option value='{{ $k }}'>{{ $v['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{--<div class="form-group client_type_div">--}}
                        {{--<label class="col-sm-3 control-label">客户端类型</label>--}}
                        {{--<div class="col-sm-9">--}}
                            {{--<select name="client_type" class="form-control" >--}}
                                {{--<option value=''>所有类型</option>--}}
                                {{--@foreach ($source_list as $k=>$v)--}}
                                    {{--<option value='{{ $k }}'>{{ $v }}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    <div class="form-group">
                        <label class="col-sm-3 control-label text-right app_label_pt9px">全部总计</label>
                        <div class="col-sm-9">
                            <select name="calculate_total" class="form-control">
                                <option value="1" checked="checked">否</option>
                                <option value="2">是</option>
                            </select>
                        </div>
                    </div>
                    <!--end：游戏帐变相关-->
                </div>
            </div>

            <div class="box-footer text-center">
                <button type="button" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                <button type="submit" class="btn btn-warning margin"><i class="fa fa-download" aria-hidden="true"></i>导出</button>

            </div>
	   </form>
    </div>
    <!--搜索框 End-->

    <div class="box box-primary">
        <div class="box-body">

            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                    <tr>
                        <th class="hidden-sm" data-sortable="false"></th>
                        <th class="hidden-sm" data-sortable="false">订单编号</th>
                        <th class="hidden-sm">账变时间</th>
                        <th class="hidden-sm" data-sortable="false">用户名</th>
                        <th class="hidden-sm" data-sortable="false">账变类型</th>
                        <th class="hidden-sm" data-sortable="false">彩种</th>
                        <th class="hidden-sm" data-sortable="false">玩法</th>
                        <th class="hidden-sm" data-sortable="false">奖期</th>
                        <th class="hidden-sm" data-sortable="false">模式</th>
                        <th class="hidden-sm">支出</th>
                        <th class="hidden-sm">收入</th>
                        <th class="hidden-sm">后余额/冻结</th>
                        <th class="hidden-sm">前余额/冻结</th>
                        <th class="hidden-sm" data-sortable="false" title=" 用户 IP 地址 ">IP 地址</th>
                        <th class="hidden-sm" data-sortable="false">客户端</th>
                        <th class="hidden-sm" data-sortable="false">备注</th>
                        <th class="hidden-sm" data-sortable="false">管理员</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="9" class="text-right"><b>本页总计： </b></th>
                        <th></th>
                        <th></th>
                        <th colspan="5"></th>
                    </tr>
                    <tr id="total_sum" style="display: none;">
                        <th colspan="9" class="text-right"><b>全部总计： </b></th>
                        <th id="total_outmoney"></th>
                        <th id="total_inmoney"></th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="modal-detail" tabIndex="-1" role="dialog" >
    <div class="modal-dialog  modal-lg"  role="document">
        <div class="modal-content"></div>
    </div>
</div>

@stop


@section('js')
<style lang="css">
    table td { max-width: 180px; word-break: break-word;}
</style>
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig ={
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex:2
        };
        laydate(layConfig);

        layConfig.elem = '#end_date';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'order_type_ids' : $('select[name="order_type_ids[]"]').val(),
                    'order_no'       : $("input[name='order_no']").val(),
                    'admin_user_id'  : $("select[name='admin_user_id']").val(),
                    'ip'             : $("input[name='ip']").val(),
                    'start_date'     : $("input[name='start_date']").val(),
                    'end_date'       : $("input[name='end_date']").val(),
                    'amount_min'     : $('input[name="amount_min"]').val(),
                    'amount_max'     : $('input[name="amount_max"]').val(),
                    'mode'           : $('select[name="mode"]').val(),
                    'client_type'    : $('select[name="client_type"]').val(),
                    'user_group_id'  : $('select[name="user_group_id"]').val(),
                    'lottery_id'     : $('select[name="lottery_id"]').val(),
                    'payment_account_id'  : $("#payment_account_id").val(),
                    'method_id'           : $('select[name="method_id"]').val(),
                    'included_sub_agent'  : $('input[name="included_sub_agent"]').prop('checked') ? 1 : 0,
                    'no_included_zongdai' : $('input[name="no_included_zongdai"]').prop('checked') ? 1 : 0,
                    'zongdai  '           : $('select[name="zongdai"]').val(),
                    'search_type'    : $('input[name="search_type"]:checked').val(),
                    'calculate_total'    : $('select[name="calculate_total"]').val(),
                };

                if (param['search_type'] == 1) {
                    param['username'] = $('input[name="username"]').val();
                } else {
                    param['zongdai'] = $('select[name="zongdai"]').val();
                }

                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language:app.DataTable.language(),

                order: [[2, "desc"]],
                serverSide: true,
                pageLength:25,
                searching:false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    null,
                	{"data": "order_id","orderable":false},
                	{"data": "created_at"},
                	{"data": "username","orderable":false},
                	{"data": "order_name","orderable":false},
                    {"data": "lottery_name","orderable":false},
                    {"data": "method_name","orderable":false},
                    {"data": "issue","orderable":false},
                    {"data": "mode","orderable":false},
                	{"data": "amount","orderable":false},
                    {"data": "amount","orderable":false},
                	{"data": "balance","orderable":false},
                    {"data": "pre_balance","orderable":false},
                	{"data": "ip","orderable":false},
                	{"data": "client_type","orderable":false},
                    {"data": "comment","orderable":false},
                	{"data": "adminname","orderable":false},
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "visible": false,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return '';
                        }
                    },
                    {
                        "targets": 1,
                        "searchable": false,
                        "render": function (data, type, row) {
                            @if(Gate::check('project/detail'))
                                return row.project_id !== 0 ? '<a href="/project/detail?id='+row.project_id+'" mountTabs title="'+row.username+'投注详情['+row.project_id+']'+row.order_id_raw+'"  >'+data+'</a>' : '<span title="'+row.order_id_raw+'">'+data+'</span>';
                            @else
                                return '<span title="'+row.order_id_raw+'">'+data+'</span>';
                            @endif
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
                        "targets": 4,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return app.getLabelHtml((row.operation==1?"[+]":(row.operation==2?"[-]":"[h]"))+data, 'label-primary');
                        }
                    },
                    {
                        "targets": -5,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return row['pre_balance']+"/"+row['pre_hold_balance'];
                        }
                    },
                    {
                        "targets": -6,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return row['balance']+"/"+row['hold_balance'];
                        }
                    },
                    {   "targets": 8,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return app.getLabelHtml(data, 'label-info');
                        }
                    },
                    {
                        "targets": 9,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return (row.operation == 2 || (row.hold_operation == 2 && row.operation == 0)) ? app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data), 'text-red', true) : '';

                        }
                    },
                    {
                        "targets": 10,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return (row.operation == 1 || (row.hold_operation == 1 && row.operation == 0)) ? app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data), 'text-green', true) : '';
                        }
                    },
                    {
                        "targets": 11,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data)
                        }
                    },
                    {
                        "targets": 12,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data)
                        }
                    },
                    {"targets": -3,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return app.getLabelHtml(data, 'label-default');
                        }
                    }
                ],

                "footerCallback": function ( tfoot, data, start, end, display ){
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?i : 0;
                    };

                    var inmoney = 0;
                    var outmoney = 0;

                    for(item in data){
                        if(data[item].operation==1) {
                            inmoney += intVal(data[item].amount);
                        }else if(data[item].operation==2){
                            outmoney += intVal(data[item].amount);
                        }
                    }

                    $(tfoot).find('th').eq(1).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(outmoney), 'text-red', true)
                    );
                    $(tfoot).find('th').eq(2).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(inmoney), 'text-green', true)
                    );
                }

            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('xhr.dt', function ( e, settings, json, xhr ) {
                if(json !== undefined && json !== null ){
                    if( typeof json['sum_amount'] == 'object' ){
                        $('#total_outmoney').html(
                            app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(json['sum_amount']['outmoney']), 'text-red', true)
                        );
                        $('#total_inmoney').html(
                            app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(json['sum_amount']['inmoney']), 'text-green', true)
                        );
                    }else{
                        $('#total_outmoney').html('');
                        $('#total_inmoney').html('');
                    }
                }
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            $('#search_btn').click(function(event){
            	event.preventDefault();
                table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function(){
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                //showErrors( arguments[0].jqXHR.responseJSON.errors );
                loadFadeOut();
            };

            //总代选择操作
            $("input[name='search_type']").change(function(){
                if ($(this).val() == 1) { // 输入用户名
                    $('.search_agent').hide();
                    $('.search_username').show();
                    $('.lbl_no_included_zongdai').hide();
                    $('input[name="no_included_zongdai"]').prop('checked',false);
                    $('select[name="zongdai"]').val('0');
                } else { // 选择总代
                    $('.search_username').hide();
                    $('input[name="username"]').val('');
                    $('.search_agent').show();
                    $('.lbl_no_included_zongdai').show();
                }

            });

            //游戏或者冲提帐变单选radio操作
            $("input[name='zhangbian_type']").change(function(){

                if ($(this).val() == 1) { // 游戏帐变联动
                    $('.lottery_id_div').show();
                    $('.mode_div').show();
                    $('.client_type_div').show();
                    $('.payment_category_id_div').hide();
                    $('.payment_account_id_div').hide();
                    $('select[name="payment_category_id"]').val(0);
                    $('select[name="payment_account_id"]').val(0);
                    $('select[name="order_type_ids[]"]').val([{{ $sub_order_type['lottery_order_type'] }}]);
                } else { // 冲提帐变联动

                    $('select[name="lottery_id"]').val('');
                    $('select[name="mode"]').val('');
                    $('select[name="client_type"]').val('');
                    $('.payment_category_id_div').show();
                    $('.payment_account_id_div').show();
                    $('.lottery_id_div').hide();
                    $('.mode_div').hide();
                    $('.client_type_div').hide();
                    $('.method_div').hide();
                    $('select[name="method_id"]').val('')
                    $('select[name="order_type_ids[]"]').val([{{ $sub_order_type['chongti_order_type'] }}]);
                }

                //fixed chrome浏览器不自动滚动问题
                if ( /chrome/.test(navigator.userAgent.toLowerCase())){;
                    $('select[name="order_type_ids[]"]').scrollTop(0);
                    $('select[name="order_type_ids[]"]').animate({
                        scrollTop:parseInt($('select[name="order_type_ids[]"]').find("option:selected").eq(0).offset().top)-parseInt($('select[name="order_type_ids[]"]').offset().top)
                    },500);
                }

            });

            //计算合计
            $("select[name='calculate_total']").change(function () {
                if ($(this).val() == 1) {
                    $('#total_sum').hide();
                } else {
                    $('#total_sum').show();
                }
            });

            // 彩种选择onchange，联动玩法显示与否效果,隐藏重置为初始值
            $(".lottery_id").change(function(){
                if ($(this).val() >= 1) {
                    $('.method_label').show();
                    $('.method_div').show();
                    lottery_change($(this).find("option:selected").attr("ident"));
                } else {
                    $('.method_label').hide();
                    $('.method_div').hide();
                    $('select[name="method_id"]').hide().val('')
                }
            });

            function lottery_change(ident){
                var method_list = {!! $lottery_method_list !!};
                var html = "<select name='method_id' class='form-control'><option value=''>玩法列表</option>";
                for (var i = 0; i < method_list[ident].length; i++)
                {
                    html += "<option value='"+method_list[ident][i].id+"'>"+method_list[ident][i].name+"</option>";
                }
                html += "</select>";
                $('.method_div .col-sm-9').html(html);
            }

            $('#modal-detail').on('show.bs.modal', function () {
                loadShow();
            });
            $('#modal-detail').on('hidden.bs.modal', function () {
                $(this).find(".modal-content").html('');
                $(this).removeData();
		loadFadeOut();
            });
            $("#modal-detail").on('loaded.bs.modal',function(){//数据加载完成后删除loading
                loadFadeOut();
            });
        });
    </script>
@stop
