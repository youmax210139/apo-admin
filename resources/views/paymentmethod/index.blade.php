@extends('layouts.base')

@section('title','支付类型管理')

@section('function','支付类型管理')
@section('function_link', '/paymentmethod/')

@section('here','类型列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('paymentmethod/create'))
                <a href="/paymentmethod/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加类型</a>
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
                    <ul class="list-group" style="margin:10px 0px;">
                        <li class="list-group-item active">
                            <div class='row payment-info '>
                                <div class="col-md-1 text-center">编号</div>
                                <div class="col-md-1 text-center">英文标识</div>
                                <div class="col-md-3 text-center">名称</div>
                                <div class="col-md-1 text-center">同步</div>
                                <div class="col-md-1 text-center">状态</div>
                                <div class="col-md-3 text-center">可选操作</div>
                            </div>
                        </li>
                        @forelse ($rows as $item)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-1 text-center"><strong>{{ $item->id }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item->ident }}</strong></div>
                                    <div class="col-md-3 text-center"><strong>{{ $item->name }}</strong></div>
                                    <div class="col-md-1 text-center">
                                        @if ($item->sync == 1)
                                            <span class="label label-success">是</span>
                                        @else
                                            <span class="label label-danger">否</span>
                                        @endif
                                    </div>
                                    <div class="col-md-1 text-center">
                                        @if ($item->status == 1)
                                            <span class="label label-success">启用</span>
                                        @else
                                            <span class="label label-danger">禁用</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3 text-center">
                                        @if(Gate::check('paymentmethod/edit'))
                                            <a style="padding: 5px;" href="/paymentmethod/edit?id={{ $item->id }}"
                                               class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a>
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

@stop