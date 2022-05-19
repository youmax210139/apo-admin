@extends('layouts.base')

@section('title','玩法管理')

@section('function','玩法管理')
@section('function_link', '/lotterymethod/')

@section('here','玩法列表')

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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="level" class="col-sm-3 control-label">层级</label>
                            <div class="col-sm-9">
                                <select name="level" class="form-control">
                                    <option value="0">全部</option>
                                    <option value="1">玩法组1</option>
                                    <option value="2">玩法组2</option>
                                    <option value="3">玩法</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="lottery_method_status" class="col-sm-3 control-label">玩法状态</label>
                            <div class="col-sm-9">
                                <select name="lottery_method_status" class="form-control">
                                    <option value="all">所有状态</option>
                                    <option value="1">开启</option>
                                    <option value="0">关闭</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="lottery_method_name" class="col-sm-3 col-sm-3 control-label">玩法名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name='lottery_method_name' placeholder="玩法名称" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="btn-group col-md-6">
                        <input type="hidden" name="id" value="{{ $id }}" >
                        <input type="hidden" name="is_search" value="0" >
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
                <a href="/lotterymethod/outfile" class="btn btn-warning btn-md">
                    <i class="fa fa-arrow-down"></i> 导出所有玩法
                </a>
            @if(Gate::check('lotterymethod/create'))
                <a href="/lotterymethod/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加玩法
                </a>
            @endif
        </div>
    </div>

    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
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
                    <form class="updatesortForm" method="POST" action="/lotterymethod/edit">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="post">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th class="hidden-sm">ID</th>
                            <th class="hidden-sm" data-sortable="false">级数</th>
                            <th class="hidden-sm">英文标识</th>
                            <th class="hidden-sm" style="min-width: 180px">名称</th>
                            <th class="hidden-sm">菜单排序</th>
                            <th class="hidden-sm">奖金级别</th>
                            <th class="hidden-sm">玩法类别</th>
                            <th class="hidden-sm">状态</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <td colspan="9">
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-edit"></i> 更新排序
                            </button>
                        </td>
                        </tfoot>
                    </table>
                    </form>
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
                        确认要删除这个玩法及其所有下级玩法吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!--开启关闭玩法-->
    <div class="modal fade" id="modal-disable" tabIndex="-1">
        <div class="modal-dialog modal-primary">
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
                        确认要<span class="row_status_text"></span>该配置吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="disableForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i> 确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--修改奖级-->
    <div class="modal fade" id="modal-prize" tabIndex="-1">
        <div class="modal-dialog modal-primary">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">修改奖级</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="edit-prize-form" role="form" method="POST" action="/lotterymethod/editprize">
                        <div class="form-group">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="id" value="">
                            <div class="form-group method_name">
                                <label for="tag" class="col-md-3 control-label">玩法名称</label>
                                <div class="col-md-5 control" id="method_name">
                                </div>
                            </div>
                            <div class="prize_level">

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-outline edit-prize-btn">
                         确认
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--修改注数限制-->
    <div class="modal fade" id="modal-maxnum" tabIndex="-1">
        <div class="modal-dialog modal-primary">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">注数限制</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="edit-Maxnum-form" role="form" method="POST">

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="id" value="">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">玩法名称</label>
                                <div class="col-md-5 control method_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">最大投注数（0为不限制）</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="max_num"  value="" >
                                </div>
                            </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-outline edit-maxnum-btn">
                        确认
                    </button>
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
                                'lottery_method_status'         : $("select[name='lottery_method_status']").val(),
                                'lottery_method_name'           : $("input[name='lottery_method_name']").val(),
                                'id'                            : $("input[name='id']").val(),
                                'is_search'                     : $("input[name='is_search']").val(),
                                 'level'                     : $("select[name='level']").val(),
                            };
                            return $.extend({}, data, param);
                        }

                        var table = $("#tags-table").DataTable({
                        	language:app.DataTable.language(),
                            order: [[0, "asc"]],
                            serverSide: true,
                            pageLength:50,
                            searching:false,
                            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                            // 要 ajax(url, type) 必须加这两参数
                            ajax: app.DataTable.ajax(null, null, get_params),
                            "columns": [
                                {"data": "id"},
                                {"data": "tree_level"},
                                {"data": "ident"},
                                {"data": "name"},
                                {"data": "sort"},
                                {"data": "prize_level"},
                                {"data": "lottery_method_category_name"},
                                {"data": "status"},
                                {"data": 'action'}
                            ],
                            columnDefs: [
                                {
                                    'targets': -1, "render": function (data, type, row) {
                                        var row_edit = {{Gate::check('lotterymethod/edit') ? 1 : 0}};
                                        var row_delete = {{Gate::check('lotterymethod/delete') ? 1 :0}};
                                        var row_edit_prize_level = {{Gate::check('lotterymethod/editprize') ? 1 :0}};
                                        var row_limit = {{Gate::check('lotterymethodlimit/index') ? 1 :0}};
                                        var row_status = {{ Gate::check('lotterymethod/status') ? 1 : 0 }};
                                        var row_status_class = row['status'] ? 'text-primary' : 'text-muted';
                                        var row_status_icon = row['status'] ? 'fa-lock' : 'fa-unlock';
                                        var row_status_text = row['status'] ? '关闭' : '开启' ;



                                        var str = '';

                                        //编辑
                                        if (row_edit) {
                                            str += '<a style="margin:3px;" mounttabs title="修改玩法-'+row['name']+'" href="/lotterymethod/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                        }
                                        //修改奖级
                                        if (row_edit_prize_level && row['tree_level'] == 3) {
                                            str += '<a style="margin:3px;" title="修改奖级-'+row['name']+'" href=\'javascript:editPrize('+row['id']+',"'+row['name']+'",'+row['prize_level_name']+','+row['prize_level']+');\' class="X-Small btn-xs text-warning "><i class="fa fa-edit"></i> 修改奖级</a>';
                                        }
                                        //投注限制
                                        if (row_limit && row['tree_level'] == 3 ) {
                                            str += '<a mounttabs style="margin:3px;" title="投注限制" href=\'/lotterymethodlimit?ident='+row['ident']+'\' class="X-Small btn-xs text-info"><i class="fa fa-edit"></i> 投注限制</a>';
                                        }

                                        //删除
                                        if (row_delete) {
                                            str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
                                        }

                                        //开启关闭
                                        if (row_status) {
                                            a_attr = {
                                                'class' : 'X-Small btn-xs  disable ' + row_status_class,
                                                'rowid': row['id'],
                                                'status': row.status == true ? 0: 1,
                                                'href': '#',
                                                'isdisabled' : row_status_text
                                            };
                                            //如果这里的html简单，直接写原生的html。如果html代码和js混一起比较复杂点的，使用app.getalinkHtml()
                                            str += app.getalinkHtml(row_status_text,a_attr, row_status_icon);
                                        }

                                        if(row['tree_level'] < 3) {
                                            str += '<a style="margin:3px;" href="/lotterymethod/create?id=' + row['id'] + '" class="X-Small btn-xs text-primary "><i class="fa fa-plus"></i> 添加下级玩法</a>';
                                        }

                                        return str;
                                    }
                                },
                                {
                                    'targets': 3, "render": function (data, type, row) {
                                        var str = '';
                                        if(row['tree_level'] < 3) {
                                            str += '<a href="/lotterymethod/index?id=' + row['id'] + '">'+ row['name'] +'</a>';
                                        } else {
                                            str += row['name'];
                                        }
                                        return str;
                                    },
                                },
                                {
                                    'targets': -2,
                                    'render': function (data, type, row) {
                                        return app.getLabelHtml(
                                            row['status']==false ? '关闭':'开启',
                                            'label-'+(row['status'] ? 'success' : 'danger')
                                        );
                                    }
                                },
                                {
                                    'targets': -5,
                                    'render': function (data, type, row) {
                                        return '<input name="sort['+row['id']+']" value="'+row['sort']+'">';
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
                            $('.deleteForm').attr('action', '/lotterymethod/?id=' + id);
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


                    $(".edit-prize-btn").click(function () {
                        $("#modal-prize").modal('hide');
                        loadShow();
                        $.ajax({
                            url: "/lotterymethod/editprize",
                            dataType: "json",
                            method: "put",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: $("#edit-prize-form").serialize()
                        }).done(function (json) {
                            loadFadeOut();
                            if(json.status==0){
                                table.draw(false);
                            }
                            BootstrapDialog.alert($("#modal-maxnum .method_name").html()+json.msg);
                        }).fail(function () {
                            loadFadeOut();
                        });
                    });
                        $(".edit-maxnum-btn").click(function () {
                            $("#modal-maxnum").modal('hide');
                            loadShow();
                            $.ajax({
                                url: "/lotterymethod/editmaxnum",
                                dataType: "json",
                                method: "put",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: $("#edit-Maxnum-form").serialize()
                            }).done(function (json) {
                                loadFadeOut();
                                if(json.status==0){
                                    table.draw(false);
                                }
                                BootstrapDialog.alert($("#modal-maxnum .method_name").html()+json.msg);
                            }).fail(function () {
                                loadFadeOut();
                            });
                        });
                    });
                    function editPrize(id, name, prize_name, prize) {
                        $("#method_name").html(name);
                        $("#modal-prize input[name='id']").val(id);
                        var html = '';
                        $.each(prize,function (i) {
                            html+='<div class="form-group">\n' +
                                '                                <label for="tag" class="col-md-3 control-label">'+prize_name[i]+'</label>\n' +
                                '                                <div class="col-md-5">\n' +
                                '                                    <input type="text" class="form-control" name="prize_level['+i+']" value="'+prize[i]+'">\n' +
                                '                                </div>\n' +
                                '                            </div>';
                        })
                        $("#modal-prize .prize_level").html(html);
                        $("#modal-prize").modal();
                    }
                    function editMaxnum(id, name, num) {
                        $("#modal-maxnum .method_name").html(name);
                        $("#modal-maxnum input[name='id']").val(id);
                        $("#modal-maxnum input[name='max_num']").val(num);
                        $("#modal-maxnum").modal();
                    }
                </script>
@stop
