@extends('layouts.base')

@section('title','追号纪录查询')
@section('function','追号纪录')
@section('function_link', '/task/')
@section('here','追号纪录查询')

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
                            <div class="form-group">
                                <label class="col-sm-3 control-label">追号单编号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name='task_no' placeholder="注单编号" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">追号时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" value="{{ $start_date }}"  id='start_date'   placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" value="{{ $end_date }}"  id='end_date'  placeholder="结束时间">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right app_label_pt9px">用户搜索</label>
                                <div class="col-sm-9">
                                    <div class="col-sm-6 form-control app_wauto_bnone">
                                        <label class="radio-inline"><input type="radio" name="search_type" value="1" checked>手动输入</label>
                                        <label class="radio-inline"><input type="radio" name="search_type" value="2">总代列表</label>
                                    </div>
                                    <div class="col-sm-6 form-control app_wauto_bnone">
                                        <label class="checkbox-inline"><input type="checkbox" name="included_sub_agent">包含下级</label>
                                        <label class="checkbox-inline lbl_no_included_zongdai" style="display: none;"><input type="checkbox" name="no_included_zongdai">不计总代</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group search_username">
                                <label class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username" placeholder="用户名">
                                </div>
                            </div>

                            <div class="form-group search_agent" style="display: none;">
                                <label class="col-sm-3 control-label">总代</label>
                                <div class="col-sm-9">
                                    <select name="zongdai" class="form-control" >
                                        <option value=''>所有总代</option>
                                        @foreach ($zongdai_list as $v)
                                            <option value='{{ $v->id }}'>{{ $v->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">IP 地址</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="ip" placeholder="用户 IP 地址">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">追号金额</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="amount_min" placeholder="最小金额">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="amount_max" placeholder="最大金额">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">追号模式</label>
                                <div class="col-sm-9">
                                    <select name="mode" class="form-control" >
                                        <option value=''>所有模式</option>
                                        @foreach ($mode_list as $k=>$v)
                                            <option value='{{ $k }}'>{{ $v['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">用户组别</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="0">所有组别</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}" @if($item->id==1) selected @endif>{{ $item->name }}</option>
                                        @endforeach
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
                                                    <option value='{{ $lottery->id }}' ident='{{ $lottery->ident }}'>{{ $lottery->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group method_div">
                                <label class="col-sm-3 control-label text-right">玩法选择</label>
                                <div class="col-sm-9">
                                    <select name='method_id' class='form-control'>
                                        <option value=''>玩法列表</option>
                                        <!--js生成对应玩法列表-->
                                    </select>
                                </div>
                            </div>

                            <div class="form-group issue_div">
                                <label class="col-sm-3 control-label text-right">奖期</label>
                                <div class="col-sm-9">
                                    <input name="issue" list="issue_datalist" class="form-control" placeholder="奖期">
                                    <datalist id="issue_datalist">
                                        <option value=''>奖期列表</option>
                                        <!--js生成对应奖期列表-->
                                    </datalist>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">客户端类型</label>
                                <div class="col-sm-9">
                                    <select name="client_type" class="form-control" >
                                        <option value=''>所有类型</option>
                                        @foreach ($source_list as $k=>$v)
                                            <option value='{{ $k }}'>{{ $v }}</option>
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
                            <th class="hidden-sm" data-sortable="false">追号编号</th>
                            <th class="hidden-sm" data-sortable="false">游戏用户</th>
                            <th class="hidden-sm">追号时间</th>
                            <th class="hidden-sm" data-sortable="false">彩种</th>
                            <th class="hidden-sm" data-sortable="false">玩法</th>
                            <th class="hidden-sm" data-sortable="false">模式</th>
                            <th class="hidden-sm">开始期号</th>
                            <th class="hidden-sm">追号期数</th>
                            <th class="hidden-sm" data-sortable="false">完成期数</th>
                            <th class="hidden-sm" data-sortable="false">取消期数</th>
                            <th class="hidden-sm">追号总金额</th>
                            <th class="hidden-sm">完成金额</th>
                            <th class="hidden-sm">取消金额</th>
                            <th class="hidden-sm" data-sortable="false">追中即停</th>
                            <th class="hidden-sm" data-sortable="false">追号状态</th>
                            <th class="hidden-sm" data-sortable="false">来源</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="11" class="text-right"><b>本页总计： </b></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="3"></th>
                        </tr>
                        </tfoot>

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
                    'task_no'       : $("input[name='task_no']").val(),
                    'ip'             : $("input[name='ip']").val(),
                    'start_date'     : $("input[name='start_date']").val(),
                    'end_date'       : $("input[name='end_date']").val(),
                    'amount_min'     : $('input[name="amount_min"]').val(),
                    'amount_max'     : $('input[name="amount_max"]').val(),
                    'mode'           : $('select[name="mode"]').val(),
                    'client_type'    : $('select[name="client_type"]').val(),
                    'user_group_id'       : $('select[name="user_group_id"]').val(),
                    'lottery_id'     : $('select[name="lottery_id"]').val(),
                    'method_id'           : $('select[name="method_id"]').val(),
                    'issue'           : $('input[name="issue"]').val(),
                    'included_sub_agent'  : $('input[name="included_sub_agent"]').prop('checked') ? 1 : 0,
                    'no_included_zongdai' : $('input[name="no_included_zongdai"]').prop('checked') ? 1 : 0,
                    'zongdai  '           : $('select[name="zongdai"]').val(),
                    'search_type'    : $('input[name="search_type"]:checked').val(),
                };

                if (param['search_type'] == 1) {
                    param['username'] = $('input[name="username"]').val();
                } else {
                    param['zongdai'] = $('select[name="zongdai"]').val();
                }

                return $.extend({}, data, param);
            }

            var table = $("#tags-table").DataTable({
                language:app.DataTable.language(),

                order: [[3, "desc"]],
                serverSide: true,
                pageLength:25,
                searching:false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    null,
                    {"data": "task_no"},
                    {"data": "username","orderable":false},
                    {"data": "created_at"},
                    {"data": "lottery_name"},
                    {"data": "method_name"},
                    {"data": "mode"},
                    {"data": "begin_issue"},
                    {"data": "issue_count"},
                    {"data": "finished_count"},
                    {"data": "cancel_count"},
                    {"data": "task_price"},
                    {"data": "finish_price"},
                    {"data": "cancel_price"},
                    {"data": "stop_on_win","orderable":false},
                    {"data": "status","orderable":false},
                    {"data": "client_type_label"}
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
                        "targets": 1,
                        "searchable": false,
                        "render": function (data, type, row) {
                            @if(Gate::check('task/detail'))
                                    return '<a mountTabs title="追号详情['+data+']" href="/task/detail?id='+data+'" target="_blank">'+data+'</a>';
                            @else
                                    return data;
                            @endif
                            //return app.getLabelHtml(data, 'label-primary');
                        }
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            if (row['user_observe']) {
                                return app.getColorHtml(row.username, 'label-danger', false);
                            } else {
                                return row.username;
                            }
                        }
                    },
                    {
                        "targets": 11,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return new Intl.NumberFormat('en', {minimumFractionDigits:4}).format(data)
                        }
                    },
                    {
                        "targets": 12,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return new Intl.NumberFormat('en', {minimumFractionDigits:4}).format(data)
                        }
                    },
                    {
                        "targets": 13,
                        "searchable": false,
                        "render": function (data, type, row) {
                            return new Intl.NumberFormat('en', {minimumFractionDigits:4}).format(data)
                        }
                    },
                    {
                        "targets": 15,
                        "searchable": false,
                        "render": function (data, type, row) {
                            if(data==0){
                                return app.getColorHtml('进行中', 'text-green', true);
                            }else if(data==1){
                                return app.getColorHtml('已取消', 'text-red', true);
                            }else if(data==2)
                            {
                                return app.getColorHtml('已完成', 'text-blue', true);
                            }
                        }
                    }
                ],

                "footerCallback": function ( tfoot, data, start, end, display ){
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?i : 0;
                    };

                    var task_price = 0;
                    var finish_price = 0;
                    var cancel_price = 0;

                    for(item in data){
                        task_price += intVal(data[item].task_price);
                        finish_price += intVal(data[item].finish_price);
                        cancel_price += intVal(data[item].cancel_price);
                    }

                    $(tfoot).find('th').eq(1).html(
                            app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(task_price), 'text-green', true)
                    );
                    $(tfoot).find('th').eq(2).html(
                            app.getColorHtml('+' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(finish_price), 'text-green', true)
                    );
                    $(tfoot).find('th').eq(3).html(
                            app.getColorHtml('-' + new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(cancel_price), 'text-red', true)
                    );
                }

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

            $("input[name='search_type']").change(function(){
                if ($(this).val() == 1) { // 输入用户名
                    $('.search_agent').hide();
                    $('.search_username').show();
                    $('.lbl_no_included_zongdai').hide();
                    $('input[name="no_included_zongdai"]').prop('checked',false);
                    $('select[name="zongdai"]').val('0');
                } else { // 选择总代
                    $('.search_username').hide();
                    $('input[name="username"]').val('');
                    $('.search_agent').show();
                    $('.lbl_no_included_zongdai').show();
                }

            });

            // 彩种选择onchange，联动玩法显示与否效果,隐藏重置为初始值
            $(".lottery_id").change(function(){
                if ($(this).val() >= 1) {
                    lottery_change($(this).find("option:selected").attr("ident"));
                    //异步获取对应选中彩种和时间段的奖期
                    get_issue_list($(this).find("option:selected").attr("ident"));
                } else {
                    $('select[name="method_id"]').html("<option value=''>玩法列表</option>")
                    $('select[name="issue"]').html("<option value=''>奖期列表</option>")
                }
            });

            function lottery_change(ident) {
                //玩法显示
                var method_list = JSON.parse('{!! $lottery_method_list !!}');
                var html = "<option value=''>玩法列表</option>";
                $.each(method_list[ident], function (i, v) {
                    html += "<option value='" + v.id + "'>" + v.name + "</option>";
                });
                $('.method_div .col-sm-9 select').html(html);
            }

            //开始结束时间失去焦点时，判断是否选中彩种，如果选中异步更新奖期
            $('#start_date').blur(function(){
                if ($('select[name="lottery_id"]').val() >= 1) {
                    get_issue_list(
                        $('select[name="lottery_id"]').find("option:selected").attr("ident")
                    );
                }
            });
            $('#end_date').blur(function(){
                if ($('select[name="lottery_id"]').val() >= 1) {
                    get_issue_list(
                        $('select[name="lottery_id"]').find("option:selected").attr("ident")
                    );
                }
            });

            //异步获取对应选中彩种和时间段的奖期
            function get_issue_list(ident){
                $.ajax({
                    type: "POST",
                    url: ".?get_issue=1",
                    dataType: 'JSON',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'start_date': $("input[name='start_date']").val(),
                        'end_date': $("input[name='end_date']").val(),
                        //'ident':  $('select[name="lottery_id"]').find("option:selected").attr("ident"),
                        'ident':  ident,
                    },
                    success: function (json_data) {
                        var html = "<option value=''>奖期列表</option>";
                        $.each(json_data, function (i, v) {
                            html += "<option value='" + v.issue + "'>" + v.issue + "</option>";
                        });
                        $('.issue_div .col-sm-9 datalist').html(html);
                    }
                });
            }

        });
    </script>
@stop
