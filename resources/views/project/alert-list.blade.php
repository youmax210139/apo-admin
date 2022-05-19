@extends('layouts.base')

@section('title','提示列表')

@section('function','提示列表')
@section('function_link', '/project/alert/')

@section('here','提示列表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
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
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="read_type" class="col-sm-3 control-label">查看状态</label>
                                <div class="col-sm-9" >
                                    <select class="form-control" name="read_type">
                                        <option value="-1" selected>全部</option>
                                        <option value="0" >未读</option>
                                        <option value="1" >已读</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" id='end_date' value="{{$end_date}}" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="alert_type" class="col-sm-3 control-label">通知类型</label>
                                <div class="col-sm-9" >
                                    <select class="form-control" name="alert_type">
                                        <option value="-1" selected>全部</option>
                                        <option value="0" >高额中奖</option>
                                        <option value="1" >久未活跃用户投注</option>
                                        <option value="2" >重点观察用户上线</option>
                                        <option value="3" >今日登录数过高</option>
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
        </div>
    </div>
        <div class="box box-primary">
            @include('partials.errors')
            @include('partials.success')
            <div class="box-body">
                <table id="tags-table" class="table table-bordered table-hover app_w100pct">
                    <thead>
                    <tr>
                        <th data-sortable="false">流水号</th>
                        <th data-sortable="false">通知类型</th>
                        <th data-sortable="false">注单号</th>
                        <th data-sortable="false">用户名</th>
                        <th data-sortable="false">时间</th>
                        <th data-sortable="false">彩种</th>
                        <th data-sortable="false">玩法</th>
                        <th data-sortable="false">奖期</th>
                        <th data-sortable="false">投注金额</th>
                        <th data-sortable="false">中奖金额</th>
                        <th data-sortable="false">投注前馀额</th>
                        <th data-sortable="false">可选操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                'start_date': $("input[name='start_date']").val(),
                'end_date': $("input[name='end_date']").val(),
                'alert_type': $('select[name="alert_type"]').val(),
                'read_type': $('select[name="read_type"]').val(),
            };
            return $.extend({}, data, param);
        };

        var table = $("#tags-table").DataTable({
            language:app.DataTable.language(),
            order: [],
            serverSide: true,
            searching: false,
            pageLength:25,
            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
            // 要 ajax(url, type) 必须加这两参数
            ajax: app.DataTable.ajax(null, null, get_params),
            "columns": [
                {"data": "id"},
                {"data": "type"},
                {"data": "project_id"},
                {"data": "username"},
                {"data": "created_at"},
                {"data": "lottery_name"},
                {"data": "method_name"},
                {"data": "issue"},
                {"data": "total_price"},
                {"data": "bonus"},
                {"data": "pre_balance"},
                {"data": null}
            ],
            columnDefs: [
                {
                    'targets': 1, "render": function (data, type, row) {
                        var str = '高额中奖';
                        if (row.type == 1) {
                            str = '久未活跃用户投注';
                        } else if (row.type == 2) {
                            str = '重点观察用户上线';
                        } else if (row.type == 3) {
                            str = '今日登录数过高';
                        }

                        if (row.status != undefined &&  row.status == 0) {
                            str += app.getLabelHtml('未读', 'label-warning');
                        } else {
                            str += app.getLabelHtml('已读', 'label-default');
                        }

                        return str;
                    }
                },
                {
                    'targets': -1, "render": function (data, type, row) {
                        var html = '';
                        if (row.type == 2) {
                            html += '<a mountTabs href="/loginlog?username=' + row.username + '" class="X-Small btn-xs text-success " title="[' + row.username + ']登录日志查询"><i class="fa fa-file-text-o"></i> 详情</a>';
                            html += '<a mountTabs href="/order?username=' + row.username + '" class="X-Small btn-xs text-success " title="['+ row.username + ']账变列表"><i class="fa fa-file-text-o"></i> 账变</a>';
                        } else if (row.type == 3) {
                            html += '<a mountTabs href="/loginlog" class="X-Small btn-xs text-success " title="登录日志查询"><i class="fa fa-file-text-o"></i> 详情</a>';
                        } else {
                            html += '<a mountTabs href="/project/detail?id=' + row.project_id + '" class="X-Small btn-xs text-success " title="[' + row.username + ']投注详情[' + row.project_id + ']"><i class="fa fa-file-text-o"></i> 详情</a>';
                            html += '<a mountTabs href="/order?username=' + row.username + '" class="X-Small btn-xs text-success " title="[' + row.username + ']账变列表"><i class="fa fa-file-text-o"></i> 账变</a>';
                        }

                        return html;
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

        $('#modal-detail').on('show.bs.modal', function () {
            loadShow();
        });
        $('#modal-detail').on('hidden.bs.modal', function () {
            $(this).find(".modal-content").html('');
            $(this).removeData();
        });
        $("#modal-detail").on('loaded.bs.modal',function(){//数据加载完成后删除loading
            loadFadeOut();
        });
    });
</script>
@stop