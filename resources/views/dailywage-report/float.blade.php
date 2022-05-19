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
                                        <option value="7" @if($wage_type==7) selected @endif>奖期工资</option>
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
                                <label for="created_start_date" class="col-sm-3 control-label">计算日期</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date"
                                               id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                    </div>
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
                                <label for="userlevel" class="col-sm-3 col-sm-3 control-label">用户级别</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='userlevel' 
                                        onkeyup="value=value.replace(/^(0+)|[^\d]+/g,'')" 
                                        placeholder="用户级别:'1级代理'>1"
                                    />
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
                                <th class="hidden-sm" data-sortable="true">用户名</th>
                                <th class="hidden-sm" data-sortable="true">级别</th>
                                <th class="hidden-sm" data-sortable="false">用户组</th>
                                <th class="hidden-sm" data-sortable="true">日期</th>
                                <th class="hidden-sm" data-sortable="true">团队销售总额</th>
                                <th class="hidden-sm" data-sortable="true">累积返点</th>
                                <th class="hidden-sm" data-sortable="false">有效人数</th>
                                <th class="hidden-sm" data-sortable="false">备注</th>
                                <th class="hidden-sm" data-sortable="true">工资比例</th>
                                <th class="hidden-sm" data-sortable="true">下级工资总和</th>
                                <th class="hidden-sm" data-sortable="true">工资应派金额</th>
                                <th class="hidden-sm" data-sortable="true">工资总和</th>
                                <th class="hidden-sm" data-sortable="false">发放时间</th>
                                <th class="hidden-sm" data-sortable="false">状态</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr id="total_sum">
                                <th colspan="4" class="text-right"><b>全部总计： </b></th>
                                <th ></th>
                                <th ></th>
                                <th ></th>
                                <th ></th>
                                <th ></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row">
                        <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="delete_by_select" id="delete_by_select"
                                   value="删除所选"/>
                        </div>

                        <div class="col-xs-2">
                            <input type="button" class="btn btn-primary" value="删除计算日期所有记录" name="delete_by_date"
                                   id="delete_by_date"/>
                        </div>
                        <div class="col-xs-1">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="start_time" id='start_time'
                                       autocomplete="off" placeholder="计算日期">
                            </div>
                        </div>
                        <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="check_by_select" id="check_by_select"
                                   value="确认所选"/>
                        </div>
                        <div class="col-xs-2">
                            <input type="button" class="btn btn-primary" value="确认计算日期所有记录" name="check_by_date"
                                   id="check_by_date"/>
                        </div>
                        <div class="col-xs-1">
                            <input type="button" class="btn btn-primary" name="refuse_by_select" id="refuse_by_select"
                                   value="拒绝所选"/>
                        </div>
                    </div>
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
                    <form class="modal_form" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="select_ids" id="select_ids" value="">
                        <input type="hidden" name="type" id="select_ids" value="4">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
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
        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'userlevel': $("input[name='userlevel']").val(),
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                    'frozen': $("select[name='frozen']").val(),
                    'search_scope': $("select[name='search_scope']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'status': $("select[name='status']").val()
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[2, "asc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax('/dailywagereport/float', null, get_params),
                "columns": [
                    {"data": "username"},
                    {"data": "user_level_name"},
                    {"data": "user_group_name"},
                    {"data": "date"},
                    {"data": "total_price"},
                    {"data": "total_rebate"},
                    {"data": "activity"},
                    {"data": "remark"},
                    {"data": "rate"},
                    {"data": "child_amount"},
                    {"data": "amount"},
                    {"data": "total_amount"},
                    {"data": "send_at"},
                    {"data": "status"},

                ],
                createdRow: function (row, data, index) {
                    var total = parseFloat(row.amount) + parseFloat(row.deduct);
                    var total1 = parseFloat(row['total_bet']) * parseFloat(row['rate']) / 100;
                    if (Math.abs(total1 - total) > 1) {
                        $(row).addClass('danger');
                    }
                },
                columnDefs: [
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(data, 'label-primary');
                        }
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            var label = 'label-success';

                            if (row.user_group_id == 2 ) {
                                label = 'label-warning';
                            } else if (row.user_group_id == 3 ) {
                                label = 'label-danger';
                            }

                            return app.getLabelHtml(data, label);
                        }
                    },
                    {
                        'targets': 7,
                        'render': function (data, type, row) {
                            @if(in_array(get_config('dailywage_type_ident'), ['Feiyu2','B6','Bile','Feiyu3','Jiucheng2','Duowan2','Oudi','ChuangYing']))
                                data = $.parseJSON(data);
                                var str = '日亏损:'+new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data.loss);
                                if (data.float != undefined) {
                                    str += '<br>工资比例:'+data.float+'%';
                                }

                                if (data.salary != undefined) {
                                    str += '<br>亏损工资:'+data.salary;
                                }

                                if (data.period != undefined) {
                                    str += '<br>周期开始:'+data.period.time_start;
                                    str += '<br>周期结束:'+data.period.time_end;
                                }

                                if (data.activity != undefined) {
                                    str += '<br>有效人数:'+ data.activity;
                                }
                                return str;
                            @else
                                return '';
                            @endif
                        }
                    },
                    {
                        'targets': 6,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 0}).format(data);
                        }
                    },
                    {
                        'targets': 9,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                        }
                    },
                    {
                        'targets': 10,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
                        }
                    },
                    {
                        'targets': 11,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(data);
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
                            } else if (row.status == 5) {
                                str = '已取消';
                            } else if (row.status == 6) {
                                str = '不符合条件';
                            } else if (row.status == 7) {
                                str = '非结算日';
                            } else {
                                str = '状态[' + row.status + ']';
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


            //批量删除
            $("#delete_by_select").bind('click', function () {
                var select_rows = table.rows({selected: true});
                var id_array = select_rows.data().pluck('id').toArray();
                if (id_array.length == 0) {
                    $("#tips_content").html("请选择需要删除的记录");
                    $("#modal-msg").modal();
                    return false;
                }
                $("#delete_tips_content").html("确认要删除选中的记录吗？");
                $('#select_ids').val(id_array.join(','));
                $('.modal_form').attr('action', '/dailywagereport/delete?delete_by=select');
                $("#modal-form").modal();
            });

            //批量确认
            $("#check_by_select").bind('click', function () {
                var select_rows = table.rows({selected: true});
                var id_array = select_rows.data().pluck('id').toArray();
                if (id_array.length == 0) {
                    $("#tips_content").html("请选择需要确认的记录");
                    $("#modal-msg").modal();
                    return false;
                }
                $("#delete_tips_content").html("要确认选中的记录吗？");
                $('#select_ids').val(id_array.join(','));
                $('.modal_form').attr('action', '/dailywagereport/check?check_by=select');
                $("#modal-form").modal();
            });
            //批量拒绝
            $("#refuse_by_select").bind('click', function () {
                var select_rows = table.rows({selected: true});
                var id_array = select_rows.data().pluck('id').toArray();
                if (id_array.length == 0) {
                    $("#tips_content").html("请选择需要确认的记录");
                    $("#modal-msg").modal();
                    return false;
                }
                $("#delete_tips_content").html("要确认选中的记录吗？");
                $('#select_ids').val(id_array.join(','));
                $('.modal_form').attr('action', '/dailywagereport/check?check_by=select&status=3');
                $("#modal-form").modal();
            });

            //按日期删除
            $("#delete_by_date").bind('click', function () {
                var start_time = $('#start_time').val();
                if (!start_time) {
                    $("#tips_content").html("请输入要删除的日期");
                    $("#modal-msg").modal();
                    return false;
                }
                $("#delete_tips_content").html("要删除 " + start_time + "  的所有记录吗？");
                $('#select_ids').val('');
                $('.modal_form').attr('action', '/dailywagereport/delete?delete_by=date&start_time=' + start_time);
                $("#modal-form").modal();
            });

            //按日期确认
            $("#check_by_date").bind('click', function () {
                var start_time = $('#start_time').val();
                if (!start_time) {
                    $("#tips_content").html("请输入要确认的日期");
                    $("#modal-msg").modal();
                    return false;
                }
                $("#delete_tips_content").html("要确认 " + start_time + "  的所有记录吗？");
                $('#select_ids').val('');
                $('.modal_form').attr('action', '/dailywagereport/check?check_by=date&start_time=' + start_time);
                $("#modal-form").modal();
            });


            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
                if (json !== undefined && json !== null && typeof json['totalSum'] == 'object') {
                    var total_price     = json['totalSum']['total_price'];
                    var total_rebate    = json['totalSum']['total_rebate'];
                    var child_amount    = json['totalSum']['child_amount'];
                    var amount          = json['totalSum']['amount'];
                    var total_activity  = json['totalSum']['total_activity'];


                    $("#total_sum").find('th').eq(1).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(total_price), 'text-green', true)
                    );

                    $("#total_sum").find('th').eq(3).html(
                        app.getColorHtml('' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 0}).format(total_activity), 'text-green', true)
                    );
                    $("#total_sum").find('th').eq(2).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(total_rebate), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(5).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(child_amount), 'text-red', true)
                    );
                    $("#total_sum").find('th').eq(6).html(
                        app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(amount), 'text-red', true)
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
