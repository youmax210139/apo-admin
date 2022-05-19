@extends('layouts.base')

@section('title','投注限制')

@section('function','投注限制')
@section('function_link', '/lotterymethodlimit/')

@section('here','投注限制')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    
    <div class="row">
        <div class="col-xs-12">
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
                            <label class="col-sm-3 control-label text-right">彩种</label>
                            <div class="col-sm-9">
                                <select name="lottery_id" class="form-control lottery_id">
                                    <option value='all'>受限彩种</option>
                                    <option value='0'>所有彩种</option>
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
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lottery_method_category_id" class="col-sm-3 control-label">玩法分类</label>
                            <div class="col-sm-9">
                                <select name="lottery_method_category_id" class="form-control">
                                    <option value="all">所有分类</option>
                                    @foreach($method_categories as $item)
                                        <optgroup label="{{ $item['name'] }}">
                                            @if(isset($item['child']))
                                            @foreach($item['child'] as $_item)
                                            <option value="{{ $_item->id }}">{{ $_item->name }}</option>
                                            @endforeach
                                            @endif
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lottery_method_ident" class="col-sm-3 col-sm-3 control-label">玩法标识</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{request()->get('ident','')}}" class="form-control" name='lottery_method_ident' placeholder="玩法英文标识" />
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
        </div>
    </div>
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">

            @if(Gate::check('lotterymethodlimit/create'))
                <a href="javascript:;" class="btn btn-primary btn-md add-limit-btn">
                    <i class="fa fa-plus-circle"></i> 添加限制
                </a>
            @endif
        </div>
    </div>
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fa fa-info"></i>提示!如果受限玩法同时设定了所有彩种和指定彩种，以指定彩种配置为准,0为不限制
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">

                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">
                                <label><input type ='checkbox' name='check_all' class='check_all' value=''>全选</label>
                            </th>
                            <th class="hidden-sm" data-sortable="false">玩法标识</th>
                            <th class="hidden-sm" data-sortable="false">玩法名称</th>
                            <th class="hidden-sm" data-sortable="false">玩法类别</th>
                            <th class="hidden-sm" data-sortable="false">受限彩种</th>
                            <th class="hidden-sm" data-sortable="false">单注最低投注金额</th>
                            <th class="hidden-sm" data-sortable="false">单注最高投注金额</th>
                            <th class="hidden-sm" data-sortable="false">用户单期最高投注金额</th>
                            <th class="hidden-sm" data-sortable="false">全局单期最高投注金额</th>
                            <th class="hidden-sm" data-sortable="false">最大投注注数</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="row">
                        @if(Gate::check('lotterymethodlimit/edit'))
                            <input type="button" class="btn btn-primary" name="upate_by_select" id="upate_by_select"
                                   value="批量修改">
                        @endif
                        @if(Gate::check('lotterymethodlimit/delete'))
                            <input type="button" class="btn btn-danger" name="del_by_select" id="del_by_select"
                                   value="批量删除">
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-delete" tabIndex="-1">
        <div class="modal-dialog modal-danger">
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
                        确认要删除这个玩法的投注限制吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-outline">
                            确认
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade modal-primary" id="modal-add-limit" tabIndex="-1">
        <div class="modal-dialog modal-default modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">添加限制</h4>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <form class="form-horizontal" role="form" id="add-limit-form" method="POST" action="/lotterymethodlimit/create">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">玩法类型</label>
                                <div class="col-md-5 control">
                                    <select id="lottery_method_category_id" class="form-control">
                                        <option value="">请选择分类</option>
                                        @foreach($method_categories as $item)
                                            <optgroup label="{{ $item['name'] }}">
                                                @if(isset($item['child']))
                                                    @foreach($item['child'] as $_item)
                                                        <option value="{{ $_item->id }}">{{ $_item->name }}</option>
                                                    @endforeach
                                                @endif
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">玩法组</label>
                                <div class="col-md-5 control">
                                    <select id="method_group" class="form-control">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">限制玩法(可多选)</label>
                                <div class="col-md-5 control">
                                    <select name="method_ids[]" id="method_ids" multiple class="form-control">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">受限彩种</label>
                                <div class="col-md-5">
                                    <select name="lottery_id" id="limit-lottery" class="form-control">
                                        <option value='0'>所有彩种</option>
                                        @foreach ($lottery_list as $k=>$lotteries)
                                            <optgroup label="{{$k}}">
                                                @foreach($lotteries as $lottery)
                                                    <option value='{{ $lottery->id }}'
                                                            ident='{{ $lottery->ident }}'>{{ $lottery->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">单注最低投注</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="project_min" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">单注最低投注金额</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="project_max" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">用户单项最高投注金额</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="issue_max" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">全局单期最高投注金额</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="issue_total_max" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">最高投注注数</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="max_bet_num" value="0">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-outline" id="add-limit-submit">添加限制</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-success" id="modal-edit" tabIndex="-1">
        <div class="modal-dialog modal-default modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">修改限制</h4>
                </div>
                <div class="modal-body ">
                    <form class="form-horizontal" role="form" id="limit-edit-form" method="POST" action="/lotterymethodlimit/edit">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" value="">

                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">玩法类型</label>
                            <div class="col-md-5 control" id="method_group_name">

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">玩法名称</label>
                            <div class="col-md-5 control" id="method_name">
                                五星 - 五星直选 - 直选复式
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">受限彩种</label>
                            <div class="col-md-5" id="limit-lottery">
                                所有
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">单注最低投注金额</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="project_min" value="1.0000">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">单注最高投注金额</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="project_max" value="10">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">用户单期最高投注金额</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="issue_max" value="100">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">全局单期最高投注金额</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="issue_total_max" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">最高投注注数</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="max_bet_num" value="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-outline" id="edit-limit-submit">立即修改</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-success" id="modal-multi-edit" tabIndex="-1">
        <div class="modal-dialog modal-default modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">批量修改限制</h4>
                </div>
                <div class="modal-body ">
                    <div class="alert alert-success alert-dismissible">
                        提示！批量修改不会修改受限彩种！
                    </div>
                    <form class="form-horizontal" role="form" id="limit-mulit-form" method="POST" action="/lotterymethodlimit/edit">
                        {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="flag" value="multi">
                        <input type="hidden" name="ids" value="">
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">单注最低投注</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="project_min" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">单注最高投注</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="project_max" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">单项最高投注</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="issue_max" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">全局单项最高投注</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="issue_total_max" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">最高投注注数</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="max_bet_num" value="0">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-outline" id="edit-mulit-submit">立即修改</button>
                </div>
            </div>
        </div>
    </div>
            @stop

            @section('js')
                <script src="/assets/js/app/common.js" charset="UTF-8"></script>
                <script>
                    $(function () {
                        var get_params = function (data) {
                        var param = {
                                'lottery_method_category_id'    : $("select[name='lottery_method_category_id']").val(),
                                'lottery_id'         : $("select[name='lottery_id']").val(),
                                'lottery_method_ident'           : $("input[name='lottery_method_ident']").val()

                            };
                            return $.extend({}, data, param);
                        }

                        var table = $("#tags-table").DataTable({
                        	language:app.DataTable.language(),
                            order: [[0, "asc"]],
                            serverSide: true,
                            pageLength:100,
                            searching:false,
                            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                            // 要 ajax(url, type) 必须加这两参数
                            ajax: app.DataTable.ajax(null, null, get_params),
                            "columns": [
                                {"data": "id"},
                                {"data": "ident"},
                                {"data": "method_name"},
                                {"data": "lottery_method_category_name"},
                                {"data": "lottery_name"},
                                {"data": "project_min"},
                                {"data": "project_max"},
                                {"data": "issue_max"},
                                {"data": "issue_total_max"},
                                {"data": "max_bet_num"},
                                {"data": 'action'}
                            ],
                            columnDefs: [
                                {
                                    'targets': 0, "render": function (data, type, row) {
                                       return  "<input type ='checkbox' name='check[]' class='icheckbox_minimal' value='"+row['id']+"'> "+row['id'];
                                    }
                                },
                                {
                                    'targets': -1, "render": function (data, type, row) {
                                        var row_edit = {{Gate::check('lotterymethodlimit/edit') ? 1 : 0}};
                                        var row_delete = {{Gate::check('lotterymethodlimit/delete') ? 1 :0}};

                                        var str = '';

                                        //编辑
                                        if (row_edit) {
                                            str += '<a style="margin:3px;" data=\''+JSON.stringify(row)+'\' href="javascript:;" onclick="editLimit(this)" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                        }

                                        //删除
                                        if (row_delete) {
                                            str += '<a style="margin:3px;" href="javascript:;" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
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
                            loadFadeOut();
                        });

                        $('#search').submit(function(event){
                            $("input[name='is_search']").val(1);
                            event.preventDefault();
                            table.ajax.reload();
                        });

                        $("table").delegate('.delBtn', 'click', function () {
                            var id = $(this).attr('attr');
                            $('.deleteForm').attr('action', '/lotterymethodlimit/?id=' + id);
                            $("#modal-delete").modal();
                        });

                        //开启关闭玩法
                        $("table").delegate('.disable', 'click', function () {
                            var id = $(this).attr('rowid');
                            var status = $(this).attr('status');
                            $(".row_status_text").text($(this).attr('isdisabled'));
                            $('.disableForm').attr('action', '/lotterymethod/status?id=' + id+'&status='+status);
                            $("#modal-disable").modal();
                        });
                        $("#lottery_method_category_id").change(function () {
                            if($(this).val()==''){
                                return ;
                            }
                            $("#method_group").html('');
                            $("#method_ids").html('');
                            $("#limit-lottery").html('');
                            $.ajax({
                                url: "/lotterymethodlimit/create?flag=group&cate_id="+$(this).val(),
                                dataType: "json",
                                method: "get",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                            }).done(function (json) {
                                var html = '<option value="">请选择玩法组</option>';
                                $.each(json.group,function (i) {
                                    html+='<option value="'+json.group[i].id+'">'+json.group[i].name+'</option>'
                                })
                                $("#method_group").html(html);
                                html = '<option value="0">所有彩种</option>';
                                $.each(json.lottery,function (i) {
                                    html+='<option value="'+json.lottery[i].id+'">'+json.lottery[i].name+'</option>'
                                });
                                $("#limit-lottery").html(html);
                            });
                        });
                        $("#method_group").change(function () {
                            if($(this).val()==''){
                                return ;
                            }
                            $("#method_ids").html('');
                            $.ajax({
                                url: "/lotterymethodlimit/create?flag=method&cate_id="+$(this).val(),
                                dataType: "json",
                                method: "get",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                            }).done(function (json) {
                                var html = '';
                                $.each(json.data,function (i) {
                                    html+='<option value="'+json.data[i].id+'">'+json.data[i].name+'</option>'
                                })
                                $("#method_ids").html(html);
                            });
                        });
                        $(".add-limit-btn").click(function () {
                            $("#modal-add-limit").modal();
                        });
                        $("#add-limit-submit").click(function () {
                            if($("#method_ids").val() ==''){
                                BootstrapDialog.alert("请选择受限玩法");
                                return;
                            }
                            $("#modal-add-limit").modal('hide');
                            loadShow();
                            $.ajax({
                                url: "/lotterymethodlimit/create",
                                dataType: "json",
                                method: "post",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data:$("#add-limit-form").serialize()
                            }).done(function (json) {
                                loadFadeOut();
                                if(json.status==0){
                                    table.draw(false);
                                }
                                BootstrapDialog.alert(json.msg);

                            }).fail(function () {
                                loadFadeOut();
                            });
                        });
                        $("#edit-limit-submit").click(function () {
                            $("#modal-edit").modal('hide');
                            loadShow();
                            $.ajax({
                                url: "/lotterymethodlimit/edit",
                                dataType: "json",
                                method: "put",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data:$("#limit-edit-form").serialize()
                            }).done(function (json) {
                                loadFadeOut();
                                if(json.status==0){
                                    table.draw(false);
                                }
                                BootstrapDialog.alert(json.msg);
                            }).fail(function () {
                                loadFadeOut();
                            });
                        });
                        $(".check_all").click(function () {
                            if($(this).is(':checked')){
                                $("#tags-table input[type='checkbox']").prop('checked',true);
                            }else{
                                $("#tags-table input[type='checkbox']").prop('checked',false);
                            }
                        });
                        $("#upate_by_select").click(function () {
                            if($('input[name="check[]"]:checked').length<=0){
                                BootstrapDialog.alert('请选择需要修改的限制规则');
                                return false;
                            }
                            var id_array = [];
                            $('input[name="check[]"]:checked').each(function () {
                                id_array.push($(this).val());//向数组中添加元素
                            });
                            $("#modal-multi-edit input[name='ids']").val(id_array);
                            $("#modal-multi-edit").modal();
                        });
                        $("#edit-mulit-submit").click(function () {
                            $("#modal-multi-edit").modal('hide');
                            loadShow();
                            $.ajax({
                                url: "/lotterymethodlimit/edit",
                                dataType: "json",
                                method: "put",
                                data:$("#limit-mulit-form").serialize()
                            }).done(function (json) {
                                loadFadeOut();
                                if(json.status==0){
                                    $(".check_all").prop('checked',false);
                                    table.draw(false);
                                }
                                BootstrapDialog.alert(json.msg);
                            }).fail(function () {
                                loadFadeOut();
                            });
                        });
                        $("#del_by_select").click(function () {
                            if($('input[name="check[]"]:checked').length<=0){
                                BootstrapDialog.alert('请选择需要删除的限制规则');
                                return false;
                            }
                            var id_array = [];
                            $('input[name="check[]"]:checked').each(function () {
                                id_array.push($(this).val());//向数组中添加元素
                            });
                            BootstrapDialog.confirm({
                                message: '确定要批量删除所选规则吗？',
                                type: BootstrapDialog.TYPE_WARNING,
                                closable: true,
                                draggable: true,
                                btnCancelLabel: '取消',
                                btnOKLabel: '立即删除',
                                btnOKClass: 'btn-warning',
                                callback: function(result) {
                                    if (result) {
                                        $.ajax({
                                            url: "/lotterymethodlimit",
                                            dataType: "json",
                                            method: "POST",
                                            data:{flag:'multi',_method: 'DELETE',ids:id_array.toString()}
                                        }).done(function (json) {
                                            if(json.status==0){
                                                $(".check_all").prop('checked',false);
                                                table.draw(false);
                                            }
                                            BootstrapDialog.alert(json.msg);
                                        }).fail(function () {
                                        });
                                    }
                                }});
                        });
                    });
                    function editLimit(obj) {
                        eval("var json = "+($(obj).attr('data')));
                      $("#limit-edit-form input[name='id']").val(json.id);
                      $("#limit-edit-form #method_group_name").html(json.lottery_method_category_name);
                      $("#limit-edit-form #method_name").html(json.method_name);
                      $("#limit-edit-form #limit-lottery").html(json.lottery_name);
                        $("#limit-edit-form input[name='project_min']").val(json.project_min);
                        $("#limit-edit-form input[name='project_max']").val(json.project_max);
                        $("#limit-edit-form input[name='issue_max']").val(json.issue_max);
                        $("#limit-edit-form input[name='issue_total_max']").val(json.issue_total_max);
                        $("#limit-edit-form input[name='max_bet_num']").val(json.max_bet_num);
                        $("#modal-edit").modal();
                    }
                </script>
@stop
