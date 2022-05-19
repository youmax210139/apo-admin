@extends('layouts.base')

@section('title','杀号日志查询')

@section('function','杀号日志查询')
@section('function_link', '/killcodelog/')

@section('here','杀号日志列表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/killcodelog/" method="post">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date" class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date"
                                               id='start_date' value="{{$start_date}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date"
                                               id='end_date' value="{{$end_date}}" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">本地彩种</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='local_lottery' id="local_lottery"
                                           placeholder="本地彩种"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">本地奖期</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='local_issue' id="local_issue"
                                           placeholder="本地奖期"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">第三方标识</label>
                                <div class="col-sm-9">
                                    <select name="third_ident" class="form-control">
                                        <option value="0" selected="selected"></option>
                                        <option value="wincodes">wincodes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">第三方彩种</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='third_lottery' id="third_lottery"
                                           placeholder="第三方彩种标识"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">第三方奖期</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='third_issue' id="third_issue"
                                           placeholder="第三方奖期"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type" class="col-sm-3 control-label">点杀功能</label>
                                <div class="col-sm-9">
                                    <select name="flag_switch" class="form-control">
                                        <option value="-1" selected="selected">全部</option>
                                        <option value="0">无</option>
                                        <option value="1">有</option>
                                        <option value="2">次数不足</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="type" class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select name="error" class="form-control">
                                        <option value="-1" selected="selected">全部</option>
                                        <option value="0">正常</option>
                                        <option value="1">本地无对应彩种</option>
                                        <option value="2">本地彩种不存在</option>
                                        <option value="3">无对应分隔符号</option>
                                        <option value="4">本地奖期不存在</option>
                                        <option value="5">推送发生异常</option>
                                        <option value="6">推送返回空值</option>
                                        <option value="7">推送返回解析失败</option>
                                        <option value="8">推送返回缺少顺利标识</option>
                                        <option value="9">重复发送</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="step" class="col-sm-3 control-label">步骤</label>
                                <div class="col-sm-9">
                                    <select name="step" class="form-control">
                                        <option value="-1" selected="selected">全部</option>
                                        <option value="0">初始化</option>
                                        <option value="1">本地匹配</option>
                                        <option value="2">计算完成</option>
                                        <option value="3">推送完成</option>
                                    </select>
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
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modal-detail" tabIndex="-1" role="dialog">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content"></div>
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
                    <th data-sortable="true">ID</th>
                    <th data-sortable="false">第三方</th>
                    <th data-sortable="false">第三方彩种</th>
                    <th data-sortable="false">第三方奖期</th>
                    <th data-sortable="true">注单数</th>
                    <th data-sortable="true">投注和值</th>
                    <th data-sortable="false">点杀</th>
                    <th data-sortable="false">方式</th>
                    <th data-sortable="false">步骤</th>
                    <th data-sortable="false">状态</th>
                    <th data-sortable="true">响应请求</th>
                    <th data-sortable="false">计算完成</th>
                    <th data-sortable="false">推送完成</th>
                    <th data-sortable="false">操作</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5" class="text-right"><b>本页总计： </b></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr >
                    <th colspan="5" class="text-right"><b>全部总计： </b></th>
                    <th id="total_sum"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
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
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'local_lottery': $("input[name='local_lottery']").val(),
                    'local_issue': $("input[name='local_issue']").val(),
                    'third_ident': $("select[name='third_ident']").val(),
                    'third_lottery': $("input[name='third_lottery']").val(),
                    'third_issue': $("input[name='third_issue']").val(),
                    'flag_switch': $("select[name='flag_switch']").val(),
                    'error': $("select[name='error']").val(),
                    'step': $("select[name='step']").val(),
                };
                console.log(param);
                return $.extend({}, data, param);
            };

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [],
                serverSide: true,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "third_ident"},
                    {"data": "third_lottery"},
                    {"data": "third_issue"},
                    {"data": "all_bet_count"},
                    {"data": "all_bet_sum"},
                    {"data": "flag_switch"},
                    {"data": "mode"},
                    {"data": "step"},
                    {"data": "error"},
                    {"data": "created_at"},
                    {"data": "calculated_at"},
                    {"data": "posted_at"},
                    {"data": null},
                ],
                columnDefs: [
                    {
                        'targets': -8, "render": function (data, type, row) {
                        if (data == 0) {
                            return app.getLabelHtml('一般', 'label-success');
                        } else if (data == 1) {
                            return app.getLabelHtml('黑名单', 'label-primary', false);
                        } else if (data == 2) {
                            return app.getLabelHtml('次数不足', 'label-warning', false);
                        } else {
                            return app.getLabelHtml('其他[ ' + data + ' ]', 'label-danger', false);
                        }

                        //return '<a href="/killcodelog/detail?id=' + row['id'] + '&type=' + row['type'] + '" class="X-Small btn-xs text-success " data-target="#modal-detail" data-toggle="modal"><i class="fa fa-file-text-o"></i> 详情</a>';
                    }
                    }, {
                        'targets': -7, "render": function (data, type, row) {
                            if (data == 0) {
                                return app.getLabelHtml('WEB', 'label-success');
                            } else if (data == 1) {
                                return app.getLabelHtml('CLI', 'label-primary');
                            } else {
                                return app.getLabelHtml('其他[ ' + data + ' ]', 'label-danger');
                            }
                        }
                    }, {
                        'targets': -6, "render": function (data, type, row) {
                            if (data == 0) {
                                return app.getLabelHtml('响应返回', 'label-warning');
                            } else if (data == 1) {
                                return app.getLabelHtml('本地适配', 'label-warning');
                            } else if (data == 2) {
                                return app.getLabelHtml('计算完成', 'label-warning');
                            } else if (data == 3) {
                                return app.getLabelHtml('推送完成', 'label-success');
                            } else {
                                return app.getLabelHtml('其他[ ' + data + ' ]', 'label-danger');
                            }
                        }
                    }, {
                        'targets': -5, "render": function (data, type, row) {
                            if (data == 0) {
                                return app.getLabelHtml('正常', 'label-success');
                            } else if (data == 1) {
                                return app.getLabelHtml('本地无对应彩种', 'label-warning', false);
                            } else if (data == 2) {
                                return app.getLabelHtml('本地彩种不存在', 'label-warning', false);
                            } else if (data == 3) {
                                return app.getLabelHtml('无对应分隔符号', 'label-warning', false);
                            } else if (data == 4) {
                                return app.getLabelHtml('本地奖期不存在', 'label-warning', false);
                            } else if (data == 5) {
                                return app.getLabelHtml('推送发生异常', 'label-warning', false);
                            } else if (data == 6) {
                                return app.getLabelHtml('推送返回空值', 'label-danger', false);
                            } else if (data == 7) {
                                return app.getLabelHtml('推送返回解析失败', 'label-danger', false);
                            } else if (data == 8) {
                                return app.getLabelHtml('推送返回缺少顺利标识', 'label-danger', false);
                            } else if (data == 9) {
                                return app.getLabelHtml('重复发送', 'label-warning', false);
                            } else {
                                return app.getLabelHtml('其他异常[ ' + data + ' ]', 'label-danger', false);
                            }

                            //return '<a href="/killcodelog/detail?id=' + row['id'] + '&type=' + row['type'] + '" class="X-Small btn-xs text-success " data-target="#modal-detail" data-toggle="modal"><i class="fa fa-file-text-o"></i> 详情</a>';
                        }
                    }, {
                        'targets': -1, "render": function (data, type, row) {
                            return '<a href="/killcodelog/detail?id=' + row['id'] + '&type=' + row['type'] + '" class="X-Small btn-xs text-success " data-target="#modal-detail" data-toggle="modal"><i class="fa fa-file-text-o"></i> 详情</a>';
                        }
                    }
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                    var amount  = 0;

                    for(x in data){
                        var row = data[x];
                        amount        += parseFloat(row['all_bet_sum']);
                    }
                    $(tfoot).find('th').eq(1).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(amount)
                    );
                }
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });
            table.on('xhr.dt', function ( e, settings, json, xhr ) {
                if(json['sum'] !== undefined && json !== null ){
                    $('#total_sum').html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(json['sum'])
                    );
                }
            });

            $('#search_btn').click(function (event) {
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
            $("#modal-detail").on('loaded.bs.modal', function () {//数据加载完成后删除loading
                loadFadeOut();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
        });
    </script>
@stop