@extends('layouts.base')

@section('title','用户分层管理')

@section('function','用户分层管理')
@section('function_link', '/userlevel/')

@section('here','用户分层列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <style>
        .payment-info>div {word-wrap: break-word;padding:0px 5px;}
    </style>

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('userlevel/create'))
                <a href="/userlevel/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加用户分层</a>
            @endif
                @if(Gate::check('userlevel/refreshserver'))

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
                                <div class="col-md-1 text-center">层级</div>
                                <div class="col-md-1 text-center">描述</div>
                                <div class="col-md-1 text-center">加入时间</div>
                                <div class="col-md-1 text-center">存款次数</div>
                                <div class="col-md-1 text-center">存款总量</div>
                                <div class="col-md-1 text-center">最大存款</div>
                                <div class="col-md-1 text-center">累计消费</div>
                                <div class="col-md-1 text-center">提款次数</div>
                                <div class="col-md-2 text-center">提款总额</div>
                                <div class="col-md-1 text-center">状态</div>
                                <div class="col-md-1 text-center">操作</div>
                            </div>
                        </li>
                        @forelse ($rows as $item)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-1 text-center"><strong>{{ $item['id'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['name'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['register_start_time'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['deposit_times'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['deposit_count_amount'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['deposit_max_amount'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['expense_count_amount'] }}</strong></div>
                                    <div class="col-md-1 text-center"><strong>{{ $item['withdrawal_times'] }}</strong></div>
                                    <div class="col-md-2 text-center"><strong>{{ $item['withdrawal_count_amount'] }}</strong></div>
                                    <div class="col-md-1 text-center">
                                        @if ($item['status'] == 1)
                                            <span class="label label-success">启用</span>
                                        @else
                                            <span class="label label-danger">禁用</span>
                                        @endif
                                    </div>
                                    <div class="col-md-1 text-center">
                                        @if(Gate::check('userlevel/edit'))
                                            <a style="padding: 5px;" href="/userlevel/edit?id={{ $item['id'] }}"
                                               class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a><br>
                                        @endif
                                        @if(Gate::check('userlevel/delete') )

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