@extends('layouts.base')

@section('title','用户转移记录')

@section('function','用户转移记录')
@section('function_link', '/usermigration/')

@section('here','用户转移记录')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('usermigration/index'))
                <a href="/usermigration/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 用户转移</a>
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
                                <div class="col-md-2 text-center">转移用户及其下级</div>
                                <div class="col-md-2 text-center">新父级</div>
                                <div class="col-md-2 text-center">旧父级</div>
                                <div class="col-md-2 text-center">操作管理员</div>
                                <div class="col-md-2 text-center">操作时间</div>
                            </div>
                        </li>
                        @forelse ($rows as $item)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-1 text-center">{{ $item->id }}</div>
                                    <div class="col-md-2 text-center">{{ $item->username }}</div>
                                    <div class="col-md-2 text-center">{{ $item->new_parent_username }}</div>
                                    <div class="col-md-2 text-center">{{ $item->old_parent_username }}</div>
                                    <div class="col-md-2 text-center">{{ $item->admin_username }}</div>
                                    <div class="col-md-2 text-center">{{ $item->created_at }}</div>
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