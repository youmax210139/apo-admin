@extends('layouts.base')

@section('title','私返列表')
@section('function','私返列表')
@section('function_link', '/privatereturn/')
@section('here','私返列表')

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
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名称</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="用户名称"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="source_user" class="col-sm-3 col-sm-3 control-label">发放人</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='source_user' placeholder="用户名称"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="balance" class="col-sm-3 control-label">搜索范围</label>
                                <div class="col-sm-9">
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
                                <label for="start_time" class="col-sm-3 control-label">计算日期</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_time"
                                               id="start_time" value="{{$start_time}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_time"
                                               id="end_time" value="{{$end_time}}" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-3 control-label">用户组别</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="0">所有组别</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-4 control-label text-right">状态</label>
                                <div class="col-sm-8">
                                    <select name="status" class="form-control">
                                        <option value="">全部</option>
                                        <option value="0">待确认</option>
                                        <option value="1">待发放</option>
                                        <option value="2">已发放</option>
                                    </select>
                                </div>
                            </div>
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
                                <th class="hidden-sm" data-sortable="false"></th>
                                <th class="hidden-sm" data-sortable="true">用户名</th>
                                <th class="hidden-sm" data-sortable="false">级别</th>
                                <th class="hidden-sm" data-sortable="false">用户组</th>
                                <th class="hidden-sm" data-sortable="false">销量</th>
                                <th class="hidden-sm" data-sortable="false">盈亏</th>
                                <th class="hidden-sm" data-sortable="false">活跃人数</th>
                                <th class="hidden-sm" data-sortable="true">基数</th>
                                <th class="hidden-sm" data-sortable="false">比例</th>
                                <th class="hidden-sm" data-sortable="false">私返金额</th>
                                <th class="hidden-sm" data-sortable="true">状态</th>
                                <th class="hidden-sm" data-sortable="true">开始时间</th>
                                <th class="hidden-sm" data-sortable="true">结束时间</th>
                                <th class="hidden-sm" data-sortable="false">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
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
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_time',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);
        layConfig.elem = '#end_time';
        laydate(layConfig);
        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'source_user': $("input[name='source_user']").val(),
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                    'frozen': $("select[name='frozen']").val(),
                    'search_scope': $("select[name='search_scope']").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'start_time': $("input[name='start_time']").val(),
                    'end_time': $("input[name='end_time']").val(),
                    'status': $("select[name='status']").val()
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
                    {"visible":false},
                    {"data": "username"},
                    {"data": "parent_tree"},
                    {"data": "user_group"},
                    {"data": "price"},
                    {"data": "profit"},
                    {"data": "active"},
                    {"data": "cardinal"},
                    {"data": "rate"},
                    {"data": "amount"},
                    {"data": "status"},
                    {"data": "start_time"},
                    {"data": "end_time"},
                    {"data": "null"},

                ],
                columnDefs: [
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(data, 'label-primary');
                        }
                    },
                    {
                        'targets': 3,
                        'render': function (data, type, row) {
                            var label = 'label-success';
                            if (row.user_group == 2) {
                                label = 'label-warning';
                            } else if (row.user_group == 3) {
                                label = 'label-danger';
                            }

                            return app.getLabelHtml(data, label);
                        }
                    },
                    {
                        'targets': -4,
                        'render': function (data, type, row) {
                            var label = 'label-default';
                            if (data == 0) {
                                label = 'label-info';
                            } else if (data == 1) {
                                label = 'label-primary';
                            } else if (data == 2) {
                                label = 'label-success';
                            } else if (data == 3) {
                                label = 'label-danger';
                            } else if (data == 4) {
                                label = 'label-warning';
                            }
                            return app.getLabelHtml(row.status_label, label);
                        }
                    },
                    {
                    'targets': -1,
                    "render": function (data, type, row) {
                        var str = '';
                        @if(Gate::check('privatereturn/detail'))
                            str += '<a href="javascript:;" data-id="' + row.id + '" class="X-Small btn-xs text-info private_return_detail">明细</a>';
                        @endif
                        return str;
                    }
                }
                ]
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
                if (json !== undefined && json !== null && typeof json['totalSum'] == 'object') {
                    var total_amount = json['totalSum']['total_amount'];
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

            //明细
            $("table").delegate('.private_return_detail', 'click', function () {
                var id = $(this).attr('data-id');
                console.log(id);
                loadShow();
                $.ajax({
                    url: "/privatereturn/detail",
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
        });
    </script>
@stop
