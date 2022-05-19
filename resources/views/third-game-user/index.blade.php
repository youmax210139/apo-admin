@extends('layouts.base')

@section('title','用户管理')
@section('function','用户管理')
@section('function_link', '/thirdgameuser/')
@section('here','用户管理')

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
                            <label for="cashier" class="col-sm-3 col-sm-3 control-label">平台</label>
                            <div class="col-sm-9">
                                <select name="platform" class="form-control">
                                    <option value="">请选择</option>
                                    @foreach($platforms as $platform)
                                        <option value="{{ $platform['ident'] }}">{{ $platform['name'] }} [ {{ $platform['ident'] }} ]</option>
                                    @endforeach
                                </select>
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
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="created_start_date" class="col-sm-3 control-label">注册时间</label>
                            <div class="col-sm-9">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="created_start_date" id='created_start_date' value="{{$start_date}}" placeholder="开始时间">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="created_end_date" id='created_end_date' value="" placeholder="结束时间">
                                </div>
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
                        <th class="hidden-sm" data-sortable="false">ID</th>
                        <th class="hidden-sm" data-sortable="false">平台用户名</th>
                        <th class="hidden-sm" data-sortable="false">第三方用户名</th>
                        <th class="hidden-sm" data-sortable="false">所属平台</th>
                        <th class="hidden-sm" >注册时间</th>
                        <th class="hidden-sm" data-sortable="false">资金</th>
                        <!--th class="hidden-sm" data-sortable="false">锁定</th-->
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                    'username': $("input[name='username']").val(),
                    'created_start_date': $("input[name='created_start_date']").val(),
                    'created_end_date': $("input[name='created_end_date']").val(),
                    'platform': $("select[name='platform']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "username"},
                    {"data": "user_name_third"},
                    {"data": "third_game_name"},
                    {"data": "created_at"},
                    {"data": "balance"}
                    //{"data": "is_lock"}
                ],
                createdRow: function (row, data, index) {
                    if (data['withdraw_sum'] >= 30000 || data['withdraw_sum'] >= 3000) {
                        $(row).addClass('danger');
                    }
                },
                columnDefs: [
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            if (row['user_observe']) {
                                return app.getColorHtml(row.username, 'label-danger', false);
                            } else {
                                return row.username;
                            }
                        }
                    },
                    /*
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var str = '---';
                            if (row.is_lock == 1) {
                                str = '<a dealing="' + row.is_lock + '" attr="' + row.id + '" href="javascript:;" class=" btn-xs text-primary lock">解锁</a>';
                            }else{
                                str = '<a dealing="' + row.is_lock + '" attr="' + row.id + '" href="javascript:;" class=" btn-xs text-primary lock">锁定</a>';
                            }
                            return str;
                        }
                    },
                     */
                    {
                        'targets': -1,
                        'render': function (data, type, row) {
                            var str = ' <a refresh="' + row.id + '" href="javascript:;" title="点击获取此用户最新余额" class=" btn-xs text-primary refresh">'+row.balance+'</a>';
                            return str;
                        }
                    }
                ]
            });
            $("table").on( 'click', '.lock', function () {
                var _a = $(this);
                var is_lock = $(this).attr('dealing');
                var label_lock = '';
                if(is_lock == 1){
                    label_lock = '解锁';
                }else{
                    label_lock = '锁定';
                }
                if (confirm("确认"+label_lock+"该用户吗？")) {
                    var id = $(this).attr('attr');
                    loadShow();
                    $.ajax({
                        url: "/thirdgameuser/lock",
                        dataType: "json",
                        method: "POST",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {
                            id: id
                        }
                    }).done(function (json) {
                        loadFadeOut();
                        if (json.status == 0) {
                            if(is_lock == 1){
                                _a.attr('dealing',  0).html('锁定');
                            }else{
                                _a.attr('dealing',  1).html('解锁');
                            }
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
                }
            });
            $("table").delegate('.refresh', 'click', function () {
                var id = $(this).attr('refresh');
                loadShow();
                $.ajax({
                    url: "/thirdgameuser/refresh?id=" + id,
                    dataType: "json",
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id: id
                    }
                }).done(function (json) {
                    loadFadeOut();
                    var _a = $('a[refresh="' + id + '"]');
                    if (json.status == 0) {
                        if(json.balance == -1){
                         _a.html('远程用户记录不存在，已删除本地记录');
                        }
                        _a.html(json.balance);
                    } else {
                        _a.html('更新失败');
                    }
                });
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