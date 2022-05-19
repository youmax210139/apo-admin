@extends('layouts.base')

@section('title','返点日志查询')

@section('function','返点日志查询')
@section('function_link', '/rebateslog/')

@section('here','返点日志查询')

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
                                <label for="type" class="col-sm-3 control-label">返点类型</label>
                                <div class="col-sm-9">
                                    <select name="type" class="form-control">
                                        <option value="">所有</option>
                                        @foreach($rebates as $rebate)
                                            <option value="{{$rebate['ident']}}">{{$rebate['name']}}</option>
                                        @endforeach

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
                        <th class="hidden-sm" data-sortable="false">用户名</th>
                        <th class="hidden-sm" data-sortable="false">返点类型</th>
                        <th class="hidden-sm" data-sortable="false">修改前返点</th>
                        <th class="hidden-sm" data-sortable="false">修改后返点</th>
                        <th class="hidden-sm" data-sortable="false">操作人类型</th>
                        <th class="hidden-sm" data-sortable="false">操作人</th>
                        <th class="hidden-sm" data-sortable="false">时间</th>
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
                'type': $("select[name='type']").val(),
            };
            return $.extend({}, data, param);
        };

        var table = $("#tags-table").DataTable({
            language:app.DataTable.language(),
            order: [],
            serverSide: true,
            searching: false,
            pageLength:50,
            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
            // 要 ajax(url, type) 必须加这两参数
            ajax: app.DataTable.ajax(null, null, get_params),
            "columns": [
                {"data": "username"},
                {"data": "type"},
                {"data": "old_value"},
                {"data": "new_value"},
                {"data": "operator_type"},
                {"data": "operator_id"},
                {"data": "created_at"}
            ],
            columnDefs: [
                {
                    'targets': -3,
                    'render': function (data, type, row) {
                        if (row['operator_type']==0) {
                            return app.getColorHtml('用户', 'label-danger', false);
                        } else {
                            return app.getColorHtml('管理员', 'label-success', false);
                        }
                    }
                },
                {
                    'targets': -2,
                    'render': function (data, type, row) {
                        if (row['operator_type']==0) {
                            return row['operator'];
                        } else {
                            return row['operator1'];
                        }
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