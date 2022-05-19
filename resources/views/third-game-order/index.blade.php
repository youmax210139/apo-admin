@extends('layouts.base')

@section('title','平台转帐')
@section('function','平台转帐')
@section('function_link', '/thirdgameorder/')
@section('here','平台转帐')

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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">流水号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='order_num' placeholder="第三方流水号" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="balance" class="col-sm-3 control-label">金额</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="amount_min" placeholder="最小金额">
                                        <span class="input-group-addon"> ~ </span>
                                        <input type="text" class="form-control form_datetime" name="amount_max" placeholder="最大金额">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="balance" class="col-sm-3 control-label">平台</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <select name="from" class="form-control">
                                            <option value="">所有平台</option>
                                            @foreach($platforms as $platform)
                                                <option value="{{ $platform['ident'] }}">{{ $platform['name'] }} [ {{ $platform['ident'] }} ]</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"> - </span>
                                        <select name="to" class="form-control">
                                            <option value="">所有平台</option>
                                            @foreach($platforms as  $platform)
                                                <option value="{{ $platform['ident'] }}">{{ $platform['name'] }} [ {{ $platform['ident'] }} ]</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="created_start_date" class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="created_start_date" id='created_start_date' value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="created_end_date" id='created_end_date' value="" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cashier" class="col-sm-3 col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="">全部</option>
                                        <option value="1">成功</option>
                                        <option value="2">失败</option>
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
                            <button type="reset" class="btn btn-default col-sm-2" ></i>重置</button>
                        </div>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->

            <div class="box box-primary">
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">流水号</th>
                            <th class="hidden-sm" data-sortable="false">来源平台</th>
                            <th class="hidden-sm" data-sortable="false">目标平台</th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" >订单金额</th>
                            <th class="hidden-sm" >订单时间</th>
                            <th class="hidden-sm"  data-sortable="false">用户IP</th>
                            <th class="hidden-sm" >订单状态</th>
                            <th class="hidden-sm" data-sortable="false">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
        #modal-deal .modal-body th, #modal-deal .modal-body td{
            height: 28px;line-height: 28px;
        }
        .dealthird_action{
            display: none
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
                <div class="modal-body">

                </div>
                <div class="modal-footer" style="text-align: center">
                    <div class="deal_action">
                        <button type="button" flag="2" class="btn btn-danger deal_btn">出款失败</button>
                        <button type="button" flag="1" class="btn btn-primary deal_btn">出款成功</button>
                    </div>
                    <div class="dealthird_action">
                        <button type="button" class="btn btn-success dealthird_btn">提交至第三方</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#created_start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);
        layConfig.elem = '#created_end_date';
        laydate(layConfig);
        $(function () {
            var get_params = function (data) {
                var param = {
                    'order_num': $("input[name='order_num']").val(),
                    'username': $("input[name='username']").val(),
                    'created_start_date': $("input[name='created_start_date']").val(),
                    'created_end_date': $("input[name='created_end_date']").val(),
                    'from': $("select[name='from']").val(),
                    'to': $("select[name='to']").val(),
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                    'status': $("select[name='status']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[5, "desc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "order_num"},
                    {"data": "from"},
                    {"data": "to"},
                    {"data": "username"},
                    {"data": "amount"},
                    {"data": "created_at"},
                    {"data": "created_ip"},
                    {"data": "status_label"},
                    {"data": "remark"}
                ],
                createdRow: function (row, data, index) {
                    if (data['withdraw_sum'] >= 30000 || data['withdraw_sum'] >= 3000) {
                        $(row).addClass('danger');
                    }
                },
                columnDefs: [
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
                        'targets': -2,
                        'render': function (data, type, row) {
                            var str = row.status_label + ' [ ' + row.status + ' ]';
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