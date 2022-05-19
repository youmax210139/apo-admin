@extends('layouts.base')

@section('title','异常行为监控')

@section('function','异常行为监控')
@section('function_link', '/behaviorlog/')

@section('here','异常行为列表')

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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" />
                                </div>
                            </div>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action" class="col-sm-3 col-sm-3 control-label">行为</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='action' placeholder="行为" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="level" class="col-sm-3 control-label">严重程度</label>
                                <div class="col-sm-9">
                                    <select name="level" class="form-control">
                                        <option value="-1" selected="selected">全部</option>
                                        <option value="0">一般</option>
                                        <option value="1">严重</option>
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
                        <th data-sortable="false">用户名</th>
                        <th data-sortable="false">用户组</th>
                        <th data-sortable="false">行为</th>
                        <th data-sortable="false">严重程度</th>
                        <th data-sortable="false">描述</th>
                        <th data-sortable="false">时间</th>
                        <th data-sortable="false">操作</th>
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
                'action': $("input[name='action']").val(),
                'level': $("select[name='level']").val(),
            };
            return $.extend({}, data, param);
        };

        var table = $("#tags-table").DataTable({
            language:app.DataTable.language(),
            order: [],
            serverSide: true,
            pageLength:50,
            searching: false,
            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
            // 要 ajax(url, type) 必须加这两参数
            ajax: app.DataTable.ajax(null, null, get_params),
            "columns": [
                {"data": "username"},
                {"data": "user_group_name"},
                {"data": "action"},
                {"data": "level"},
                {"data": "description"},
                {"data": "created_at"},
                {"data": null}
            ],
            columnDefs: [
                {
                    'targets': 0,
                    'render': function (data, type, row) {
                        if (row['user_observe']) {
                            return app.getColorHtml(row.username, 'label-danger', false);
                        } else {
                            return row.username;
                        }
                    }
                },
                {
                    'targets': 1,
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
                    'targets': 3, "render": function (data, type, row) {
                        if (data == 0) {
                            return app.getLabelHtml('一般', 'label-warning');
                        } else {
                            return app.getLabelHtml('严重', 'label-danger');
                        }
                    }
                },
                {
                    'targets': -1, "render": function (data, type, row) {
                        return '<a href="/behaviorlog/detail?id=' + row['id'] + '" class="X-Small btn-xs text-success " data-target="#modal-detail" data-toggle="modal"><i class="fa fa-file-text-o"></i> 详情</a>';
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
