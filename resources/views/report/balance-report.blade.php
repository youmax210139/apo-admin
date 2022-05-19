@extends('layouts.base')

@section('title','团队余额报表')
@section('function','团队余额报表')
@section('function_link', '/balancereport/')
@section('here','团队余额报表')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form class="form-horizontal" action="/balancereport/" method="post">
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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date" class="col-sm-3 control-label">日期</label>
                                    <div class="col-sm-9">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control form_datetime" name="date"
                                                   id='date' value="{{$date}}" placeholder="开始时间">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="username" class="col-sm-3 control-label">用户</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name='username' placeholder="用户名" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label text-right">显示冻结用户</label>
                                    <div class="col-sm-9">
                                        <select name="frozen" class="form-control">
                                            <option value="-1">全部</option>
                                            <option value="1">是</option>
                                            <option value="2" selected="selected">否</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">排序</label>
                                <div class="col-sm-9">
                                    <select name="order_by" class="form-control">
                                        <option value="team_balance">团队余额</option>
                                        <option value="third_team_balance">第三方团队余额</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">排序方式</label>
                                <div class="col-sm-9">
                                    <select name="order_type" class="form-control">
                                        <option value="desc">从大到小</option>
                                        <option value="asc">从小到大</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">显示零结算用户</label>
                                <div class="col-sm-9">
                                    <select name="show_zero" class="form-control">
                                        <option value="1">是</option>
                                        <option value="0" selected="selected">否</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                            <input type="hidden" name="parent_id" value=""/>
                            <button type="submit" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                            <button type="reset" class="btn btn-default margin"></i>重置</button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->

            <div class="box box-primary">
                <style>
                    .total{
                        font-weight: bold;
                    }
                    .total > td:first-child{
                        text-align: right;
                    }
                </style>
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')

                    <div id="user_tree_wrap" style="position: relative;height: 30px;display:none;"><div id="user_tree" style="position: absolute;top: 5px;"></div></div>
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <tfoot>
                        <tr>
                            <th style="text-align: right;">合计</th>
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
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#date',
            event: 'focus',
            format: 'YYYY-MM-DD',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);

        $(function () {
            var get_params = function (data) {
                var param = {
                    'show_zero': $("select[name='show_zero']").val(),
                    'frozen': $("select[name='frozen']").val(),
                    'username': $("input[name='username']").val(),
                    'parent_id': $("input[name='parent_id']").val(),
                    'date': $("input[name='date']").val(),
                    'order_by': $("select[name='order_by']").val(),
                    'order_type': $("select[name='order_type']").val()
                };
                return $.extend({}, data, param);
            };

            var columnDefs = [
                {
                    'targets': -1,
                    'orderable': false,
                    'render': function (data, type, row) {
                        return data == $("input[name='parent_id']").val() ? '' : '<a href="javascript:void(0);" parent_id="'+data+'" >查看下级</a>';
                    }
                },{
                    'targets': -3,
                    'orderable': false,
                    'render': function (data, type, row) {
                        return parseFloat(row.team_balance)+parseFloat(row.third_team_balance);
                    }
                },
                {
                    'targets': 0,
                    'render': function (data, type, row) {
                        if (row['user_observe']) {
                            return app.getColorHtml(row.username, 'label-danger', false);
                        } else {
                            return row.username;
                        }
                    }
                }
            ];
            function response(data) {
                if(data.parent_tree){
                    var html = '用户层级：';
                    for(var i=0; i < data.parent_tree.length; i++){
                        if(i > 0){
                            html+='&gt;';
                        }
                        html += '&nbsp;&nbsp;<a href="javascript:void(0);" parent_id="'+data.parent_tree[i].id+'">'+data.parent_tree[i].username+'</a>&nbsp;&nbsp;';
                    }
                    $('#user_tree').html(html);
                    $('#user_tree_wrap').show();
                }else{
                    $('#user_tree').html('');
                    $('#user_tree_wrap').hide();
                }
            }
            var setting = {
                language: app.DataTable.language(),
                dom: 'tip',
                ordering: false,
                pageLength: 100,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params, response),
                columns: [
                    {"data": "username", "title": "用户名"},
                    {"data": "self_balance", "title": "自身余额"},
                    {"data": "team_balance", "title": "团队余额"},
                    {"data": "third_self_balance", "title": "第三方自身余额"},
                    {"data": "third_team_balance", "title": "第三方团队余额"},
                    {"data": "total", "title": "总计"},
                    {"data": "belong_date", "title": "日期"},
                    {"data": "user_id", "title": "操作"}
                ],
                columnDefs: columnDefs,
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Total over all pages
                    var total = 0;
                    var tatal_all = 0;
                    var columns = [2,3,4,5];
                    for(var i = 1 ;i< 6;i++){
                        total = api.column( i ).data().reduce( function (a, b) {
                            return (parseFloat(a) + parseFloat(b)).toFixed(4);
                        }, 0 );
                        $( api.column( i ).footer() ).html(total);
                        if (i == 2 || i == 4) {
                            tatal_all += parseFloat(total);
                        }
                    }

                    $( api.column(5).footer() ).html(tatal_all.toFixed(4));
                }

            };
            var table = $("#tags-table").DataTable(setting);

            function research(param){
                if(typeof param.parent_id !== "undefined"){
                    $("input[name='parent_id']").val(param.parent_id);
                    $("input[name='username']").val('');
                }
                event.preventDefault();
                table.ajax.reload();
            }

            $('.box-body').on('click','a[parent_id]',function () {
                research({parent_id:$(this).attr('parent_id')});
            });

            $('#search_btn').click(function (event) {
                $("input[name='parent_id']").val(0);
                event.preventDefault();
                table.ajax.reload();
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                loadFadeOut();
            });

            $.fn.dataTable.ext.errMode = function(){
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

        });
    </script>
@stop
