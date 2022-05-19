@extends('layouts.base')

@section('title','用户私返契约')
@section('function','用户私返契约')
@section('function_link', '/userprivatereturn/')
@section('here','契约调整记录')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">【{{$user->username}}】的私返契约调整记录</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>创建时间</th>
                            <th>删除时间</th>
                            <th>创建者</th>
                            <th>删除者</th>
                            <th>操作位置</th>
                            <th>时间类型</th>
                            <th>私返类型</th>
                            <th>私返基数</th>
                            @if($show_conditions['bet'])
                                <th>销量({{$rate_unit}})</th>
                            @endif
                            @if($show_conditions['profit'])
                                <th>亏损({{$rate_unit}})</th>
                            @endif
                            @if($show_conditions['active'])
                                <th>活跃人数</th>
                            @endif
                            @if($show_conditions['rate'])
                                <th>比率(%)</th>
                            @endif
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($logs)
                            @foreach($logs as $v)
                                <tr>
                                    <td>{{$v->id}}</td>
                                    <td>{{$v->created_at}}</td>
                                    <td>{{$v->deleted_at}}</td>
                                    <td>{{$v->created_username}}</td>
                                    <td>{{$v->deleted_username}}</td>
                                    <td><span class="label label-primary">
                                        @if ($v->stage == 1)
                                            前台
                                        @else
                                            后台
                                        @endif
                                    </span></td>
                                    <td>{{$v->time_type}}</td>
                                    <td>{{$v->condition_type}}</td>
                                    <td>{{$v->cardinal_type}}</td>
                                    @if($show_conditions['bet'])
                                    <td>
                                        @foreach($v->content as $_row)
                                            {{$_row['bet']??0}}<br>
                                        @endforeach
                                    </td>
                                    @endif
                                    @if($show_conditions['profit'])
                                        <td>
                                            @foreach($v->content as $_row)
                                                {{$_row['profit']??0}}<br>
                                            @endforeach
                                        </td>
                                    @endif
                                    @if($show_conditions['active'])
                                    <td>
                                        @foreach($v->content as $_row)
                                            {{$_row['active']??0}}<br>
                                        @endforeach
                                    </td>
                                    @endif
                                    @if($show_conditions['rate'])
                                        <td>
                                            @foreach($v->content as $_row)
                                                {{$_row['rate']}}<br>
                                            @endforeach
                                        </td>
                                    @endif
                                    <td>
                                        @if ($v->status == 0)
                                            <span class="label label-success">生效中</span>
                                        @else
                                            <span class="label label-warning">已失效</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9">暂无数据</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer text-center">
                    <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
                        <i class="fa fa-arrow-left"></i> 返回
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
