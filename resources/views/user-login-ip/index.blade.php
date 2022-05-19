@extends('layouts.base')

@section('title','同IP统计表')

@section('function','同IP统计表')
@section('function_link', '/userloginip/')

@section('here','同ip统计表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="row">
<div class="col-sm-12">
     @include('partials.errors')
                @include('partials.success')
    <!--搜索框 Start-->
    <div class="box box-primary">
        <form class="form-horizontal" id="search" method="post" action="/Userloginip">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="export" value="1"/>
            <div class="box-header with-border">
                <h3 class="box-title"><!--搜索查询区--></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="created_date" class="col-sm-2 control-label">时间</label>
                            <div class="col-sm-10">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="start_date" id='start_date' value="{{$start_date}}" placeholder="开始时间" autocomplete="off">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="end_date" id='end_date' value="{{$end_date}}" placeholder="结束时间" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer text-center">
                    <button type="submit" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                    <button type="button" onclick="$('#search').submit()" class="btn btn-warning margin" id="export"><i class="fa fa-download" aria-hidden="true"></i>导出</button>
            </div>
	   </form>
    </div>

    <div class="box box-primary">
        <div class="box-body">
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th class="hidden-sm" data-sortable="false">IP</th>
                    <th class="hidden-sm" >用此IP登入账号数</th>
                    <th class="hidden-sm" data-sortable="false">操作</th>
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
<script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig ={
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
        };
        laydate(layConfig);

        layConfig.elem = '#end_date';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'start_date'     : $("input[name='start_date']").val(),
                    'end_date'       : $("input[name='end_date']").val(),
                };
                return $.extend({}, data, param);
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "ips"},
                    {"data": "sum"},
                ],
                createdRow: function (row, data, index) {
                },
                columnDefs: [
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            return '<a href="javascript:;" attr="' + row.ips + '" class="X-Small btn-xs text-primary detail">详情</a>';
                        }
                    }
                ]
            });
            $(document).on( 'click',".detail", function () {
                var ip = $(this).attr('attr');
                var start_date = $("input[name='start_date']").val().replace(' ', '_');
                var end_date = $("input[name='end_date']").val().replace(' ', '_');
                BootstrapDialog.show({
                    title:'同IP记录>详情',
                    message: $('<div></div>').load("/userloginip/detail?start_date="+start_date+"&end_date="+end_date+"&ip="+ip),
                    buttons: [{
                        label: '确认',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            $('#search_btn').click(function(event){
                event.preventDefault();
                table.ajax.reload();
                return false;
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
        });
    </script>
@stop
