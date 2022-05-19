@extends('layouts.base')

@section('title','日工资列表')
@section('function','日工资列表')
@section('function_link', '/dailywagereport/')
@section('here','日工资列表')

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
                                <label class="col-sm-3 control-label text-right">注单号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='project_code' placeholder="注单号"/>
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
                                <label for="created_start_date" class="col-sm-3 control-label">计算日期</label>
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
                            <th class="hidden-sm" data-sortable="true">ID</th>
                            <th class="hidden-sm" data-sortable="false">注单ID</th>
                            <th class="hidden-sm" data-sortable="true">用户名</th>
                            <th class="hidden-sm" data-sortable="true">级别</th>
                            <th class="hidden-sm" data-sortable="false">用户组</th>
                            <th class="hidden-sm" data-sortable="true">金额</th>
                            <th class="hidden-sm" data-sortable="true">备注</th>
                            <th class="hidden-sm" data-sortable="true">计算时间</th>
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
                            <th colspan="3"></th>
                        </tr>
                        </tfoot>
                    </table>
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
        layConfig.elem = '#end_date';
        laydate(layConfig);
        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'project_code': $("input[name='project_code']").val(),
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                    'frozen': $("select[name='frozen']").val(),
                    'search_scope': $("select[name='search_scope']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'status': $("select[name='status']").val()
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order:[[7, 'desc']],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax('/dailywagereport/realtime', null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "project_id"},
                    {"data": "username"},
                    {"data": "parent_level"},
                    {"data": "user_group"},
                    {"data": "amount"},
                    {"data": "remark"},
                    {"data": "created_at"},
                    {"data": "send_date"},
                    {"data": "status"}
                ],
                createdRow: function (row, data, index) {
                },
                columnDefs: [
                    {
                        "targets": 1,
                        "searchable": false,
                        "render": function (data, type, row) {
                            @if(Gate::check('project/detail'))
                            //return '<a href="/project/detail?id='+data+'" target="_blank">'+data+'</a>';
                            return '<a id="'+row['id']+'" href="/project/detail?id='+data+'" mountTabs title="'+row.username+'投注详情['+data+']" >'+data+'</a>';
                            @else
                                return data;
                            @endif
                        }
                    },
                    {
                        'targets':3,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(data, 'label-primary');
                        }
                    },
                    {
                        'targets': 4,
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
                    var total_amount = json['totalSum']['amount'];
                    $("#total_sum").find('th').eq(1).html(
                        app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits: 4}).format(total_amount), 'text-green', true)
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
