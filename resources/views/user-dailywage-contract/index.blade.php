@extends('layouts.base')

@section('title','用户工资契约')
@section('function','用户工资契约')
@section('function_link', '/userdailywagecontract/')
@section('here','用户工资列表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">用户名称</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name='username_input' placeholder="用户名" value="{{$username}}"/>
                                    <input type="hidden" name="is_search" value="0"/>
                                </div>
                                <label class="control-label checkbox-inline col-sm-3" style="padding-top: 7px;">
                                    <input type="checkbox" name="include_all" value="1"/>
                                    包含所有下级
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-3 control-label">用户组别</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="">全部</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="wage_type" class="col-sm-3 control-label">工资类别</label>
                                <div class="col-sm-9">
                                    <select name="wage_type" class="form-control">
                                        <option value="">全部</option>
                                        @foreach($wage_type_options as $wage_type_option)
                                            <option value="{{$wage_type_option}}">{{__('wage.line_type_'.$wage_type_option)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="btn-group col-md-6">
                            <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i
                                        class="fa fa-search" aria-hidden="true"></i>查询
                            </button>
                        </div>
                        <div class=" btn-group col-md-6">
                            <button type="reset" class="btn btn-default col-sm-2"><i class="fa fa-refresh"
                                                                                     aria-hidden="true"></i>重置
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                        <tr>
                            <th class="col-sm-2" data-sortable="false">用户名</th>
                            <th class="col-sm-1" data-sortable="false">级别</th>
                            <th class="col-sm-1" data-sortable="false">用户组</th>
                            <th class="col-sm-1" data-sortable="true">工资类型</th>
                            <th class="col-sm-1" data-sortable="true">最高比例</th>
                            <th class="col-sm-2" data-sortable="false">更新时间</th>
                            <th class="col-sm-3" data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_del_confirm" tabIndex="-1">
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
                        确认要删除 <span class="row_verify_text">该会员</span> 及其下级的日工资契约吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" value="" id="delete_user_id">
                    <input type="hidden" value="" id="delete_wage_type">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-success" id="delete_confirm">
                        <i class="fa fa-check-circle-o"></i> 确认
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
                    'username_input': $("input[name='username_input']").val(),
                    'include_all': $("input[name='include_all']:checked").val(),
                    'user_group_id': $("select[name='user_group_id']").val(),
                    'wage_type': $("select[name='wage_type']").val(),
                    'is_search': $("input[name='is_search']").val(),
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

            function showParentTree(data) {
                var str = '';
                if(data) {
                    for(var i=0; i<data.length; i++) {
                        str += '<li><a href="/userdailywagecontract/?username=' + data[i].username + '" class="X-Small btn-xs text-primary">'+ data[i].username +'</a></li>';
                    }
                }
                $breadcrumb = $(".breadcrumb");
                $first = $breadcrumb.children().first();
                $breadcrumb.children().remove();
                $breadcrumb.append($first);
                $breadcrumb.append(str);
            }
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[3, "desc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params, json_cb),
                "columns": [
                    {"data": "username"},
                    {"data": "user_level"},
                    {"data": "user_group_name"},
                    {"data": "wage_type_label"},
                    {"data": "top_rate"},
                    {"data": "created_at"},
                    {"data": null}
                ],
                columnDefs: [
                    {
                        'targets': 0,
                        'render': function (data, type, row) {
                            if (typeof row.self !== 'undefined' && row.self === 1) {
                                if (row['user_observe']) {
                                    return app.getColorHtml(row.username, 'label-danger', false);
                                } else {
                                    return row.username;
                                }
                            } else {
                                if (row['user_observe']) {
                                    return '<a href="/userdailywagecontract/?username=' + row.username + '" class="label-danger">' + row.username + '</a>';
                                } else {
                                    return '<a href="/userdailywagecontract/?username=' + row.username + '" >' + row.username + '</a>';
                                }
                            }
                        }
                    },
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(data, 'label-primary');
                        }
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            var label = 'label-success';

                            if (row.user_group_id == 2) {
                                label = 'label-warning';
                            } else if (row.user_group_id == 3) {
                                label = 'label-danger';
                            }

                            return app.getLabelHtml(data, label);
                        }
                    },
                    {
                        'targets': -1,
                        'render': function (data, type, row) {
                            var str = '';
                            if(row.user_type_id == 1){
                                str += '<a href="/userdailywagecontract/line/?user_id=' + row.user_id + '" class="btn-xs text-primary">线路配置</a>';
                            }
                            str += '<a href="/userdailywagecontract/edit/?user_id=' + row.user_id + '&wage_type='+row.wage_type+'" class="btn-xs text-primary">设置契约</a>';
                            str += '<a href="/userdailywagecontract/record/?username='+row.username+'&user_id=' + row.user_id + '&wage_type='+row.wage_type+'" class="btn-xs text-primary">契约调整记录</a>';
                            if(row.wage_type != undefined){
                                str += '<a href="javascript:;" user_id="' + row.user_id + '" wage_type="'+row.wage_type+'" username="' + row.username + '" class="btn-xs text-danger del">清除团队日工资设定</a>';
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
                    var data = table.row(i).data() || {};
                    if (typeof data.self !== 'undefined' && data.self === 1) {
                        $(table.row(i).node()).css('background-color', '#ffece6');
                    }
                });
                loadFadeOut();
            });
            $('#search').submit(function (event) {
                event.preventDefault();
                $("input[name='is_search']").val(1);
                table.ajax.reload();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };
            $(document).on('click', ".del", function () {
                $('#delete_user_id').val($(this).attr('user_id'));
                $('#delete_wage_type').val($(this).attr('wage_type'));
                $(".row_verify_text").text($(this).attr('username'));
                $("#modal_del_confirm").modal();
            });
            $('#delete_confirm').click(function () {
                var user_id = $('#delete_user_id').val();
                var wage_type = $('#delete_wage_type').val();
                loadShow();
                $.ajax({
                    url: "/userdailywagecontract/delete",
                    dataType: "json",
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        'user_id': user_id,
                        'wage_type':wage_type
                    },
                }).done(function (json) {
                    loadFadeOut();
                    $('#modal_del_confirm').modal('hide');
                    $('#delete_user_id').val('');
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    var type = 'danger';
                    if (json.status == 0) {
                        type = 'success';
                        table.ajax.reload();
                    }
                    $.notify({
                        title: '<strong>提示!</strong>',
                        message: json.msg
                    }, {
                        type: type
                    });
                });
            });
        });
    </script>
@stop
