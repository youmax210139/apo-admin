@extends('layouts.base')

@section('title','工资列表')
@section('function','工资列表')
@section('function_link', '/dailywagereport/')
@section('here','工资列表')

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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">结算周期</label>
                                <div class="col-sm-9" >
                                    <select class="form-control" onchange="location.href='/dailywagereport/?type_page='+this.value">
                                        <option value="1" @if($wage_type==1) selected @endif>日工资</option>
                                        <option value="2" @if($wage_type==2) selected @endif>实时工资</option>
                                        <option value="3" @if($wage_type==3) selected @endif>小时工资</option>
                                        <option value="4" @if($wage_type==4) selected @endif>浮动工资</option>
                                        <option value="5" @if($wage_type==5) selected @endif>挂单日工资</option>
                                        <option value="5" @if($wage_type==7) selected @endif>奖期工资</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="balance" class="col-sm-3 control-label">应派金额</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="amount_min"
                                               placeholder="最小金额">
                                        <span class="input-group-addon"> ~ </span>
                                        <input type="text" class="form-control form_datetime" name="amount_max"
                                               placeholder="最大金额">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="show_type" class="col-sm-3 control-label text-right">显示类型</label>
                                <div class="col-sm-9">
                                    <select name="show_type" class="form-control">
                                        <option value="">全部</option>
                                        <option value="1">只看源用户</option>
                                        <option value="2">不看源用户</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名称</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="用户名称"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="created_start_date" class="col-sm-3 control-label">投注时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date"
                                               id="start_date" value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date"
                                               id="end_date" value="{{$end_date}}" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lottery_id" class="col-sm-3 control-label">彩种</label>
                                <div class="col-sm-9">
                                    <select name="lottery_id" class="form-control">
                                        <option value="0">所有彩种</option>
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

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="balance" class="col-sm-4 control-label">搜索范围</label>
                                <div class="col-sm-8">
                                    <div class="input-daterange input-group">
                                        <select name="search_scope" class="form-control">
                                            <option value="owner">自己</option>
                                            <option value="directly">直属下级</option>
                                            <option value="team">团队成员</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-4 control-label">用户组别</label>
                                <div class="col-sm-8">
                                    <select name="user_group_id" class="form-control">
                                        <option value="0">所有组别</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="issue" class="col-sm-4 control-label">指定奖期</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name='issue' placeholder="指定奖期"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-4 control-label text-right">显示冻结用户</label>
                                <div class="col-sm-8">
                                    <select name="frozen" class="form-control">
                                        <option value="">全部</option>
                                        <option value="1">是</option>
                                        <option value="2" selected="selected">否</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label text-right">状态</label>
                                <div class="col-sm-8">
                                    <select name="status" class="form-control">
                                        <option value="">全部</option>
                                        <option value="0">待确认</option>
                                        <option value="1">待发放</option>
                                        <option value="2">已发放</option>
                                        <option value="3">已拒绝</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="source_username" class="col-sm-4 control-label">来源用户名</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name='source_username' placeholder="来源用户名"/>
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
            <!--搜索框 End-->

            <div class="box box-primary">
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="true">ID</th>
                            <th class="hidden-sm" data-sortable="true">彩种</th>
                            <th class="hidden-sm" data-sortable="true">奖期</th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" data-sortable="false">级别</th>
                            <th class="hidden-sm" data-sortable="false">用户组</th>
                            <th class="hidden-sm" data-sortable="true">金额</th>
                            <th class="hidden-sm" data-sortable="true">基数</th>
                            <th class="hidden-sm" data-sortable="true">比例</th>
                            <th class="hidden-sm" data-sortable="true">帐变ID</th>
                            <th class="hidden-sm" data-sortable="false">来源用户名</th>
                            <th class="hidden-sm" data-sortable="false">有效人数</th>
                            <th class="hidden-sm" data-sortable="false">投注</th>
                            <th class="hidden-sm" data-sortable="false">奖金</th>
                            <th class="hidden-sm" data-sortable="false">返点</th>
                            <th class="hidden-sm" data-sortable="false">亏损</th>
                            <th class="hidden-sm" data-sortable="true">发放时间</th>
                            <th class="hidden-sm" data-sortable="false">状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr id="total_sum">
                            <th colspan="6" class="text-right"><b>全部总计： </b></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
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
    </style>

@stop
@section('js')
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Select/css/select.dataTables.min.css"/>
    <link rel="stylesheet" href="/assets/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css"/>
    <script src="/assets/plugins/datatables/extensions/Select/js/dataTables.select.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js" charset="UTF-8"></script>
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
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                    'frozen': $("select[name='frozen']").val(),
                    'search_scope': $("select[name='search_scope']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'status': $("select[name='status']").val(),
                    'type': $("select[name='type']").val(),
                    'show_type': $("select[name='show_type']").val(),
                    'lottery_id': $("select[name='lottery_id']").val(),
                    'issue': $("input[name='issue']").val(),
                    'source_username': $("input[name='source_username']").val()
                };
                return $.extend({}, data, param);
            };

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax('/dailywagereport/issue', null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "lottery_name"},
                    {"data": "issue"},
                    {"data": "username"},
                    {"data": "user_type_name"},
                    {"data": "user_group"},
                    {"data": "amount"},
                    {"data": "cardinal"},
                    {"data": "rate"},
                    {"data": "order_id"},
                    {"data": "source_username"},
                    {"data": "daus"},
                    {"data": "price"},
                    {"data": "bonus"},
                    {"data": "rebate"},
                    {"data": "profit"},
                    {"data": "created_at"},
                    {"data": "status"}
                ],
                createdRow: function (row, data, index) {
                    /*var total = parseFloat(row.amount) + parseFloat(row.deduct);
                    var total1 = parseFloat(row['total_bet']) * parseFloat(row['rate']) / 100;
                    if (Math.abs(total1 - total) > 1) {
                        $(row).addClass('danger');
                    }*/
                },
                columnDefs: [
                    {
                        'targets': 11,
                        'render': function (data, type, row) {
                            var str = '-';
                            if (row.daus != undefined) {
                                str = row.daus;
                            }

                            return str;
                        }
                    },
                    {
                        'targets': -1,
                        'render': function (data, type, row) {
                            var str = '';
                            if (row.status == 0) {
                                str = '待确认';
                            } else if (row.status == 1) {
                                str = '待发放';
                            } else if (row.status == 2) {
                                str = '已发放';
                            } else if (row.status == 3) {
                                str = '已拒绝';
                            }
                            return str;
                        }
                    }
                ],
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
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
                if (json !== undefined && json !== null && typeof json['totalSum'] == 'object') {
                    var amount              = json['totalSum']['total_amount'];
                    var price           = json['totalSum']['price'];
                    var bonus          = json['totalSum']['bonus'];
                    var rebate    = json['totalSum']['rebate'];
                    var profit          = json['totalSum']['profit'];
                    $("#total_sum").find('th').eq(1).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(amount), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(7).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(price), 'text-green', true)
                    );
                    $("#total_sum").find('th').eq(8).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(bonus), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(9).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(rebate), 'text-green', true)
                    );
                    $("#total_sum").find('th').eq(10).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(profit), 'text-red', true)
                    );

                }
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

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


        });
    </script>
@stop
