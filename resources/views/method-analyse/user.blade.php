@extends('layouts.base')
@section('title','用户彩票玩法分析')
@section('function','用户彩票玩法分析')
@section('function_link','/methodanalyse/?mode=user')
@section('here','用户彩票玩法分析')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <form class="form-horizontal">
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
                                <label class="col-sm-3 control-label">日期</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control" name="start_date" id="start_date" value="{{$start_date}}" placeholder="开始日期" autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control" name="end_date" id="end_date" value="{{$end_date}}" placeholder="结束日期" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">彩种</label>
                                <div class="col-sm-9">
                                    <select name="lottery_id" class="form-control">
                                        <option value="0">所有彩种</option>
                                        @foreach ($lottery_list as $k=>$lotteries)
                                            <optgroup label="{{$k}}">
                                                @foreach($lotteries as $lottery)
                                                    <option value='{{ $lottery->id }}'>{{ $lottery->name }}[{{ $lottery->ident }}]</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='username' autocomplete="off" placeholder="用户名"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="submit" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                        <button type="button" class="btn btn-default margin" onclick="location.href='/methodanalyse/?mode=platform'"><i class="fa fa-retweet" aria-hidden="true"></i>平台模式</button>
                    </div>
                </form>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">ID</th>
                            <th class="hidden-sm">日期</th>
                            <th class="hidden-sm">彩种</th>
                            <th class="hidden-sm">玩法</th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">销售总额</th>
                            <th class="hidden-sm">返点</th>
                            <th class="hidden-sm">实际销售总额</th>
                            <th class="hidden-sm">中奖</th>
                            <th class="hidden-sm">盈亏</th>
                            <th class="hidden-sm">RTP</th>
                            <th class="hidden-sm">投注单数</th>
                            <th class="hidden-sm">中奖单数</th>
                            <th class="hidden-sm">中奖率(%)</th>
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
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD',
        };
        laydate(layConfig);
        layConfig.elem = '#end_date';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'lottery_id': $("select[name='lottery_id']").val(),
                    'username': $("input[name='username']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[1, "desc"]],
                serverSide: true,
                pageLength: 50,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "belong_date"},
                    {"data": "lottery_name"},
                    {"data": "method_name"},
                    {"data": "username"},
                    {"data": "price"},
                    {"data": "rebate"},
                    {"data": "real_price"},
                    {"data": "bonus"},
                    {"data": "profit"},
                    {"data": "rtp"},
                    {"data": "bet_count"},
                    {"data": "win_count"},
                    {"data": "win_percent"},
                ],
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

            $('#search_btn').click(function (event) {
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
