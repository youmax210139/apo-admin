@extends('layouts.base')

@section('title','提现通道管理')

@section('function','提现通道管理')
@section('function_link', '/withdrawalchannel/')

@section('here','提现通道列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('withdrawalchannel/create'))
                <a href="/withdrawalchannel/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加提现通道</a>
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
        <div class="col-sm-12">
            <div class="box  box-primary">

                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <div>
                        <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/withdrawalchannel/'" type="button" class="btn {{1==$status?'bg-primary':''}}" style="margin: 5px">启用</button>
                        <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/withdrawalchannel/?status=0'" type="button" class="btn {{0==$status?'bg-primary':''}} " style="margin: 5px">禁用</button>
                    </div>
                    <ul class="list-group" style="margin:10px 0px;">
                        <li class="list-group-item active">
                            <div class='row payment-info '>
                                <div class="col-md-1 text-center">ID</div>
                                <div class="col-md-2 text-center">通道名称</div>
                                <div class="col-md-2 text-center">渠道名称</div>
                                <div class="col-md-2 text-center">商户号</div>
                                <div class="col-md-2 text-center">域名</div>
                                <div class="col-md-1 text-center">状态</div>
                                <div class="col-md-2 text-center">可选操作</div>
                            </div>
                        </li>
                        @forelse ($channels as $cate)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-1 text-center"><strong>{{ $cate->id }}</strong></div>
                                    <div class="col-md-2 text-center"><strong>{{ $cate->channel_name }}</strong></div>
                                    <div class="col-md-2 text-center"><strong>{{ $cate->category_name}}</strong></div>
                                    <div class="col-md-2 text-center"><strong>{{ $cate->merchant_id }}</strong></div>
                                    <div class="col-md-2 text-center">
                                        <strong>
                                            @foreach ($domains as $item)
                                                {{ $item->id == $cate->domain_id ? $item->domain : '' }}
                                            @endforeach
                                        </strong>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        @if ($cate->status == 1)
                                            <span class="label label-success" id="status_show_{{ $cate->id }}">启用</span>
                                        @else
                                            <span class="label label-danger" id="status_show_{{ $cate->id }}">禁用</span>
                                        @endif
                                    </div>
                                    <div class="col-md-2 text-center">
                                        @if(Gate::check('withdrawalchannel/edit'))
                                            <a style="padding: 5px;" href="/withdrawalchannel/edit?id={{ $cate->id }}" class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a>
                                        @endif
                                        @if(Gate::check('withdrawalchannel/delete'))
                                            <a style="padding: 5px;" href="javascript:;" onclick="delWithdrawalChannel({{$cate->id}})"
                                               class="btn-sm text-danger"><i class="fa fa-times-circle"></i>删除</a>
                                        @endif
                                            @if ($cate->status == 1)
                                                <a  style="padding: 5px;" href="javascript:;" data-id='{{ $cate->id }}' data-set-status='0' class="disable btn-sm  text-danger set-status"><i class="fa fa-ban"></i> 禁用</a>
                                            @else
                                                <a style="padding: 5px;" href="javascript:;" data-id='{{ $cate->id}}' data-set-status='1' class="disable btn-sm  text-success set-status"><i class="fa fa-check-circle-o"></i> 启用</a>
                                            @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">空数据</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <!--删除-->
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
                        确认要删除该提现通道吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
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
        function delWithdrawalChannel(id){
            $('.deleteForm').attr('action', '/withdrawalchannel/?id=' + id);
            $('#modal-delete').modal();
        }

        //状态
        $('.set-status').bind('click',function(){
            var obj = $(this);
            var id = obj.attr('data-id');
            var show_obj = $("#status_show_"+id);
            var status = obj.attr('data-set-status');
            var status_txt = status == 1 ? '启用':'禁用';
            $.ajax({
                url: "/withdrawalchannel/edit",
                dataType: "json",
                method: "PUT",
                data:{"id":id, "set_status":1, "status":status,},
            }).done(function (json) {
                if(json.status == 0) {
                    if(status == '1') {
                        obj.attr('data-set-status', '0');
                        obj.removeClass('text-success');
                        obj.addClass('text-danger');
                        obj.html('<i class="fa fa-ban"></i> 禁用');
                        show_obj.removeClass('label-danger');
                        show_obj.addClass('label-success');
                        show_obj.html('启用');

                    } else {
                        obj.attr('data-set-status', '1');
                        obj.removeClass('text-danger');
                        obj.addClass('text-success');
                        obj.html('<i class="fa fa-check-circle-o"></i> 启用');
                        show_obj.removeClass('label-success');
                        show_obj.addClass('label-danger');
                        show_obj.html('禁用');
                    }
                    BootstrapDialog.alert({
                        title: status_txt + ' 提现通道',
                        message: json.msg,
                        type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                        closable: true, // <-- Default value is false
                        draggable: true, // <-- Default value is false
                        buttonLabel: '关闭', // <-- Default value is 'OK',
                        callback: function(result) {
                            //document.location.reload();
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
    </script>
@stop