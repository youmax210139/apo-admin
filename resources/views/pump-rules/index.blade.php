@extends('layouts.base')
@section('title','抽返水规则查询')
@section('function','抽返水规则查询')
@section('function_link', '/pumprule/')
@section('here','列表')
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
                                <label for="user_group_id" class="col-sm-3 control-label">查询过滤</label>
                                <div class="col-sm-9">
                                    <select name="search_type" class="form-control">
                                        <option value="0" @if($param['search_type'] == 0 ) selected @endif>全部显示</option>
                                        <option value="1" @if($param['search_type'] == 1 ) selected @endif>无规则不显示</option>
                                    </select>
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
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <ol class="breadcrumb" id="parent_tree" style="margin-bottom: 10px;display: none;font-weight: bold;padding: 5px">
                    </ol>
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th class="hidden-sm" data-sortable="false">用户ID</th>
                            <th class="hidden-sm" data-sortable="false">用户名</th>
                            <th class="hidden-sm" data-sortable="false">用户级别</th>
                            <th class="hidden-sm" data-sortable="false">用户组别</th>
                            <th class="hidden-sm" data-sortable="false">状态</th>
                            <th class="hidden-sm" data-sortable="false">开关</th>
                            <th class="hidden-sm" data-sortable="false">最高比例</th>
                            <th class="hidden-sm" data-sortable="false">生成时间</th>
                            <th class="hidden-sm" data-sortable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-detail" tabIndex="-1" role="dialog">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content"></div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>

        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'include_all': $("input[name='include_all']:checked").val(),
                    'user_group_id': $('select[name="user_group_id"]').val(),
                    'search_type': $('select[name="search_type"]').val(),
                };
                return $.extend({}, data, param);
            }
            var json_cb = function(json) {
                if(typeof(json.parent_tree) !== undefined) {
                    showParentTree(json.parent_tree);
                } else {
                    showParentTree(null);
                }
            }

            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[1, "asc"]],
                serverSide: true,
                pageLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax('/pumprule/index', null, get_params,json_cb),
                "columns": [
                    {"data": "user_id"},
                    {"data": "username"},
                    {"data": "user_type_name"},
                    {"data": "user_group_name"},
                    {"data": "rule_status"},
                    {"data": "rule_enable"},
                    {"data": "rule_scale_max"},
                    {"data": "rule_created_at"},
                    {"data": null},
                ],
                columnDefs: [
                    {
                        'targets': 1,
                        'render': function (data, type, row) {
                            return  '<a  href="/pumprule/index?username='+ row.username
                                +'&user_group_id='+$('select[name="user_group_id"]').val()
                                +'" class="" title="查看下级">'+ row.username +'</a>';

                            return row.username ;
                        }
                    },
                    {
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
                    },
                    {
                        'targets': 4,
                        'render': function (data, type, row) {
                            var label = 'label-success';
                            var text = '生效';
                            if (row.rule_status !== 0 ) {
                                label = 'label-warning';
                                text = '失效';
                            }
                            return app.getLabelHtml(text, label);
                        }
                    },
                    {
                        'targets': 5,
                        'render': function (data, type, row) {
                            var label = 'label-warning';
                            var text = '关闭';
                            if (row.rule_enable == 1 ) {
                                label = 'label-success';
                                text = '开启';
                            }
                            return app.getLabelHtml(text, label);
                        }
                    },
                    {
                        'targets': 6,
                        'render': function (data, type, row) {
                            if(typeof(data) == 'number'){
                                data +=  ' %'
                            }
                            return data;
                        }
                    },
                    {
                        'targets': -1,
                        'render': function (data, type, row) {
                            var str = '';
                            if(row.rule_id == 0){
                                @if(Gate::check('pumprule/create'))
                                    //str += '<a  mounttabs=""  title="添加规则[' + row.username + ']" href="/pumprule/create/?user_id=' + row.user_id + '"  href="JavaScript:;" data="'+row.user_id+'" username="'+row.username+'" class="X-Small btn-xs text-primary ">添加</a>';
                                @endif
                            }else{
                                @if(Gate::check('pumprule/edit'))
                                    //str += '<a  title="编辑规则" href="/pumprule/edit/?user_id=' + row.user_id + '"  href="JavaScript:;" data="'+row.user_id+'" username="'+row.username+'" class="X-Small btn-xs text-primary ">编辑</a>';
                                @endif
                                @if(Gate::check('pumprule/detail'))
                                    //str += '<a  title="删除规则" href="JavaScript:;" data="'+row.user_id+'" username="'+row.username+'" class="X-Small btn-xs text-primary delete">删除</a>';
                                @endif
                            }
                            @if(Gate::check('pumprule/history'))
                            if(row.rule_id > 0 || row.user_id == row.top_id ){
                                //str += '<a href="/pumprule/history?user_id=' + row['user_id'] + '" class="X-Small btn-xs text-success " data-target="#modal-detail" data-toggle="modal"><i class="fa fa-file-text-o"></i> 历史</a>';
                                str += '<a mounttabs="" title="规则调整记录【' + row.username + '】" href="/pumprule/history?user_id=' + row['user_id'] + '" class="X-Small btn-xs text-success ">调整记录</a>';
                            }
                            @endif
                            return str;
                        }
                    },
                ],
                "footerCallback": function ( tfoot, data, start, end, display ){
                }
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            table.on('xhr.dt', function ( e, settings, json, xhr ) {
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
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

            $(document).on( 'click',".delete", function () {
                var user_id = $(this).attr('data');
                var username = $(this).attr('username');
                BootstrapDialog.confirm({
                    message: '确定要删除'+username+'的抽返水规则吗？',
                    type: BootstrapDialog.TYPE_WARNING,
                    closable: true,
                    draggable: true,
                    btnCancelLabel: '取消',
                    btnOKLabel: '删除',
                    btnOKClass: 'btn-warning',
                    callback: function(result) {
                        if(result) {
                            $.ajax({
                                url: "/pumprule",
                                dataType: "json",
                                method: "DELETE",
                                data:{user_id:user_id}
                            }).done(function (json) {
                                if(json.status==0){
                                    table.draw(false);
                                    notify(json.msg,'success');
                                }else{
                                    notify(json.msg,'error');
                                }

                            }).error(function (jqXHR, textStatus, errorThrown) {
                                alert(errorThrown.toString())
                            });
                        }
                    }
                });
            });
            function showParentTree(data) {
                var str = '';
                if(data) {
                    for(var i=0; i<data.length; i++) {
                        str += '<li> <a href="/pumprule/index?username=' + data[i].username + '" >'+ data[i].username +'</a></li>';
                    }
                }
                $breadcrumb = $("#parent_tree");
                $breadcrumb.children().remove();
                if(str==''){
                    $breadcrumb.hide();
                }else{
                    $breadcrumb.append(str);
                    $breadcrumb.show();
                }
            }

            $('#search_btn').click(function () {
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
