@extends('layouts.base')

@section('title','投注明细')
@section('function','投注纪录')
@section('function_link', '/project/')
@section('here','投注纪录明细')

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
        <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">注单详情</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <td>注单编号</td>
                                    <td>
                                        {{$project->project_no}}
                                    </td>
                                </tr>
                                @if($project->task_id !== 0)
                                <tr>
                                    <td>追号编号</td>
                                    <td><a href="/task/detail?id={{$project->task_id}}" target="_blank">{{$project->task_id}}</a></td>
                                </tr>
                                @endif
                                <tr>
                                    <td>投注耗时</td>
                                    <td>{{$project->process_time}}</td>
                                </tr>
                                <tr>
                                    <td>用户名</td>
                                    <td>{{$project->username}}</td>
                                </tr>
                                <tr>
                                    <td>销售开始时间</td>
                                    <td>{{$project->sale_start}}</td>
                                </tr>
                                <tr>
                                    <td>销售结束时间</td>
                                    <td>{{$project->sale_end}}</td>
                                </tr>
                                @if(!empty($project->requested_at))
                                <tr>
                                    <td>请求时间</td>
                                    <td>{{$project->requested_at}}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>投单时间</td>
                                    <td>{{$project->created_at}}</td>
                                </tr>
                                <tr>
                                    <td>投单IP</td>
                                    <td>{{$project->ip}}</td>
                                </tr>
                                <tr>
                                    <td>来源</td>
                                    <td>
                                        @if($project->client_type==1)
                                            web
                                            @elseif($project->client_type==2)
                                            IOS
                                        @elseif($project->client_type==3)
                                            Android
                                        @elseif($project->client_type==4)
                                            挂机
                                            @else
                                            未知
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>游戏</td>
                                    <td>{{$project->lottery_name}}</td>
                                </tr>
                                <tr>
                                    <td>玩法</td>
                                    <td>{{$project->method_name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>期号</td>
                                    <td>{{$project->issue}}</td>
                                </tr>
                                <tr>
                                    <td>模式：</td>
                                    <td>{{$project->mode}}</td>
                                </tr>
                                @if(!empty($project->code_position))
                                <tr>
                                    <td>投注位置</td>
                                    <td><div style="max-height:200px; overflow-y: auto;word-wrap: break-word; width: 700px;">{{$project->code_position}}</div></td>
                                </tr>
                                @endif
                                <tr>
                                    <td>投注内容</td>
                                    <td><div style="max-height:200px; overflow-y: auto;word-wrap: break-word; width: 700px;">{{$project->code}}</div></td>
                                </tr>
                                <tr>
                                    <td>倍数</td>
                                    <td>{{$project->multiple}}倍</td>
                                </tr>
                                <tr>
                                    <td>注数</td>
                                    <td>{{$project->bet_count}}</td>
                                </tr>
                                <tr>
                                    <td>投注总金额</td>
                                    <td class="text-red">{{number_format($project->total_price,4)}}</td>
                                </tr>
                                <tr>
                                    <td>原发奖金</td>
                                    <td class="text-red">{{number_format($project->original_bonus,4)}}</td>
                                </tr>
                                <tr>
                                    <td>实发奖金</td>
                                    <td class="text-red">{{number_format($project->bonus,4)}}</td>
                                </tr>
                                @if($project->project_fee > 0)
                                <tr>
                                    <td>服务费</td>
                                    <td class="text-red">{{number_format($project->project_fee,4)}} ( {{number_format($project->project_fee_rate * 100,4)}} % ) @if($project->project_fee_status == 1) 已返还 @endif</td>
                                </tr>
                                @endif
                                <tr>
                                    <td >中奖号码</td>
                                    <td>@if(empty($project->bonus_code) && !empty($project->mmc_code)){{$project->mmc_code}} @else {{$project->bonus_code}} @endif<!--这里需要针对不同彩种显示不同的中奖号码，例如KL8改的时时彩--></td>
                                </tr>
                                <!--根据不同玩法显示判断和值及盘面-->
                                @if(!empty($nohepan))
                                <tr>
                                    <td style="line-height:40px;">和值及盘面：</td>
                                    <td>{{$nohepan}}</td>
                                </tr>
                                @endif

                                <tr>
                                    <td>状态</td>
                                    <td>@if ($project->is_cancel==0)
                                        @if ($project->is_get_prize==0)
                                                <span class="label label-default">未开奖</span>
                                        @elseif ($project->is_get_prize==2)
                                                <span class="label label-danger">未中奖</span>
                                        @elseif ($project->is_get_prize==1)
                                        @if ($project->prize_status==0)
                                                    <span class="label label-info">未派奖</span>
                                        @else
                                                    <span class="label label-success">已派奖</span>
                                        @endif
                                        @endif
                                        @elseif ($project->is_cancel==1)
                                            <span class="label label-warning">用户撤单</span>
                                        @elseif ($project->is_cancel==2)
                                                    <span class="label label-warning">管理员撤单</span>
                                        @elseif ($project->is_cancel==3)
                                                            <span class="label label-warning">开错奖撤单</span>
                                        @endif
                                    </td>
                                </tr>
                                <!--
                                <tr>
                                    <td>is_wage_paid</td>
                                    <td>{{$project->is_wage_paid}}</td>
                                </tr>
                                -->
                            </table>
                        </div>
                        @if(Gate::check('project/cancel'))
                        @if ($project->is_cancel==0)
                        <div class="panel-footer text-center">
                             <button type="button" id="cancel_project" class="btn btn-danger btn-md" data-toggle="tooltip" data-placement="top" title="（停售 {{$canceltime}} 分钟之后不能撤单）">
                             <i class="fa fa-times"></i> 撤单
                             </button>
                        </div>
                        @endif
                        @endif
                    </div>

                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">可能中奖情况</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <th>编号</th>
                                    <th>奖级</th>


                                    <th>奖金</th>
                                </tr>
                                @foreach ($prizelevel as $k => $pl)
                                <tr>
                                    <td>{{$k}}</td>
                                    <td>{{$project->prize_level_name[$k]}}</td>

                                    <td>{{number_format($pl,5)}}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">返点信息</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <th>编号</th>
                                    <th>用户名</th>
                                    <th>点差</th>
                                    <th>金额</th>
                                    <th>状态</th>
                                </tr>
                                @foreach ($project_rebates as $rebate)
                                    <tr>
                                        <td>{{$rebate->id}}</td>
                                        <td>{{$rebate->username}}</td>
                                        <td>{{$rebate->diff_rebate}}</td>
                                        <td>{{number_format($rebate->amount,5)}}</td>
                                        <td>
                                            @if($rebate->status==0)
                                                未返
                                                @elseif($rebate->status==1)
                                                已返
                                                @else
                                                已撤
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">关联帐变</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <th>帐变编号</th>
                                    <th>账变时间</th>
                                    <th>用户名</th>
                                    <th>账变类型</th>
                                    <th>余额收入</th>
                                    <th>余额支出</th>
                                    <th>冻结收入</th>
                                    <th>冻结支出</th>
                                    <th>后余额/冻结</th>
                                    <th>前余额/冻结</th>
                                    <th>备注</th>
                                </tr>
                                @foreach ($orders as $k => $order)
                                    <tr>
                                        <td>{{id_encode($order->id)}}</td>
                                        <td>{{$order->created_at}}</td>
                                        <td>{{$order->username}}</td>
                                        <td>{{$order->type_name}}</td>
                                        <td class="text-success">@if($order->operation == 1)+{{$order->amount}} @endif</td>
                                        <td class="text-danger">@if($order->operation == 2)-{{$order->amount}} @endif</td>
                                        <td class="text-success">@if($order->hold_operation == 1)+{{$order->amount}} @endif</td>
                                        <td class="text-danger">@if($order->hold_operation == 2)-{{$order->amount}} @endif</td>
                                        <td>{{$order->balance}}/{{$order->hold_balance}}</td>
                                        <td>{{$order->pre_balance}}/{{$order->pre_hold_balance}}</td>
                                        <td>{{$order->comment}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">其他注单</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <th>编号</th>
                                    <th>彩种</th>
                                    <th>玩法</th>
                                    <th>金额</th>
                                    <th>奖金</th>
                                    <th>状态</th>
                                </tr>
                                @foreach ($order_project as $p)
                                    <tr>
                                        <td><a href="/project/detail?id={{$p->project_no}}">{{$p->project_no}}</a></td>
                                        <td>{{$p->lottery_name}}</td>
                                        <td>{{$p->method_name}}</td>
                                        <td>{{number_format($p->total_price,5)}}</td>
                                        <td>{{number_format($p->bonus,5)}}</td>
                                        <td>
                                            @if ($p->is_cancel==0)
                                                @if ($p->is_get_prize==0)
                                                    <span class="label label-default">未开奖</span>
                                                @elseif ($p->is_get_prize==2)
                                                    <span class="label label-danger">未中奖</span>
                                                @elseif ($p->is_get_prize==1)
                                                    @if ($p->prize_status==0)
                                                        <span class="label label-info">未派奖</span>
                                                    @else
                                                        <span class="label label-success">已派奖</span>
                                                    @endif
                                                @endif
                                            @elseif ($p->is_cancel==1)
                                                <span class="label label-warning">用户撤单</span>
                                            @elseif ($p->is_cancel==2)
                                                <span class="label label-warning">管理员撤单</span>
                                            @elseif ($p->is_cancel==3)
                                                <span class="label label-warning">开错奖撤单</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-cancel" tabIndex="-1">
        <div class="modal-dialog modal-primary">
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
                        确认要<span class="row_verify_text">撤销次注单</span>吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="canceForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="user_id" value="{{$project->user_id}}">
                        <input type="hidden" name="project_id" value="{{$project->id}}">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check-circle-o"></i> 确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
<script>
    $('#cancel_project').click(function(){
        var id = $(this).attr('attr');
        $('.canceForm').attr('action', '/project/cancel');
        $("#modal-cancel").modal();
    });
</script>
@stop