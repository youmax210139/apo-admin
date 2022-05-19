@extends('layouts.base')

@section('title','活动管理')

@section('function','活动管理')
@section('function_link', '/activity/')

@section('here','活动领取记录')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">

        </div>
    </div>
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form id="search" class="form-horizontal" action="/activity/record/" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="export" value="1"/>
                    <input type="hidden" name="id" value="{{$id}}"/>
                    <div class="box-header with-border">
                        <h3 class="box-title"><!--搜索查询区--></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' autocomplete="off" placeholder="用户名"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">领取时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date"
                                               value="{{ $start_date }}" id='start_date' placeholder="开始时间" autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date"
                                               value="{{ $end_date }}" id='end_date' placeholder="结束时间" autocomplete="off">
                                    </div>
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
                                <label class="col-sm-3 control-label">金额</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="amount_min"
                                               placeholder="最小金额">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="amount_max"
                                               placeholder="最大金额">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search"
                                                                                                aria-hidden="true"></i>查询
                        </button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh"
                                                                               aria-hidden="true"></i>重置
                        </button>
                        <button type="submit" class="btn btn-warning margin"><i class="fa fa-download"
                                                                                aria-hidden="true"></i>导出
                        </button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">活动名称</th>
                            <th class="hidden-sm">领取时间</th>
                            <th class="hidden-sm">金额</th>
                            @if ($activity->draw_method==1)
                                <th class="hidden-sm">管理员IP</th>
                            @elseif ($activity->draw_method==0)
                                <th class="hidden-sm">领取IP</th>
                            @else
                                <th class="hidden-sm">IP</th>
                            @endif
                            <th class="hidden-sm">状态</th>
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
                    'username'       : $("input[name='username']").val(),
                    'ip'             : $("input[name='ip']").val(),
                    'start_date'     : $("input[name='start_date']").val(),
                    'end_date'       : $("input[name='end_date']").val(),
                    'amount_min'     : $('input[name="amount_min"]').val(),
                    'amount_max'     : $('input[name="amount_max"]').val()
                };

                if (param['search_type'] == 1) {
                    param['username'] = $('input[name="username"]').val();
                } else {
                    param['zongdai'] = $('select[name="zongdai"]').val();
                }

                return $.extend({}, data, param);
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                iDisplayLength: 25,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                searching: false,
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "username", "orderable": false},
                    {"data": "activity_name", "orderable": false},
                    {"data": "record_time", "orderable": true},
                    {"data": "draw_money", "orderable": true},
                    {"data": "ip", "orderable": false},
                    {"data": "status", "orderable": false},
                ],
                columnDefs: [

                    {
                        'targets': -1,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(
                                row['status'] ? '已发' : '未发',
                                'label-' + (row['status'] ? 'success' : 'danger')
                            );
                        }
                    }
                ]
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });
            $('#search_btn').click(function(event){
                event.preventDefault();
                table.ajax.reload();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
            table.on('draw.dt', function () {
                table.column(0).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
                loadFadeOut();
            });
        });
    </script>
@stop