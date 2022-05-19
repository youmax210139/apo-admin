@extends('layouts.base')
@section('title','抽返水查询-'.$data_types[$param['data_type']])
@section('function','抽返水查询')
@section('function_link', '/pump/')
@section('here',$data_types[$param['data_type']])
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" id="search" action="/userreport/" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                                <label class="col-sm-3 control-label text-right">数据类型</label>
                                <div class="col-sm-9" >
                                    <select class="form-control" name="data_type" onchange="location.href='/pump/?data_type='+this.value">
                                        @foreach($data_types as $_data_type => $data_label)
                                            <option value="{{ $_data_type }}" @if($_data_type == $param['data_type']) selected @endif>{{ $data_label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name='username' value="{{$param['username']}}" placeholder="会员名"/>
                                </div>
                                <label class="control-label checkbox-inline col-sm-4" style="padding-top: 7px;">
                                    <input type="checkbox" name="include_all" value="1" @if($param['include_all'] == 1) checked @endif />
                                    包含所有下级
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-3 control-label">用户组别</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="0">所有组别</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}" @if($item->id== $param['user_group_id'] ) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_time" class="col-sm-3 control-label">计算时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="start_time" id="start_time"
                                               value="{{$param['start_time']}}" placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control " name="end_time" id="end_time"
                                               value="{{$param['end_time']}}" placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_min" class="col-sm-3 control-label">{{$data_types[$param['data_type']]}}金额</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control " name="amount_min"
                                               value="{{$param['amount_min']}}" placeholder="最低金额">
                                        <span class="input-group-addon">至</span>
                                        <input type="text" class="form-control " name="amount_max"
                                               value="{{$param['amount_max']}}" placeholder="最高金额">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="0" @if($param['status'] == 0 ) selected @endif>全部</option>
                                        <option value="1" @if($param['status'] == 1 ) selected @endif>已计算</option>
                                        <option value="2" @if($param['status'] == 2 ) selected @endif>已抽水</option>
                                        <option value="3" @if($param['status'] == 3 ) selected @endif>已返水</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">彩种</label>
                                <div class="col-sm-9">
                                    <select name="lottery_id" class="form-control lottery_id">
                                        <option value=''>所有彩种</option>
                                        @foreach ($lottery_list as $k=>$lotteries)
                                            <optgroup label="{{$k}}">
                                                @foreach($lotteries as $lottery)
                                                    <option value='{{ $lottery->id }}' ident='{{ $lottery->ident }}' @if($param['lottery_id'] == $lottery->id ) selected @endif>{{ $lottery->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">奖期</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='issue' value="{{$param['issue']}}" placeholder="注号编号"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">注号编号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='project_no' value="{{$param['project_no']}}" placeholder="注号编号"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <button type="button" class="btn btn-primary margin" id="search_btn">
                            <i class="fa fa-search" aria-hidden="true"></i>查询
                        </button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->
            <div class="box box-primary">
                <style>
                    table.dataTable.table-condensed tr > th {
                        text-align: center;
                        vertical-align: middle;
                    }
                    table.dataTable.table-condensed tbody > tr > td {
                        text-align: center;
                        vertical-align: middle;
                    }
                </style>
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="true">#</th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" data-sortable="false">用户级别</th>
                            <th class="hidden-sm" data-sortable="false">用户组别</th>
                            <th class="hidden-sm" data-sortable="false">彩种</th>
                            <th class="hidden-sm" data-sortable="false">奖期</th>
                            <th class="hidden-sm" data-sortable="false">注单</th>
                            <th class="hidden-sm" data-sortable="false">基数</th>
                            <th class="hidden-sm" data-sortable="false">比例</th>
                            <th class="hidden-sm" >{{$data_types[$param['data_type']]}}金额</th>
                            @if($param['data_type'] == 'inlet')
                                <th class="hidden-sm" >返水金额</th>
                            @endif
                            <th class="hidden-sm" >状态</th>
                            <th class="hidden-sm" >计算时间</th>
                            <th class="hidden-sm" >@if($param['data_type'] == 'inlet') 抽水 @else 返水 @endif时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="9" class="text-right"><b>本页总计： </b></th>
                            <th></th>
                            @if($param['data_type'] == 'inlet')
                                <th></th>
                            @endif
                            <th></th>
                        </tr>
                        <tr >
                            <th colspan="9" class="text-right"><b>全部总计： </b></th>
                            <th id="total_sum"></th>
                            @if($param['data_type'] == 'inlet')
                                <th id="total_outlet_sum"></th>
                            @endif
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-detail" tabIndex="-1" role="dialog">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content"></div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>

        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_time',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);
        layConfig.elem = '#end_time';
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'data_type': $('select[name="data_type"]').val(),
                    'username': $("input[name='username']").val(),
                    'include_all': $("input[name='include_all']:checked").val(),
                    'user_group_id': $('select[name="user_group_id"]').val(),
                    'start_time': $("input[name='start_time']").val(),
                    'end_time': $("input[name='end_time']").val(),
                    'amount_min': $("input[name='amount_min']").val(),
                    'amount_max': $("input[name='amount_max']").val(),
                    'status': $('select[name="status"]').val(),
                    'lottery_id': $('select[name="lottery_id"]').val(),
                    'issue': $("input[name='issue']").val(),
                    'project_no': $("input[name='project_no']").val(),
                };
                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "desc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax('/pump/index', null, get_params),
                "columns": [
                    {"data": "id"},
                    {"data": "username"},
                    {"data": "user_type_name"},
                    {"data": "user_group_name"},
                    {"data": "lottery_name"},
                    {"data": "issue"},
                    {"data": "project_id"},
                    {"data": "cardinal"},
                    {"data": "scale"},
                    {"data": "amount"},
                    @if($param['data_type'] == 'inlet')
                    {"data": "outlet_amount"},
                    @endif
                    {"data": "status"},
                    {"data": "created_at"},
                    {"data": "updated_at"},
                ],
                columnDefs: [
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            return '<a href="/user/detail?id='
                                + row['user_id'] + '" mounttabs="" title="用户详情['+row['username']+']">'
                                + row.username +'</a>';
                        }
                    },{
                        'targets': 2,
                        'render': function (data, type, row) {
                            var label = 'label-success';

                            if (row.user_type_id == 1 ) {
                                label = 'label-warning';
                            } else if (row.user_type_id == 2 ) {
                                label = 'label-primary';
                            }
                            return app.getLabelHtml(data, label);
                        }
                    },
                    {
                        'targets': 3,
                        'render': function (data, type, row) {
                            var label = 'label-success';

                            if (row.user_group_id == 2 ) {
                                label = 'label-warning';
                            } else if (row.user_group_id == 3 ) {
                                label = 'label-danger';
                            }
                            return app.getLabelHtml(data, label);
                        }
                    },{
                        'targets': 6,
                        'render': function (data, type, row) {
                            //
                            //href="/project/detail?id=JBNKHEBOTB"
                            /*return '<a id="'+row['id']+'" href="/project/detail?id='
                                + row['project_id'] + '" mounttabs="" title="'+row['username']+'投注详情['+row['project_id']+']">'
                                + row.project_id +'</a>';*/
                            return '<a href="/pump/detail?id='
                                + row['project_id'] + '" data-target="#modal-detail" data-toggle="modal" title="'+row['username']+'抽返水详情['+row['project_id']+']">'
                                + row.project_id +'</a>';
                        }
                    },{
                        'targets': 8,
                        'render': function (data, type, row) {
                            //
                            //href="/project/detail?id=JBNKHEBOTB"
                            /*return '<a id="'+row['id']+'" href="/project/detail?id='
                                + row['project_id'] + '" mounttabs="" title="'+row['username']+'投注详情['+row['project_id']+']">'
                                + row.project_id +'</a>';*/
                            return data * 100;
                        }
                    },{

                        'targets': @if($param['data_type'] == 'inlet') 11 @else 10 @endif,
                        'render': function (data, type, row) {
                            var label = 'label-danger';
                            var text = '未知状态';

                            if (row.status == 1 ) {
                                label = 'label-warning';
                                text = '已计算';
                            } else if (row.status == 2 ) {
                                label = 'label-primary';
                                text = '已抽水';
                            } else if (row.status == 3 ) {
                                label = 'label-success';
                                text = '已返水';
                            }
                            return app.getLabelHtml(text, label);
                        }
                    },
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                    var amount  = 0;
                    var outlet_amount =  0;
                    for(x in data){
                        var row = data[x];
                        amount        += parseFloat(row['amount']);
                        if( row['outlet_amount'] !== undefined && row['outlet_amount'] !== null ){
                            outlet_amount        += parseFloat(row['outlet_amount']);
                        }
                    }

                    $(tfoot).find('th').eq(1).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(amount)
                    );
                    /*if(outlet_amount > 0){
                        $(tfoot).find('th').eq(2).html(
                            new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(outlet_amount)
                        );
                    }*/
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
                /*if(json['outlet_sum'] !== undefined && json !== null ){
                    $('#total_outlet_sum').html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(json['outlet_sum'])
                    );
                }*/
            });

            $('#search_btn').click(function () {
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
