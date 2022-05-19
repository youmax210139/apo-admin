@extends('layouts.base')

@section('title','开奖管理')

@section('function','开奖管理')
@section('function_link', '/draw/')

@section('here',$lottery->name)

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <style>
        table.dataTable.table-condensed thead > tr > th {
            text-align: center;
            vertical-align: middle;
        }

        table.dataTable.table-condensed tbody > tr > td {
            text-align: center;
            vertical-align: middle;
        }
        .lottery_category {width: 130px; float: left; margin-right: 10px; overflow: hidden;}
        .lottery_category_name {display: block; cursor:default;}
        .lottery_category select {width: 130px;}
        .lottery_select_active {background-color: #d9edf7; font-weight: bold;}
    </style>
    @include('partials.errors')
    @include('partials.success')
    <div class="row page-title-row" style="margin:5px;">
        <div class="box box-primary">
            <div class="box-body">
                @foreach($method_categories as $cate)
                    <div class="lottery_category">
                        <div class="btn lottery_category_name @if($category_id == $cate->id) btn-primary @else btn-default @endif">{{$cate->name}}</div>
                        <select class="form-control lottery_select @if($category_id == $cate->id) lottery_select_active @endif">
                            <option value="">选择彩种</option>
                        @foreach($lotterys as $L)
                            @if($cate->id == $L->lottery_method_category_id)
                            <option value="{{$L->id}}" @if($lottery_id == $L->id) selected="selected" @endif>
                                {{$L->name}} @if($L->status == 0) [停售]@endif
                            </option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                @endforeach

                    <div class="lottery_category">
                        <div class="btn lottery_category_name @if($is_special) btn-primary @else btn-default @endif">自开彩</div>
                        <select class="form-control lottery_select @if($is_special) lottery_select_active @endif">
                            <option value="">选择彩种</option>
                            @foreach($lotterys as $L)
                                @if($L->special > 0)
                                    <option value="{{$L->id}}" @if($lottery_id == $L->id) selected="selected" @endif>
                                        {{$L->name}} @if($L->status == 0) [停售]@endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="lottery_category">
                        <div class="btn btn-success" onclick="location.href=location.href"><i class="fa fa-refresh"></i> 刷新</div>
                    </div>

            </div>
        </div>
        <div class="box box-info">

            <!-- /.box-header -->
            <div class="box-body">

                @if(!empty($wait_issue))
                    <table class="table table-striped table-bordered table-hover">
                        <tbody>
                        <tr>
                            <td>彩种</td>
                            <td>奖期</td>
                            <td>奖期时间</td>
                            <td>状态</td>
                            <td>权重/目标权重</td>
                        </tr>
                        <tr>
                            <td>{{$lottery->name}} <span class="text-gray">{{ $lottery->ident }}</span> </td>
                            <td>{{$wait_issue->issue}}</td>
                            <td>{{$wait_issue->sale_start}} - {{$wait_issue->sale_end}}</td>
                            <td>
                                @if($wait_issue->fetch_status==0)
                                    <code>等待抓号</code>
                                @elseif($wait_issue->fetch_status==1)
                                    <code>抓号中...</code>
                                @else
                                    <code>结束</code>
                                @endif
                            </td>
                            <td style="color:red">{{$wait_issue->rank}}/{{get_config('draw_rank',0)}}</td>
                        </tr>
                        <tr>
                            <td colspan="5" style="color:red">{{$lottery->name}} <span class="text-gray">{{ $lottery->ident }}</span> 第 {{$wait_issue->issue}} 期
                                @if($is_special == 2)
                                    <span style="font-weight: bold; color: #000;">秒秒彩禁止录号</span>
                                @elseif($self_lottery_manual_input == 0 && $is_special == 1)
                                    <span style="font-weight: bold; color: #000;">自主彩禁止录号</span>
                                @else
                                    <input type="text" class="form-control" id="code"
                                           style="width: 200px;display: inline-block"/>
                                    <button type="button" id="enter_code" class="btn btn-primary">录入号码</button>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                @else
                    {{$lottery->name}} <span class="text-gray">{{ $lottery->ident }}</span> 未到开奖时间
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">

                <div class="box-body">
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false">编号</th>
                            <th data-sortable="false">奖期</th>
                            <th data-sortable="false">截止时间</th>
                            <th data-sortable="false">开奖号码</th>
                            <th data-sortable="false">录入时间</th>
                            <th data-sortable="false">录入人</th>
                            <th data-sortable="false">验证时间</th>
                            <th data-sortable="false">验证人</th>
                            <th data-sortable="false">追号</th>
                            <th data-sortable="false">号码</th>
                            <th data-sortable="false">中奖</th>
                            <th data-sortable="false">派奖</th>
                            <th data-sortable="false">扣款</th>
                            <th data-sortable="false">返点</th>
                            <th data-sortable="false">报表</th>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--彩种开售或停售-->
    <div class="modal fade" id="modal-disable" tabIndex="-1">
        <div class="modal-dialog modal-info">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        <i class="fa fa-question-circle fa-lg"></i>
                        确认要<span class="row_isSell_text">录入号码</span>吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="disableForm" method="POST" action="/draw/EnterCode">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="lottery_id" value="{{$lottery->id}}">
                        <input type="hidden" name="issue" value="{{$wait_issue?$wait_issue->issue:''}}">
                        <input type="hidden" name="code" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-info">
                            确认
                        </button>
                    </form>
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
                order: [[0, "asc"]],
                serverSide: true,
                pageLength: 50,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(),
                "columns": [
                    {"data": "id"},
                    {"data": "issue"},
                    {"data": "sale_end"},
                    {"data": "code"},
                    {"data": "write_time"},
                    {"data": "write_admin"},
                    {"data": "verify_time"},
                    {"data": "verify_admin"},
                    {"data": "code_status"},
                    {"data": "task_to_project_status"},
                    {"data": "check_bonus_status"},
                    {"data": "bonus_status"},
                    {"data": "deduct_status"},
                    {"data": "rebate_status"},
                    {"data": "report_status"}
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        'render': function (data, type, row) {
                            if (data < 1) {
                                data = 0;
                            } else if (data >= 1 && data < 5) {
                                data = 1;
                            } else {
                                data = 2;
                            }
                            var status_text = ['--', '→', '√'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[data],
                                'text-' + status_css[data]
                            );
                        }
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            var status_text = ['--', '→', '√'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[data],
                                'text-' + status_css[data]
                            );
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            var status_text = ['--', '→', '√'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[data],
                                'text-' + status_css[data]
                            );
                        }
                    },
                    {
                        'targets': -4,
                        'render': function (data, type, row) {
                            var status_text = ['--', '→', '√'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[data],
                                'text-' + status_css[data]
                            );
                        }
                    },
                    {
                        'targets': -5,
                        'render': function (data, type, row) {
                            var status_text = ['--', '→', '√'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[data],
                                'text-' + status_css[data]
                            );
                        }
                    },
                    {
                        'targets': -7,
                        'render': function (data, type, row) {
                            var status_text = ['--', '→', '√'];
                            var status_css = ['blue', 'red', 'green'];
                            return app.getLabelHtml(
                                status_text[data],
                                'text-' + status_css[data]
                            );
                        }
                    },
                    {
                        'targets': -6,
                        'render': function (data, type, row) {
                            var status_text = ['--', '→', '√','✘'];
                            var status_css = ['blue', 'red', 'green','red'];
                            return app.getLabelHtml(
                                status_text[row['code_status']],
                                'text-' + status_css[row['code_status']]
                            );
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


        });
        //开售，停售
        $("#enter_code").click(function () {
            $("input[name='code']").val($("#code").val());
            $("#modal-disable").modal();
        });

        $(".lottery_select").change(function () {
            var lottery_id = $(this).val();
            location.href="/draw/?lottery_id="+lottery_id;
        });
    </script>
@stop