@extends('layouts.base')

@section('title','红包管理')

@section('function','红包管理')
@section('function_link', '/coupon/')

@section('here','红包列表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row page-title-row" id="dangqian" style="margin:5px;">
                <div class="col-md-6">

                </div>

                <div class="col-md-6 text-right">
                    <a href="javascript:;" class="btn btn-success btn-md send_coupon"><i class="fa fa-gift"></i> 发随机红包 </a>
                    <a href="javascript:;" class="btn btn-primary btn-md set_coupon"><i class="fa fa-cog"></i> 定时红包设置 </a>
                </div>
            </div>
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form id="search" class="form-horizontal" action="/chatdeposit/" method="post">
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
                                <label class="col-sm-3 control-label">创建时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" value="" id='start_date' placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" value="" id='end_date' placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-center">
                        <button type="submit" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
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
                                <th class="hidden-sm" data-sortable="false"></th>
                                <th class="hidden-sm" data-sortable="false">ID</th>
                                <th class="hidden-sm" data-sortable="false">标题</th>
                                <th class="hidden-sm" data-sortable="false">发包人</th>
                                <th class="hidden-sm" >金额</th>
                                <th class="hidden-sm" >总个数</th>
                                <th class="hidden-sm" >已领个数</th>
                                <th class="hidden-sm" >已领金额</th>
                                <th class="hidden-sm" >类型</th>
                                <th class="hidden-sm" >创建时间</th>
                                <th class="hidden-sm" >过期时间</th>
                                <th class="hidden-sm" style="min-width: 120px" data-sortable="false">操作</th>
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
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>
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
                };
                return $.extend({}, data, param);
            }


            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[1, "DESC"]],
                serverSide: true,
                pageLength: 50,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"visible": false},
                    {"data": "id"},
                    {"data": "title"},
                    {"data": "sender_name"},
                    {"data": "amount"},
                    {"data": "total_num"},
                    {"data": "send_num"},
                    {"data": "send_amount"},
                    {"data": "type"},
                    {"data": "created_at"},
                    {"data": "expired_at"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var row_detail = {{ Gate::check('coupon/detail') ? 1 : 0 }};
                            var row_push = {{ Gate::check('coupon/push') ? 1 : 0 }};
                            var str = '';

                            //编辑
                            if (row_detail) {
                                str += '<a style="margin:3px;" data-id="'+row['id']+'" href="javascript:;" class="X-Small btn-xs text-success detail"><i class="fa fa-list"></i> 详情</a>';
                            }
                            //重新推送
                            if (row_push) {
                                str += '<a style="margin:3px;" data-id="'+row['id']+'" href="javascript:;" class="X-Small btn-xs text-success push"><i class="fa fa-list"></i> 再次推送</a>';
                            }
                            return str;
                        }
                    },
                    {
                        'targets': -4,
                        "render": function (data, type, row) {
                            var label = 'label-success';

                            if (row.type == 0) {
                                label = row.frozen==1?'label-danger':'label-warning';
                                return app.getLabelHtml('手动', label);
                            }else{

                                    label = 'label-success';
                                    return app.getLabelHtml('定时', label);

                            }
                        }
                    },
                ],
            });

            table.on('preXhr.dt', function () {
                //loadShow();
            });

            table.on('draw.dt', function () {
               // loadFadeOut();
            });


            $('#search_btn').click(function () {
                event.preventDefault();
                table.ajax.reload();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                //loadFadeOut();
            };
            $(document).on( 'click',".detail", function () {
                var id = $(this).attr('data-id');
                BootstrapDialog.show({
                    title:'红包详情',
                    message: $('<div></div>').load("/coupon/detail?id=" + id)
                });
            });
            $(document).on( 'click',".push", function () {
                var id = $(this).attr('data-id');
                BootstrapDialog.confirm('你确认再次推送红包提示吗？(不会生成新的红包)', function(result){
                    if(result) {
                        $.ajax({
                            url: '/coupon/push?id='+id,
                            dataType: "json",
                            method: "POST",
                            data:{id:id},
                        }).done(function (json) {
                            var type = 'danger';
                            if (json.status == 0) {
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                }, {
                                    type: 'success'
                                });
                            } else {
                                BootstrapDialog.alert(json.msg);
                            }
                        });
                    }
                });
            });
            $(document).on( 'click',".send_coupon", function () {

                BootstrapDialog.show({
                    title:'发拼手气红包',
                    message: $('<div></div>').load("/coupon/send"),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '立即发送',
                        cssClass: 'btn-primary',
                        action: function(dialogRef){
                            if ($("#sendcoupon-form input[name='amount']").val()==''){
                                BootstrapDialog.alert("请输入红包总金额");
                                return;
                            }
                            if ($("#sendcoupon-form input[name='num']").val()==''){
                                BootstrapDialog.alert("请输入红包个数");
                                return;
                            }
                            if ($("#sendcoupon-form input[name='google_code']").val()==''){
                                BootstrapDialog.alert("请输入谷歌动态验证码");
                                return;
                            }

                            $.ajax({
                                url: '/coupon/send',
                                dataType: "json",
                                method: "POST",
                                data:$("#sendcoupon-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    table.draw(false);
                                    $.notify({
                                        title: '<strong>提示!</strong>',
                                        message: json.msg
                                    },{
                                        type: 'success'
                                    });
                                    dialogRef.enableButtons(false);
                                    dialogRef.setClosable(false);
                                }else{
                                    BootstrapDialog.alert(json.msg);
                                }

                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            $(document).on( 'click',".set_coupon", function () {

                BootstrapDialog.show({
                    title:'定时红包设定',
                    message: $('<div></div>').load("/coupon/config"),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存配置',
                        cssClass: 'btn-primary',
                        action: function(dialogRef){

                            $.ajax({
                                url: '/coupon/config',
                                dataType: "json",
                                method: "POST",
                                data:$("#coupon_config_from").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    table.draw(false);
                                    $.notify({
                                        title: '<strong>提示!</strong>',
                                        message: json.msg
                                    },{
                                        type: 'success'
                                    });
                                    dialogRef.enableButtons(false);
                                    dialogRef.setClosable(false);
                                }else{
                                    BootstrapDialog.alert(json.msg);
                                }

                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
        });
    </script>
@stop