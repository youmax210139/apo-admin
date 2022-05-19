@extends('layouts.base')

@section('title','推广链接管理')

@section('function','推广链接管理')
@section('function_link', '/registerurl/')

@section('here','推广链接列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            @include('partials.errors')
            @include('partials.success')
            <div class="box-body">
                <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                    <thead>
                        <tr>
                            <th class="hidden-sm">编号</th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">注册人数</th>
                            <th class="hidden-sm">注册类型</th>
                            <th class="hidden-sm" data-sortable="false">域名</th>
                            <th class="hidden-sm" data-sortable="false">推广码</th>
                            <th class="hidden-sm" data-sortable="false">返点信息</th>
                            <th class="hidden-sm" data-sortable="false">创建时间</th>
                            <th class="hidden-sm" data-sortable="false">过期时间</th>
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

<div class="modal fade" id="modal-show" tabIndex="-1">
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
                    确认要<span class="row_show_text">删除</span>该注链接吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="showForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times-circle"></i> 确认
                    </button>
                </form>
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
        language: app.DataTable.language(),
        order: [[1, "asc"]],
        serverSide: true,
        iDisplayLength: 25,
// 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
// 要 ajax(url, type) 必须加这两参数
        ajax: app.DataTable.ajax(),
        "columns": [
            {"data": "id"},
            {"data": "username"},
            {"data": "num"},
            {"data": "user_type"},
            {"data": "domain"},
            {"data": "code"},
            {"data": 'rebates'},
            {"data": "created_at"},
            {"data": "expired"},
            {"data": "action"}
        ],
        columnDefs: [
            {
                'targets': -1,
                "render": function (data, type, row) {
                    var str = '';

                    //下级菜单
                    var common_class = 'X-Small btn-xs ';


                    var a_attr = {
                        'class': common_class + 'text-danger del',
                        'href': '#',
                        'attr': row['id']
                    };
                    str += app.getalinkHtml('删除', a_attr, 'fa-delete');

                    return str;
                }
            },
            {
                'targets': 3,
                'render': function (data, type, row) {
                    return app.getLabelHtml(
                            row['user_type']==3 ? '会员' : '代理',
                            'label-' + (row['user_type']==3 ? 'warning' : 'info')
                            );
                }
            },
            {
                'targets': -4,
                'render': function (data, type, row) {
                    var rebates = row['rebates'];
                    var str = "";
                    if(typeof rebates.lottery == 'undefined'){
                        rebates.lottery = {
                            title: "彩票",
                            value: '-'
                        };
                    }
                    str += '<span class="btn-xs text-primary">'+rebates.lottery.title+"："+rebates.lottery.value+'</span><span class="more_links btn-xs text-primary" style="margin-left: 15px;text-decoration:underline; cursor: pointer">详细</span>';
                   
                    str += '<div class="pop_links" style="display: none;">';
                    $.each(rebates,function(k,v){
                      str += '<p class="btn-xs text-primary ">'+v.title+'：'+v.value+'</p>';
                    });
                    str += '</div>';
                    return str;
                }
            }
        ],
                drawCallback: function () {
                    $('.more_links').popover({
                        trigger: "manual" ,
                        html: true,
                        animation:false,
                        placement: 'auto right',
                        container: 'body',
                        content: function () {
                            return  $(this).siblings(".pop_links").html();
                        }
                    }).on("mouseenter", function () {
                        var _this = this;
                        $(this).popover("show");
                        $(".popover").on("mouseleave", function () {
                            $(_this).popover('hide');
                        });
                    }).on("mouseleave", function () {
                        var _this = this;
                        setTimeout(function () {
                            if (!$(".popover:hover").length) {
                                $(_this).popover("hide");
                            }
                        }, 100);
                    });
                }
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


//隐藏
    $("table").delegate('.del', 'click', function () {
        var id = $(this).attr('attr');
        $('.showForm').attr('action', '/registerurl/del?id=' + id);
        $("#modal-show").modal();
    });
});
</script>
@stop