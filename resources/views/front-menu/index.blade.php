@extends('layouts.base')

@section('title','前台菜单')

@section('function','前台菜单')
@section('function_link', '/frontmenu/')

@section('here','菜单列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="row page-title-row">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('frontmenu/create'))
                <a href="javascript:void(0)" class="btn btn-primary btn-md" onclick="addMenu()"><i class="fa fa-plus"></i>添加菜单种类</a>
            @endif
            @if(Gate::check('frontmenu/refresh'))
                <a href="/frontmenu/refresh" class="btn btn-success btn-md"><i class="fa fa-refresh"></i>刷新缓存[{{ $last_refresh_at }}]</a>
            @endif
                @if(Gate::check('frontmenu/output'))
                    <a href="/frontmenu/output" class="btn btn-info btn-md"><i class="fa fa-download"></i>导出所有菜单</a>
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
                    <div>
                    <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/frontmenu/'" type="button" class="btn {{$category == ''?'bg-primary':''}} ">全部</button>
                    @foreach($category_array as $cate_key=>$cate_name)
                        <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/frontmenu/?category={{$cate_key}}'" type="button" class="btn {{$cate_key == $category?'bg-primary':''}} ">{{$cate_name}}</button>
                    @endforeach
                    </div>
                    <div>
                        说明：<br>
                        1、修改彩票名称，同时需要到 彩种管理→彩种列表→修改对应的彩种名称，否则投注记录的彩种名字不会改名。<br>
                        2、若LOGO路径为空，即调用系统默认的LOGO。LOGO图片上传：网站管理→图片上传，然后复制路径过来。<br>
                        3、修改完菜单后，记得点击右上角<strong>【刷新缓存】</strong>按钮，约2分钟后，前台菜单显示才能生效。<br>
                        <br>
                    </div>
                    <table id="tags-table" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">中文名称</th>
                            <th class="hidden-sm">英文标识</th>
                            <th class="hidden-sm">类别</th>
                            <th class="hidden-sm">状态</th>
                            <th class="hidden-sm">更新时间</th>
                            <th data-sortable="false">操作</th>
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
    <script>
        $(function () {
            var table = $("#tags-table").DataTable({
                language:app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                iDisplayLength :25,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(),
                "columns": [
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "ident"},
                    {"data": "category"},
                    {"data": "status"},
                    {"data": "updated_at"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var row_edit = {{ Gate::check('frontmenu/edit') ? 1 : 0 }};
                            var row_delete = {{ Gate::check('frontmenu/menudelete') ? 1 : 0 }};
                            var row_editdata = {{ Gate::check('frontmenu/editdata') ? 1 : 0 }};
                            var row_output = {{ Gate::check('frontmenu/output') ? 1 : 0 }};
                            var row_status_class = row['status'] ? 'text-danger' : 'text-success';
                            var row_status_icon = row['status'] ? 'fa-ban' : 'fa-check-circle-o';
                            var row_status_text = row['status'] ? '禁用' : '启用' ;

                            var str = '';
                            var a_attr = null;
                            var common_class = 'X-Small btn-xs ';

                            //菜单内容
                            if(row_editdata) {
                                a_attr = {
                                    href:'/frontmenu/editdata?id=' + row['id'],
                                    class:common_class + 'text-primary',
                                };
                                str += app.getalinkHtml('菜单内容', a_attr, 'fa-adn');
                            }

                            //编辑
                            if (row_edit) {
                                a_attr = {
                                    'class' : common_class + 'text-success editBtn',
                                    'href': '#',
                                    'attr': row['id']
                                };
                                str += app.getalinkHtml('编辑',a_attr, 'fa-edit');
                            }

                            //状态
                            if (row_edit) {
                                a_attr = {
                                    'class' : common_class + 'statusBtn ' + row_status_class,
                                    'attr': row['id'],
                                    'href': '#',
                                    'isdisabled' : row_status_text
                                };
                                //如果这里的html简单，直接写原生的html。如果html代码和js混一起比较复杂点的，使用app.getalinkHtml()
                                str += app.getalinkHtml(row_status_text,a_attr, row_status_icon);
                            }

                            //删除
                            if (row_delete) {
                                a_attr = {
                                    'class' : common_class + 'text-danger delBtn',
                                    'attr': row['id'],
                                    'href': '#',
                                    'isdisabled' : row_status_text
                                };
                                str += app.getalinkHtml('删除', a_attr, 'fa-times-circle');
                            }
                            //导出
                            if (row_output) {
                                a_attr = {
                                    href:'/frontmenu/output?id=' + row['id'],
                                    class:common_class + 'text-warning',
                                };
                                str += app.getalinkHtml('导出', a_attr, 'fa-download');
                            }

                            return str;
                        }
                    },
                    {
                        'targets': -3,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(
                                row['status'] ? '启用':'禁用',
                                'label-'+(row['status'] ? 'success' : 'danger')
                            );
                        }
                    }
                ]
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                table.column(0).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
                loadFadeOut();
            });

            //删除
            $("table").delegate('.delBtn', 'click', function () {
                var id = $(this).attr('attr');
                BootstrapDialog.confirm('您确定要删除该菜单种类吗？删除后不能恢复', function(result){
                    if(result) {
                        $.ajax({
                            url: "/frontmenu/menudelete",
                            dataType: "json",
                            method: "post",
                            data:{"id":id},
                        }).done(function (json) {
                            if(json.status == 0) {
                                BootstrapDialog.alert({
                                    title: '删除成功',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {
                                        table.ajax.reload();
                                    }
                                });
                            } else {
                                BootstrapDialog.alert({
                                    title: '删除失败',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {

                                    }
                                });
                            }
                        });
                    }
                });
            });

            //状态
            $("table").delegate('.statusBtn', 'click', function () {
                var id = $(this).attr('attr');
                $.ajax({
                    url: "/frontmenu/edit",
                    dataType: "json",
                    method: "POST",
                    data:{"set_status":1, "id":id}
                }).done(function (json) {
                    if(json.status == 0) {
                        BootstrapDialog.alert({
                            title: '修改状态成功',
                            message: json.msg,
                            type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                            closable: true, // <-- Default value is false
                            draggable: true, // <-- Default value is false
                            buttonLabel: '关闭', // <-- Default value is 'OK',
                            callback: function(result) {
                                table.ajax.reload();
                            }
                        });
                    } else {
                        BootstrapDialog.alert({
                            title: '修改状态失败',
                            message: json.msg,
                            type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                            closable: true, // <-- Default value is false
                            draggable: true, // <-- Default value is false
                            buttonLabel: '关闭', // <-- Default value is 'OK',
                            callback: function(result) {

                            }
                        });
                    }
                });
            });
        });

        //编辑
        $("table").delegate( ".editBtn", 'click', function () {
            var id = $(this).attr('attr');
            BootstrapDialog.show({
                title:'编辑种类信息',
                message: $('<div></div>').load("/frontmenu/edit?id=" + id),
                buttons: [{
                    icon: 'glyphicon glyphicon-send',
                    label: '保存',
                    cssClass: 'btn-primary',
                    action: function(dialogRef){
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        $.ajax({
                            url: "/frontmenu/edit",
                            dataType: "json",
                            method: "POST",
                            data:$("#menu_info_form").serialize(),
                        }).done(function (json) {
                            if(json.status == 0) {
                                BootstrapDialog.alert({
                                    title: '保存成功',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {
                                        document.location.reload();
                                        //table.ajax.reload();
                                    }
                                });
                            } else {
                                BootstrapDialog.alert({
                                    title: '保存失败提醒',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {

                                    }
                                });
                                dialogRef.enableButtons(true);
                                dialogRef.setClosable(true);
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

        //添加
        function addMenu() {
            BootstrapDialog.show({
                title:'添加种类信息',
                message: $('<div></div>').load("/frontmenu/create"),
                buttons: [{
                    icon: 'glyphicon glyphicon-send',
                    label: '保存',
                    cssClass: 'btn-primary',
                    action: function(dialogRef){
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        $.ajax({
                            url: "/frontmenu/create",
                            dataType: "json",
                            method: "POST",
                            data:$("#menu_info_form").serialize(),
                        }).done(function (json) {
                            if(json.status == 0) {
                                BootstrapDialog.alert({
                                    title: '保存成功',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {
                                        document.location.reload();
                                    }
                                });
                            } else {
                                BootstrapDialog.alert({
                                    title: '保存失败提醒',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {

                                    }
                                });
                                dialogRef.enableButtons(true);
                                dialogRef.setClosable(true);
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
        }
    </script>
@stop
