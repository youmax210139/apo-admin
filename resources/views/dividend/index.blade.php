@extends('layouts.base')

@section('title','分红契约列表')

@section('function','分红契约列表')
@section('function_link', '/dividend/')

@section('here','分红契约列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
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
                                <label for="username" class="col-sm-3 col-sm-3 control-label">用户名</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name='username' placeholder="用户名" value="{{$username}}"/>
                                </div>
                                <label class="control-label checkbox-inline col-sm-3" style="padding-top: 7px;">
                                    <input type="checkbox" name="include_all" value="1" @if($username) checked @endif/>
                                    包含所有下级
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="user_type_id" class="col-sm-3 control-label">契约状态</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="-1">所有</option>
                                        <option value="0" selected>已签订</option>
                                        <option value="1" >签订中</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_group_id" class="col-sm-3 control-label">用户组别</label>
                                <div class="col-sm-9">
                                    <select name="user_group_id" class="form-control">
                                        <option value="all">所有组别</option>
                                        @foreach($user_group as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_type_id" class="col-sm-3 control-label">用户类型</label>
                                <div class="col-sm-9">
                                    <select name="user_type_id" class="form-control">
                                        <option value="all">所有类型</option>
                                        @foreach($user_type as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
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

            <div class="box box-primary">

                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover " >
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">级别</th>
                            <th class="hidden-sm">组别</th>
                            <th class="hidden-sm">分红类型</th>
                            <th class="hidden-sm">最高分红比率</th>
                            <th class="hidden-sm">分红模式</th>
                            <th class="hidden-sm">创建时间</th>
                            <th class="hidden-sm">操作时间</th>
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

    <div class="modal fade modal-default" id="show_log" tabIndex="-1">
        <div class="modal-dialog modal-default modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title ">分红契约调整记录</h4>
                </div>
                <div class="modal-body" style="max-height:500px;overflow-y: auto;">
                    <table class="table table-hover table-striped table-bordered" id="user_level_table">
                        <tbody>
                        <tr>
                            <th colspan="7" class="text-center" id="dividend_log_title"></th>
                        </tr>
                        <tr>
                            <th class="text-center">调整时间</th>
                            <th class="text-center">状态</th>
                            <th class="text-center">操作位置</th>
                            <th class="text-center">操作人</th>
                            <th class="text-center">日盈亏(万)</th>
                            <th class="text-center">活跃人数</th>
                            <th class="text-center">分红比率</th>
                        </tr>
                        <tbody id="dividend_log_content">

                        </tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
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
                        确认要清理该代理线分红么?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST" action="/dividend">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="user_id" value="" id="del_user_id">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <style>
        .style_ol{
            margin: 0;
            padding: 0px 0;
            list-style: none;
        }
        .style_ol li{
            padding: 1px 0px;
        }
        #dividend_log_content td{
            vertical-align: middle;
        }
    </style>
@stop

@section('js')
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script>
    $(function () {
        var get_params = function (data) {
            var param = {
                'username': $("input[name='username']").val(),
                'user_group_id': $("select[name='user_group_id']").val(),
                'user_type_id': $("select[name='user_type_id']").val(),
                'status': $("select[name='status']").val(),
                'include_all': $("input[name='include_all']:checked").val()
            };
            return $.extend({}, data, param);
        }

        var table = $("#tags-table").DataTable({
            language:app.DataTable.language(),
            pageLength: 25,
            serverSide: true,
            searching: false,
            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
            // 要 ajax(url, type) 必须加这两参数
            ajax: app.DataTable.ajax('/dividend/', null, get_params),
            "columns": [
                {"data": "id"},
                {"data": "username"},
                {"data": "user_type_name"},
                {"data": "user_group_name"},
                //{"data": "status"},
                {"data": "top_type"},
                {"data": "top_rate"},
                {"data": "mode"},
                {"data": "created_at"},
                {"data": "accept_at"},
                {"data": null},
            ],
            columnDefs: [
                {
                    'targets': 1,
                    "render": function (data, type, row) {
                        if (row.self != undefined && row.self != null && row.self) {
                            if (row['user_observe']) {
                                return app.getColorHtml(row.username, 'label-danger', false);
                            } else {
                                return row.username;
                            }
                        } else {
                            if (row['user_observe']) {
                                return '<a href="/dividend/?username=' + row.username + '" class="label-danger">' + row.username + '</a>';
                            } else {
                                return '<a href="/dividend/?username=' + row.username + '" >' + row.username + '</a>';
                            }
                        }
                    }
                },
                {
                    'targets': 2,
                    "render": function (data, type, row) {
                        return app.getLabelHtml(data, 'label-primary');
                    }
                },
                {
                    'targets': 3,
                    "render": function (data, type, row) {
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
                    "render": function (data, type, row) {
                        var label = 'label-success';
                        var title = 'B线[分红模式]';
                        if (data == 1 ) {
                            label = 'label-danger';
                            title = 'A线[佣金模式]';
                        }
                        return app.getLabelHtml(title, label);
                    }
                },
                {
                    'targets': 5,
                    "render": function (data, type, row) {
                        if( row.top_type == 1 ) return '';
                        return data;
                    }
                },
                /*
                {
                    'targets': 4,
                    "render": function (data, type, row) {
                        var label = 'label-warning';
                        if( data == 1){
                            label = 'label-success';
                            data = '已同意';
                        }else if( data==2 ){
                            label = 'label-danger';
                            data = '已拒绝';
                        }else if( data==3 ){
                            label = 'label-default';
                            data = '已失效';
                        }else if( data==0 ){
                            data = '待确认';
                        }
                        return app.getLabelHtml(data, label);
                    }
                },
                */
                {
                    'targets': 6,
                    "render": function (data, type, row) {
                        if( row.top_type == 1 ) return '';
                        var label = 'label-warning';
                        var text = '不累计';
                        if (row.mode == 1){
                            label = 'label-success';
                            text = '累计';
                        }
                        return app.getLabelHtml(text, label);
                    }
                },
                {
                    'targets': 7,
                    "render": function (data, type, row) {
                        return data!=null?data:'';
                    }
                },
                {
                    'targets': 8,
                    "render": function (data, type, row) {
                        if( row.status == 3 && row.delete_at!=undefined && row.delete_at!=null){
                            return row.delete_at;
                        }
                        return (row.accept_at!=undefined&&row.accept_at!=null)?row.accept_at:'';
                    }
                },
                {
                    'targets': -1,
                    'width':'20%',
                    "render": function (data, type, row) {
                        if( row.top_type == 1 ){
                            return '';
                        }

                        var str = '';
                        str += '<a href="javascript:;" data-id="' + row.id + '" class="X-Small btn-xs text-primary record"> 契约调整记录</a>';
                        if( row.status === 0 ){
                            str += '<a href="/dividend/createoredit?user_id=' + row.id + '" title="设定分红契约" class="X-Small btn-xs text-primary setting" mounttabs> 设定分红契约</a>';
                        }else {
                            str += '<a href="/dividend/createoredit?user_id='+row.id+'" title="重新签订契约分红" class="X-Small btn-xs text-primary setting" mounttabs> 重新签订契约</a>';
                        }

                        str += '<a href="javascript:;" data-id="' + row.id + '" class="X-Small btn-xs text-danger clear"> 清除团队分红契约</a>';
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

        $('#search_btn').click(function () {
            event.preventDefault();
            table.ajax.reload();
        });

        $("table").delegate('.record', 'click', function () {
            var _this = $(this);
            var id = $(this).attr('data-id');
            loadShow();

            //清空数据
            $('#dividend_log_content').html('');
            $('#dividend_log_title').text('');

            $.ajax({
                url: "/dividend/record",
                dataType: "json",
                method: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    user_id: id
                }
            }).done(function (json) {
                loadFadeOut();
                if (json.hasOwnProperty('code') && json.code == '302') {
                    window.location.reload();
                }
                if( json.hasOwnProperty('status') && json.status == '0' ){
                    var username = !(json.data[0]!=undefined&&json.data[0]!=null)?json.data[0].username:'';
                    $('#dividend_log_title').text(username+'日工资契约调整记录');
                    var html = '';
                    // [{"profit":10,"daus":5,"rate":10},{"profit":15,"daus":7,"rate":15},{"profit":20,"daus":10,"rate":20},{"profit":50,"daus":15,"rate":25}]
                    for(var i in json.data){
                        html += "<tr>";

                        // 日盈亏
                        var profix_html = '';
                        // 活跃人数
                        var daus_html = '';
                        // 分红比率
                        var rate_html = '';
                        for(var x in json.data[i].content){
                            var content=json.data[i].content[x];
                            profix_html += '<li>'+content.profit+'</li>'
                            daus_html += '<li>'+content.daus+'</li>'
                            rate_html += '<li>'+content.rate+' %</li>'
                        }

                        // 操作位置
                        var stage_html = app.getLabelHtml((json.data[i].stage==2)?'后台':'前台', (json.data[i].stage==2)?'label-warning':'label-success');

                        var status_text  = '';
                        var status_style = '';
                        if( json.data[i].status==1 ){
                            status_text  = '已同意';
                            status_style = 'label-success';
                        }else if( json.data[i].status==2 ){
                            status_text  = '已拒绝';
                            status_style = 'label-danger';
                        }else if( json.data[i].status==3 ){
                            status_text  = '已失效';
                            status_style = 'label-default';
                        }else if( json.data[i].status==0 ){
                            status_text  = '待确认';
                            status_style = 'label-warning';
                        }
                        var status_html = app.getLabelHtml(status_text,status_style)

                        html += "<td class='text-center'>添加于 "+json.data[i].created_at+"<br>同意于 "+json.data[i].accept_at+"<br>失效于 "+json.data[i].delete_at+"<br></td>";
                        html += "<td class='text-center'>"+status_html+"</td>";
                        html += "<td class='text-center'>"+stage_html+"</td>";
                        html += "<td class='text-center'>"+json.data[i].stage_username+"</td>";
                        html += "<td class='text-center'><ol class='style_ol'>"+profix_html+"</ol></td>";
                        html += "<td class='text-center'><ol class='style_ol'>"+daus_html+"</ol></td>";
                        html += "<td class='text-center'><ol class='style_ol'>"+rate_html+"</ol></td>";
                        html += "</tr>";
                    }
                    $('#dividend_log_content').html(html);
                    $('#show_log').modal('show');
                }else{
                    app.bootoast(json.msg);
                }
            });
        });


        $("table").delegate('.clear', 'click', function () {
            var _this = $(this);
            var id = $(this).attr('data-id');

            if( id == undefined || id == null){
                return false;
            }

            $('#del_user_id').val(id);
            $('#modal-delete').modal('show');

        });


    });
</script>
@stop
