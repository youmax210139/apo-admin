@extends('layouts.base')

@section('title','积分账变管理')
@section('function','积分账变管理')
@section('function_link', '/pointorders/')
@section('here','积分账变列表')

@section('content')
<div class="row">
<div class="col-md-12">
    <!--搜索框 Start-->
    <div class="box box-primary">
        <form id="search" class="form-horizontal">
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
                    <div class="form-group search_username">
                        <label class="col-sm-3 control-label">用户名</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="username" value="{{$username}}" placeholder="用户名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">时间</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="start_date" value="{{ $start_date }}"  id='start_date' placeholder="开始时间">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="end_date" value="{{ $end_date }}"  id='end_date' placeholder="结束时间">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">编号</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name='order_no' placeholder="账变编号" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label text-right app_label_pt9px">类型</label>
                        <div class="col-sm-9">
                            <select name="order_type" class="form-control">
                                <option value="all">所有类型</option>
                                <option value="0">增加</option>
                                <option value="1">扣除</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">积分</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="amount_min" placeholder="最小积分">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="amount_max" placeholder="最大积分">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">管理员</label>
                        <div class="col-sm-9">
                            <select name="admin_user_id" class="form-control" >
                                <option value='0'>不限</option>
                                @foreach ($admin_list as $admin)
                                    <option value='{{ $admin->id }}'>{{ $admin->username }}</option>
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
                    <button type="reset" class="btn btn-default col-sm-2" ><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                </div>
            </div>
	   </form>
    </div>
    <!--搜索框 End-->

    <div class="box box-primary">
        <div class="box-body">
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                    <tr>
                        <th class="hidden-sm" data-sortable="false"></th>
                        <th class="hidden-sm" data-sortable="false">账变编号</th>
                        <th class="hidden-sm">账变时间</th>
                        <th class="hidden-sm" data-sortable="false">用户名</th>
                        <th class="hidden-sm" data-sortable="false">账变类型</th>
                        <th class="hidden-sm">增加</th>
                        <th class="hidden-sm">扣除</th>
                        <th class="hidden-sm">积分余额</th>
                        <th class="hidden-sm" data-sortable="false">账变原因</th>
                        <th class="hidden-sm" data-sortable="false">备注</th>
                        <th class="hidden-sm" data-sortable="false">管理员</th>
                        <th class="hidden-sm" data-sortable="false">相关编号</th>
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
                    'order_type'     : $("select[name='order_type']").val(),
                    'order_no'       : $("input[name='order_no']").val(),
                    'admin_user_id'  : $("select[name='admin_user_id']").val(),
                    'start_date'     : $("input[name='start_date']").val(),
                    'end_date'       : $("input[name='end_date']").val(),
                    'amount_min'     : $('input[name="amount_min"]').val(),
                    'amount_max'     : $('input[name="amount_max"]').val(),
                    'username'		 : $('input[name="username"]').val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language:app.DataTable.language(),
                order: [[2, "desc"]],
                serverSide: true,
                pageLength:25,
                searching:false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    null,
                	{"data": "order_id"},
                	{"data": "created_at"},
                	{"data": "username","orderable":false},
                	{"data": "order_type","orderable":false},
                    {"data": "amount_add"},
                    {"data": "amount_sub"},
                	{"data": "points"},
                	{"data": "relate_type","orderable":false},
                    {"data": "description","orderable":false},
                	{"data": "adminname","orderable":false},
                	{"data": "relate_id","orderable":false},
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "visible": false,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return '';
                        }
                    },
                    {
                        "targets": 5,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data), 'text-green', true);
                        }
                    },
                    {
                        "targets": 6,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data), 'text-red', true);
                        }
                    }
                ],
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            $('#search').submit(function(event){
            	event.preventDefault();
                table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function(){
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

        });
    </script>
@stop
