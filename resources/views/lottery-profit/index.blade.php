@extends('layouts.base')

@section('title','彩票盈亏报表')

@section('function','彩票盈亏报表')
@section('function_link', '/lotteryprofit/')

@section('here','彩票盈亏报表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="row">
<div class="col-sm-12">
     @include('partials.errors')
                @include('partials.success')
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
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="username" class="col-sm-3 col-sm-3 control-label">用户名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='username' placeholder="用户名" />
                                <input type="hidden" name='id' value="{{ $id }}" />
                                <input type="hidden" name="is_search" value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="created_date" class="col-sm-2 control-label">时间范围</label>
                            <div class="col-sm-10">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control form_datetime" name="start_date" id='start_date' value="{{ $start_date }}" placeholder="开始时间" autocomplete="off">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control form_datetime" name="end_date" id='end_date' value="{{ $end_date }}" placeholder="结束时间" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">彩种</label>
                            <div class="col-sm-9">
                                <select name="lottery_id" class="form-control lottery_id">
                                    <option value=''>所有彩种</option>
                                    @foreach ($lottery_list as $k=>$lotteries)
                                        <optgroup label="{{$k}}">
                                            @foreach($lotteries as $lottery)
                                                <option value='{{ $lottery->id }}' ident='{{ $lottery->ident }}'>{{ $lottery->name }}@if($lottery->status==false) [停售]@endif</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group method_div">
                            <label class="col-sm-4 control-label text-right">玩法选择</label>
                            <div class="col-sm-8">
                                <select name="method_id" class="form-control">
                                    <option value="0">选择玩法</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="user_group_id" class="col-sm-4 control-label">用户组别</label>
                            <div class="col-sm-8">
                                <select name="user_group_id" class="form-control">
                                    <option value="0">所有组别</option>
                                    @foreach($user_group as $item)
                                        <option value="{{ $item->id }}" @if($item->id==1) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <div class="btn-group col-md-6">
                    <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search"><i class="fa fa-search" aria-hidden="true"></i> 查询</button>
                </div>
                <div class=" btn-group col-md-6">
                    <button type="reset" class="btn btn-default col-sm-2" ><i class="fa fa-refresh" aria-hidden="true"></i> 重置</button>
                </div>
            </div>
	   </form>
    </div>

    <div class="box box-primary">
        <div class="box-body">
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th class="hidden-sm" data-sortable="false">用户名</th>
                    <th class="hidden-sm" data-sortable="false">用户组</th>
                    <th class="hidden-sm" data-sortable="false">销售总额</th>
                    <th class="hidden-sm" data-sortable="false">返点</th>
                    <th class="hidden-sm" data-sortable="false">实际销售总额</th>
                    <th class="hidden-sm" data-sortable="false">中奖总额</th>
                    <th class="hidden-sm" data-sortable="false">盈亏</th>
                    <th class="hidden-sm" data-sortable="false">操作</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="2" class="text-right"><b>总计：</b></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan="1"></th>
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

        function set(id) {
            $("input[name='id']").val(id);
            $("input[name='is_search']").val(0);
            $("#tags-table").DataTable().ajax.reload();
        }

        $(function () {
        	var get_params = function (data) {
                var param = {
                    'id'             		: $("input[name='id']").val(),
                    'username'       		: $("input[name='username']").val(),
                    'lottery_id'       		: $("select[name='lottery_id']").val(),
                    'method_id'       		: $("select[name='method_id']").val(),
                    'user_group_id'  		: $("select[name='user_group_id']").val(),
                    'start_date'	        : $("input[name='start_date']").val(),
                    'end_date'		        : $("input[name='end_date']").val(),
                    'is_search'       		: $("input[name='is_search']").val(),
                };
                return $.extend({}, data, param);
        	};

        	var json_cb = function(json) {
        		if(typeof(json.parent_tree) !== undefined) {
        			showParentTree(json.parent_tree);
        		} else {
        			showParentTree(null);
        		}
        	};

            var table = $("#tags-table").DataTable({
            	language:app.DataTable.language(),
                order: [],
                serverSide: true,
                dom: "<'row'<'col-sm-12'tr>>",
                pageLength:25,
                searching:false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params, json_cb),
                "columns": [
                    {"data": "username"},
                    {"data": "user_group_name"},
                    {"data": "total_price"},
                    {"data": "total_rebate"},
                    {"data": "total_real_price"},
                    {"data": "total_bonus"},
                    {"data": "total_profit"},
                    {"data": null}
                ],
                columnDefs: [
                    {
                        'targets':0,
                        "render": function (data, type, row) {
                            if (typeof row.self !== 'undefined' && row.self === 1) {
                                return row.username;
                            } else {
                                return '<span onclick="set(' + row.user_id + ')" style="color: #3c8dbc; cursor: pointer">'+ row.username +'</span>';
                            }
                        }
                    },
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            var label = 'label-success';

                            if (row.user_group_id == 2 ) {
                                label = 'label-warning';
                            } else if (row.user_group_id == 3 ) {
                                label = 'label-danger';
                            }

                            return app.getLabelHtml(data, label);
                        }
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data);
                        }
                    },
                    {
                        'targets': 3,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data);
                        }
                    },
                    {
                        'targets': 4,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data);
                        }
                    },
                    {
                        'targets': 5,
                        'render': function (data, type, row) {
                            return new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data);
                        }
                    },
                    {
                        'targets': 6,
                        'render': function (data, type, row) {
                            var label = 'text-success';

                            if (row.total_profit < 0 ) {
                                label = 'text-danger';
                            }

                            return app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(data), label, true);
                        }
                    },
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            if (typeof row.self === 'undefined') {
                                return app.getalinkHtml(
                                    '游戏明细',
                                    {
                                        target:'_blank',
                                        class:'X-Small btn-xs text-primary',
                                        href:'/lotteryprofit/detail/?id=' + row.user_id +'&start_date='+$("input[name='start_date']").val()+'&end_date='+$("input[name='end_date']").val()
                                    }
                                );
                            }
                        }
                    }
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[\$,]/g, ''))
                            : typeof i === 'number' ? i : 0;
                    };

                    var sum_total_price = 0;
                    var sum_total_bonus = 0;
                    var sum_total_real_price = 0;
                    var sum_total_rebate = 0;
                    var sum_total_profit = 0;

                    for (item in data) {
                        sum_total_price += intVal(data[item].total_price);
                        sum_total_bonus += intVal(data[item].total_bonus);
                        sum_total_real_price += intVal(data[item].total_real_price);
                        sum_total_rebate += intVal(data[item].total_rebate);
                        sum_total_profit += intVal(data[item].total_profit);
                    }

                    $(tfoot).find('th').eq(1).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(sum_total_price)
                    );
                    $(tfoot).find('th').eq(2).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(sum_total_rebate)
                    );
                    $(tfoot).find('th').eq(3).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(sum_total_real_price)
                    );
                    $(tfoot).find('th').eq(4).html(
                        new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(sum_total_bonus)
                    );
                    $(tfoot).find('th').eq(5).html(
                        app.getColorHtml(new Intl.NumberFormat(['en-US'], {minimumFractionDigits:4}).format(sum_total_profit), sum_total_profit > 0 ? 'text-success' : 'text-danger')
                    );
                }
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                var data = table.row(0).data();
                if (typeof data !== 'undefined' && typeof data.self !== 'undefined') {
                    $(table.row(0).node()).css('background-color', '#ffece6');
                }
                loadFadeOut();
            });

            $('#search').submit(function(event){
            	event.preventDefault();
            	$("input[name='id']").val(0);
            	$("input[name='is_search']").val(1);
            	table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function(){
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            // 彩种选择onchange，联动玩法显示与否效果,隐藏重置为初始值
            $(".lottery_id").change(function(){
                if ($(this).val() >= 1) {
                    lottery_change($(this).find("option:selected").attr("ident"));
                } else {
                    $('.method_div .col-sm-8').html("<select name='method_id' class='form-control'><option value=''>选择玩法</option></select>");
                }
            });

            function lottery_change(ident){
                var method_list = {!! $lottery_method_list !!};
                var html = "<select name='method_id' class='form-control'><option value=''>选择玩法</option>";
                for (var i = 0; i < method_list[ident].length; i++)
                {
                    html += "<option value='"+method_list[ident][i].id+"'>"+method_list[ident][i].name+"</option>";
                }
                html += "</select>";
                $('.method_div .col-sm-8').html(html);
            }

            function showParentTree(data) {
    	            var str = '';
    	            if(data) {
    	                for(var i=0; i<data.length; i++) {
    	                    str += '<li><span onclick="set(' + data[i].user_id + ')" class="X-Small btn-xs text-primary" style="cursor: pointer;">'+ data[i].username +'</span></li>';
    	                }
    	            }
    	            $breadcrumb = $(".breadcrumb");
    	            $first = $breadcrumb.children().first();
    	            $breadcrumb.children().remove();
    	            $breadcrumb.append($first);
    	            $breadcrumb.append(str);
    	    }
        });
    </script>
@stop
