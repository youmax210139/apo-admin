@extends('layouts.base')
@section('title','幸运大奖池【'.$period_info->period.'】抽奖号码列表')
@section('function','幸运大奖池')
@section('function_link', '/activity/')
@section('here','幸运大奖池【'.$period_info->period.'】抽奖号码列表')
@section('content')
    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">

        </div>

        <div class="col-md-6 text-right">
            @if(Gate::check('activity/jackpot'))
                <a href="/activity/jackpot" class="btn btn-primary btn-md"><i class="fa fa-backward"></i> 返回期号列表 </a>
            @endif
    </div>
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
            【{{ $period_info->period }}】期 开始时间：{{ $period_info->start_at }} - 结束时间：{{ $period_info->end_at }}
        </div>
        <div class="col-md-6 text-right">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">期号</th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">领取号码</th>
                            <th class="hidden-sm">领取时间</th>
                            <th class="hidden-sm">领取时销量</th>
                            <th class="hidden-sm">奖级</th>
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
    <script>
        $(function () {
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                pageLength: 50,
                ordering: false,
                searching: true,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax('?type=user_code&period={{ $period_info->period }}', null),
                "columns": [
                    {"data": "id"},
                    {"data": "period"},
                    {"data": "username"},
                    {"data": "code"},
                    {"data": "created_at"},
                    {"data": "current_total_bet"},
                    {"data": "prize_level"}
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var  str = '未中奖';
                            if(row.prize_level == 1) {
                                str = '<span class="text-red">一等奖</span>';
                            } else if(row.prize_level == 2) {
                                str = '<span class="text-orange">二等奖</span>';
                            }
                            return str;
                        }
                    }
                ]
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                table.column(0).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
                loadFadeOut();
            });

            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
        });
    </script>
@stop