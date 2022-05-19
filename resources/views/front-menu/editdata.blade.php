@extends('layouts.base')

@section('title','前台菜单')

@section('function','前台菜单')
@section('function_link', '/frontmenu/')

@section('here', $menu_row->name)

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="row page-title-row">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            <a href="/frontmenu/" class="btn btn-primary btn-md"><i class="fa fa-backward"></i>返回菜单列表</a>
            @if(Gate::check('frontmenu/output'))
                <a href="/frontmenu/output?id={{ $menu_row->id }}" class="btn btn-info btn-md"><i class="fa fa-download"></i>导出【{{ $menu_row->name }}】菜单</a>
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
                    <div>【{{ $menu_row->name }}】<span class="text-danger">提示：添加、修改、删除等操作需要点【保存】按钮才能保存数据。</span>上次修改：{{ $last_edit_at }}</div>
                    <form id="form_category">
                        <table id="tags-table" class="table table-striped table-bordered table-hover" id="menu_data_tbl">
                            <thead>
                            <tr>
                                <th class="hidden-sm">分类名称</th>
                                <th class="hidden-sm">标识</th>
                                <th class="hidden-sm">排序</th>
                                <th class="hidden-sm">下级菜单</th>
                                <th class="hidden-sm">删除</th>
                            </tr>
                            </thead>
                            <tbody id="menu_data">
                            </tbody>
                        </table>
                    </form>
                    <div class="form-group">
                        <button type="button" class="btn btn-warning btn-md float_left" id="add_category_btn">
                            <i class="fa fa-plus-circle"></i>
                            添加【{{ $menu_row->name }}】新分类
                        </button>

                        <div class="col-md-3 col-md-offset-3">
                            <button type="button" class="btn btn-primary btn-md" id="save_btn">
                                <i class="fa"></i>
                                保存
                            </button>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-default btn-md" onclick="location.href='/frontmenu/'">
                                <i class="fa"></i>
                                返回上一页
                            </button>
                        </div>

                        <button type="button" class="btn btn-info btn-md float_right" id="import_data_btn">
                            <i class="fa fa-upload"></i>
                            导入【{{ $menu_row->name }}】菜单JSON数据
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .input_width {width: 150px;}
        .input_sort_width {width: 70px;}
        .select_width {width: 60px;}
        .float_left {float: left;}
        .float_right {float: right;}
        .td_width {width: 200px;}
        .modal-lg {min-width: 1200px;}
    </style>
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        var menu_id = {{ $menu_row->id }};
        var menu_name = '{{ $menu_row->name }}';
        var menu_ident = '{{ $menu_row->ident }}';
        var menu_data = {!! $menu_row->data !!};
        function init() {
            for (var index in menu_data) {
                addCategoryItem(menu_data[index]);
            }
        }

        function addCategoryItem(data) {
            var item_html = '';
            var name = typeof data.name != 'undefined' ? data.name : '';
            var path = typeof data.path != 'undefined' ? data.path : '';
            var sort = typeof data.sort != 'undefined' ? data.sort : '';
            var children = typeof data.children != 'undefined' ? encodeURI(JSON.stringify(data.children)) : '';
            item_html += '<tr>';
            item_html += '<td><input class="form-control input_width" type="text" name="name[]" value="'+ name +'" ></td>';
            item_html += '<td><input class="form-control input_width" type="text" name="path[]" value="'+ path +'" ></td>';
            item_html += '<td><input class="form-control input_sort_width" type="number" name="sort[]" value="'+ sort +'" ></td>';
            item_html += '<td><input type="hidden" name="children[]" value="'+ children +'"><a href="javascript:void(0)" onclick="childrenMenu(this)">下级菜单</a></td>';
            item_html += '<td><a href="javascript:void(0)" onclick="deleteRecord(this)">删除</a></td>';
            item_html += '</tr>';

            $("#menu_data").append(item_html);
        }

        function deleteRecord(ob) {
            $(ob).parent().parent().remove();
        }

        function childrenMenu(ob) {
            var children_input_ob = $(ob).prev('input');
            var menu_data = children_input_ob.val();
            var children_menu_id = 'children_' + (Math.floor(Math.random()*100000)+1);
            var table = '<form id="form_children" method="POST"><table class="table table-striped">' +
                '<thead><tr><th>名称</th><th>路径</th><th>排序</th><th>LOGO_PC路径</th><th>LOGO_H5路径</th><th>『新』标</th><th>『爆』标</th><th>『热』标</th><th>『官』标</th><th>删除</th></tr></thead>' +
                '<tbody id="'+ children_menu_id +'">';
            if(menu_data !== '') {
                menu_data = $.parseJSON(decodeURI(menu_data));
            } else {
                menu_data = {};
            }
            for (var index in menu_data) {
                table += addChildrenItem(menu_data[index]);
            }
            table += '</tbody></table></form>';

            BootstrapDialog.show({
                title: '【'+ menu_name +'】编辑子菜单',
                message: table,
                size: 'size-wide',
                buttons: [{
                    label: '保存',
                    cssClass: 'btn-primary',
                    action: function(dialogItself){
                        saveChildren(children_input_ob);
                        dialogItself.close();
                    }
                }, {
                    icon: 'glyphicon glyphicon-plus-sign',
                    label: '添加新记录',
                    cssClass: 'btn-warning float_left',
                    action: function () {
                        addChildrenItem({}, children_menu_id);
                    }
                }, {
                    label: 'Close',
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }]
            });
        }

        function addChildrenItem(data, children_menu_id) {
            var item_html = '';
            var name = typeof data.name != 'undefined' ? data.name : '';
            var path = typeof data.path != 'undefined' ? data.path : '';
            var sort = typeof data.sort != 'undefined' ? data.sort : '';
            var logo_pc = typeof data.logo_pc != 'undefined' ? data.logo_pc : '';
            var logo_h5 = typeof data.logo_h5 != 'undefined' ? data.logo_h5 : '';
            var isnew = typeof data.isnew != 'undefined' ? data.isnew : '';
            var ishot = typeof data.ishot != 'undefined' ? data.ishot : '';
            var ishot2 = typeof data.ishot2 != 'undefined' ? data.ishot2 : '';
            var isguan = typeof data.isguan != 'undefined' ? data.isguan : '';
            item_html += '<tr>';
            item_html += '<td><input class="form-control input_width" type="text" name="name[]" value="'+ name +'" ></td>';
            item_html += '<td><input class="form-control input_width" type="text" name="path[]" value="'+ path +'" ></td>';
            item_html += '<td><input class="form-control input_sort_width" type="number" name="sort[]" value="'+ sort +'" ></td>';
            item_html += '<td><input class="form-control input_width" type="text" name="logo_pc[]" value="'+ logo_pc +'" placeholder="相对、绝对路径都可" ></td>';
            item_html += '<td><input class="form-control input_width" type="text" name="logo_h5[]" value="'+ logo_h5 +'" placeholder="相对、绝对路径都可" ></td>';
            if(isnew == 0) {
                item_html += '<td><select class="form-control select_width" name="isnew[]"><option value="0" selected>否</option><option value="1">是</option></select></td>';
            } else {
                item_html += '<td><select class="form-control select_width" name="isnew[]"><option value="0">否</option><option value="1" selected>是</option></select></td>';
            }
            if(ishot == 0) {
                item_html += '<td><select class="form-control select_width" name="ishot[]"><option value="0" selected>否</option><option value="1">是</option></select></td>';
            } else {
                item_html += '<td><select class="form-control select_width" name="ishot[]"><option value="0">否</option><option value="1" selected>是</option></select></td>';
            }
            if(ishot2 == 0) {
                item_html += '<td><select class="form-control select_width" name="ishot2[]"><option value="0" selected>否</option><option value="1">是</option></select></td>';
            } else {
                item_html += '<td><select class="form-control select_width" name="ishot2[]"><option value="0">否</option><option value="1" selected>是</option></select></td>';
            }
            if(isguan == 0) {
                item_html += '<td><select class="form-control select_width" name="isguan[]"><option value="0" selected>否</option><option value="1">是</option></select></td>';
            } else {
                item_html += '<td><select class="form-control select_width" name="isguan[]"><option value="0">否</option><option value="1" selected>是</option></select></td>';
            }
            item_html += '<td><a href="javascript:void(0)" onclick="deleteRecord(this)">删除</a></td>';
            item_html += '</tr>';
            if(typeof children_menu_id == 'undefined') {
                return item_html;
            } else {
                $("#"+ children_menu_id).append(item_html);
            }
        }

        function saveAll() {
            var form_ob = $("#form_category");
            var names_ob = form_ob.find("input[name='name[]']");
            var paths_ob = form_ob.find("input[name='path[]']");
            var sorts_ob = form_ob.find("input[name='sort[]']");
            var children_ob = form_ob.find("input[name='children[]']");
            var result = [];
            var name = '', path='', sort=0, children={};

            for(var i=0; i<names_ob.length; i++) {
                name = $.trim($(names_ob[i]).val());
                path = $.trim($(paths_ob[i]).val());
                sort = parseInt($.trim($(sorts_ob[i]).val()));
                children = $.trim($(children_ob[i]).val());
                if(children != '') {
                    children = $.parseJSON(decodeURI(children));
                } else {
                    children = {};
                }
                if(name == '') {
                    continue ;
                }
                result.push({"name":name, "path":path, "sort":sort, "children":children});
            }
            result.sort(ascend);
            ajaxSave(JSON.stringify(result), 'modify');
        }

        function saveChildren(input_ob_jq) {
            var form_ob = $("#form_children");
            var names_ob = form_ob.find("input[name='name[]']");
            var paths_ob = form_ob.find("input[name='path[]']");
            var sorts_ob = form_ob.find("input[name='sort[]']");
            var logo_pc_ob = form_ob.find("input[name='logo_pc[]']");
            var logo_h5_ob = form_ob.find("input[name='logo_h5[]']");
            var isnews_ob = form_ob.find("select[name='isnew[]']");
            var ishots_ob = form_ob.find("select[name='ishot[]']");
            var ishot2s_ob = form_ob.find("select[name='ishot2[]']");
            var isguans_ob = form_ob.find("select[name='isguan[]']");
            var result = [];
            var name = '', path='', sort=0, logo_pc='', logo_h5='', isnew=0, ishot=0, ishot2=0, isguan=0;
            for (var i=0; i<names_ob.length; i++) {
                name = $.trim($(names_ob[i]).val());
                path = $.trim($(paths_ob[i]).val());
                sort = parseInt($.trim($(sorts_ob[i]).val()));
                logo_pc = $.trim($.trim($(logo_pc_ob[i]).val()));
                logo_h5 = $.trim($.trim($(logo_h5_ob[i]).val()));
                isnew = parseInt($(isnews_ob[i]).val());
                ishot = parseInt($(ishots_ob[i]).val());
                ishot2 = parseInt($(ishot2s_ob[i]).val());
                isguan = parseInt($(isguans_ob[i]).val());
                if(name == '' || path == '') {
                    continue ;
                }
                result.push({"name":name, "path":path, "sort":sort, "logo_pc":logo_pc, "logo_h5":logo_h5, "isnew":isnew, "ishot":ishot, "ishot2":ishot2, "isguan":isguan});
            }
            result.sort(ascend);
            input_ob_jq.val(encodeURI(JSON.stringify(result)));
            saveAll();
        }

        function ascend(x, y) {
            return x.sort - y.sort;
        }

        function importData() {
            var textarea = '【'+ menu_name +'】菜单JSON数据：<textarea name="import_json_data" id="import_json_data" cols="100" rows="20" placeholder="JSON数据"></textarea>';
            BootstrapDialog.show({
                title: '导入【'+ menu_name +'】菜单JSON数据',
                message: textarea,
                size: 'size-wide',
                buttons: [{
                    label: '保存',
                    cssClass: 'btn-primary',
                    action: function(dialogItself){
                        var json_data = $.trim($("#import_json_data").val());
                        if(json_data == '') {
                            BootstrapDialog.alert("JSON 内容不能为空");
                            return false;
                        }
                        try {
                            json_data =  $.parseJSON(json_data);
                        } catch (e) {
                            BootstrapDialog.alert("JSON 格式不正确");
                            return false;
                        }
                        json_data = JSON.stringify(json_data);
                        ajaxSave(json_data, 'import');
                        dialogItself.close();
                    }
                }, {
                    label: 'Close',
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }]
            });
        }

        function ajaxSave(json_string, edit_type) {
            $.ajax({
                url: "/frontmenu/editdata",
                dataType: "json",
                method: "POST",
                data:{"id":menu_id, "ident":menu_ident, "data":json_string, "old_data":JSON.stringify(menu_data), "edit_type":edit_type},
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
                }
            });
        }

        $(document).ready(function () {
            init();
            $("#save_btn").click(function () {
                saveAll();
            });
            $("#add_category_btn").click(function () {
                addCategoryItem({});
            });
            $("#import_data_btn").click(function () {
                importData();
            });
        })
    </script>
@stop
