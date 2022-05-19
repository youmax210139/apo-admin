@extends('layouts.base')

@section('title','支付通道管理')

@section('function','支付通道管理')
@section('function_link', '/paymentchannel/')

@section('here','支付通道列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <style>
        .payment-info>div {word-wrap: break-word;padding:0px 5px;}
    </style>

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('paymentchannel/create'))
                <a href="/paymentchannel/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加支付通道</a>
            @endif
                @if(Gate::check('paymentchannel/refreshserver'))
                    <a href="/paymentchannel/refreshserver/" class="btn btn-warning btn-md"><i class="fa fa-plus-circle"></i> 同步到服务器</a>
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
                        <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/paymentchannel/'" type="button" class="btn {{1==$status?'bg-primary':''}}" style="margin: 5px">启用</button>
                        <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/paymentchannel/?status=0'" type="button" class="btn {{0==$status?'bg-primary':''}} " style="margin: 5px">禁用</button>
                    </div>
                    <ul class="list-group" style="margin:10px 0px;">
                        <li class="list-group-item active">
                            <div class='row payment-info '>
                                <div class="col-md-1 text-center">编号</div>
                                <div class="col-md-1 text-center">前台名称</div>
                                <div class="col-md-1 text-center">后台名称</div>
                                <div class="col-md-1 text-center">渠道</div>
                                <div class="col-md-1 text-center">类型</div>
                                <div class="col-md-1 text-center">账号</div>
                                <div class="col-md-2 text-center">域名</div>
                                <div class="col-md-1 text-center">同步情况</div>
                                <div class="col-md-1 text-center">排序</div>
                                <div class="col-md-1 text-center">状态</div>
                                <div class="col-md-1 text-center">可选操作</div>
                            </div>
                        </li>
                        @forelse ($rows as $item)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-1 text-center"><strong>{{ $item['id'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['front_name'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['name'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['payment_category_name'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['payment_method_name'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['account_number'] }}</strong></div>
                                    <div class="col-md-2 text-center"><strong>{{ $item['payment_domain_domain'] }}</strong></div>
                                    <div class="col-md-1 text-center">
                                        @if($item['payment_method_sync'] && !empty($item['servers_sync_status']))
                                            @foreach ($item['servers_sync_status'] as $server)
                                                @if ($server['checked'] == 1)
                                                    {{ $server['name'] }}<span class="glyphicon glyphicon-ok text-success"></span><br>
                                                @else
                                                    {{ $server['name'] }}<span class="glyphicon glyphicon-remove text-danger"></span><br>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['sort'] }}</strong></div>
                                    <div class="col-md-1 text-center">
                                        @if ($item['status'] == 1)
                                            {{--<span class="label label-success">启用</span>--}}
                                            <span   class="label label-success" id="status_show_{{ $item['id'] }}" >启用</span>
                                        @else
                                            <span  class="label label-danger" id="status_show_{{ $item['id'] }}" >禁用</span>
                                        @endif
                                    </div>
                                    <div class="col-md-1 text-center">
                                        @if(Gate::check('paymentchannel/edit'))
                                            <a style="padding: 5px;" href="/paymentchannel/edit?id={{ $item['id'] }}"
                                               class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a><br>
                                            @if(Gate::check('paymentchannel/refreshserver') && $item['payment_method_sync'])
                                                <a style="padding: 5px;" href="/paymentchannel/refreshserver?id={{ $item['id'] }}"
                                                   class="btn-sm  text-warning"><i class="fa fa-upload"></i> 同步</a><br>
                                            @endif
                                        @endif
                                            @if ($item['status'] == 1)
                                                <a  style="padding: 5px;" href="javascript:;" data-id='{{ $item['id'] }}' data-set-status='0' class="disable btn-sm  text-danger set-status"><i class="fa fa-ban"></i> 禁用</a>
                                            @else
                                                <a style="padding: 5px;" href="javascript:;" data-id='{{ $item['id']}}' data-set-status='1' class="disable btn-sm  text-success set-status"><i class="fa fa-check-circle-o"></i> 启用</a>
                                            @endif
                                    </div>
                                </div>
                                <div class="panel  panel-default payment-account" style='margin: 10px 0px;display:none'></div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">空数据</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        //状态
        $('.set-status').bind('click',function(){
            var obj = $(this);
            var id = obj.attr('data-id');
            var show_obj = $("#status_show_"+id);
            var status = obj.attr('data-set-status');
            var status_txt = status == 1 ? '启用':'禁用';
            $.ajax({
                url: "/paymentchannel/edit",
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
                        title: status_txt + ' 支付通道',
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