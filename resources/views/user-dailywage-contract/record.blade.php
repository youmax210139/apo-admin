@extends('layouts.base')
@section('title','用户工资契约')
@section('function','用户工资契约')
@section('function_link', '/userdailywagecontract/')
@section('here','契约调整记录')
@section('content')
    @if($wage_line_multi_available == 1 && $lines)
        <div class="row page-title-row" style="margin:5px;">
            <div class="col-md-12">
                @foreach($lines as $tmp_line)
                    <a  href="{{url('userdailywagecontract/record')}}?user_id={{$user->id}}&wage_type={{$tmp_line->type}}"
                        type="button" class="btn @if($line_type == $tmp_line->type) btn-primary @else  btn-default @endif">{{__("wage.line_type_".$tmp_line->type)}}</a>
                @endforeach
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">【{{$user->username}}】的 {{__("wage.line_type_".$line_type)}} 契约调整记录</h3>
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
                            @if($show_conditions['bet'])
                            <th>日销量</th>
                            @endif
                            @if($show_conditions['active'])
                            <th>活跃人数</th>
                            @endif
                            @if($show_conditions['profit'])
                                <th>盈亏</th>
                            @endif
                            @if($show_conditions['rate'])
                            <th>比率(%)</th>
                            @endif
                            @if($show_conditions['win_rate'])
                            <th>中单比例(%)</th>
                            @endif
                            @if($show_conditions['loss_rate'])
                            <th>挂单比例(%)</th>
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
                                    @if($show_conditions['bet'])
                                    <td>
                                        @foreach($v->content as $_row)
                                            {{$_row['bet']??0}}<br>
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
                                    @if($show_conditions['profit'])
                                        <td>
                                            @foreach($v->content as $_row)
                                                {{$_row['profit']??0}}<br>
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
                                    @if($show_conditions['win_rate'])
                                    <td>
                                        @foreach($v->content as $_row)
                                            {{$_row['win_rate']}}<br>
                                        @endforeach
                                    </td>
                                    @endif
                                    @if($show_conditions['loss_rate'])
                                        <td>
                                            @foreach($v->content as $_row)
                                                {{$_row['loss_rate']}}<br>
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
