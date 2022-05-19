@extends('layouts.base')

@section('title','追号纪录明细')
@section('function','追号纪录')
@section('function_link', '/project/')
@section('here','追号纪录明细')

{{--@section('pageDesc','DashBoard')--}}
@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">追号单详情</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <table  class="table table-striped table-bordered table-hover">
                                <tr>
                                    <th>追号编号</th>
                                    <td>{{$task->task_no}}</td>
                                </tr>
                                <tr>
                                    <th>游戏用户</th>
                                    <td>{{$task->username}}</td>
                                </tr>
                                <tr>
                                    <th>追号时间</th>
                                    <td>{{$task->created_at}}</td>
                                </tr>
                                <tr>
                                    <th>彩种</th>
                                    <td>{{$task->lottery_name}}</td>
                                </tr>
                                <tr>
                                    <th>玩法</th>
                                    <td>{{$task->method_name}}</td>
                                </tr>
                                <tr>
                                    <th>模式：</th>
                                    <td>{{$task->mode}}</td>
                                </tr>
                                @if(!empty($task->code_position))
                                <tr>
                                    <th>投注位置：</th>
                                    <td>{{$task->code_position}}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>追号内容</th>
                                    <td><div style="max-height:200px; overflow-y: auto;word-wrap: break-word; width: 700px;">{{$task->code}}</div></td>
                                </tr>
                                <tr>
                                    <th>开始期号</th>
                                    <td>{{$task->begin_issue}}</td>
                                </tr>
                                <tr>
                                    <th>追号期数</th>
                                    <td>{{$task->issue_count}} 期</td>
                                </tr>
                                <tr>
                                    <th>完成期数</th>
                                    <td>{{$task->finished_count}} 期</td>
                                </tr>
                                <tr>
                                    <th>取消期数</th>
                                    <td>{{$task->cancel_count}} 期</td>
                                </tr>
                                <tr>
                                    <th>追号总金额</th>
                                    <td>{{number_format($task->task_price, 4)}}</td>
                                </tr>
                                <tr>
                                    <th>完成金额</th>
                                    <td>{{number_format($task->finish_price, 4)}}</td>
                                </tr>
                                <tr>
                                    <th>取消金额</th>
                                    <td>{{number_format($task->cancel_price, 4)}}</td>
                                </tr>
                                <tr>
                                    <th>中奖后终止任务</th>
                                    <td>
                                        @if ($task->stoponwin==1)
                                            是
                                        @else
                                            否
                                        @endif
                                    </td>
                                </tr>
                                @if($task->project_fee_rate > 0 )
                                <tr>
                                    <th>服务费比例</th>
                                    <td>{{number_format($task->project_fee_rate * 100, 2)}} %</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>追号状态</th>
                                    <td>{{$task->status_label}}</td>
                                </tr>
                            </table>
                        </div>
                        @if(Gate::check('task/cancel'))
                        @if ($task->status==0)
                        <div class="panel-footer text-center">
                             <button type="button" class="btn btn-danger btn-md" id="cancel_task">
                             <i class="fa fa-times"></i> 停止追号
                             </button>
                        </div>
                        @endif
                        @endif
                    </div>

                    <div class="panel panel-primary">
                        <div class="panel-body">
                                <table  class="table table-striped table-bordered table-hover">
                                    <tr>
                                        <th><label class="checkbox-inline">奖期</label></th>
                                        <th>追号倍数</th>
                                        <th>追号状态</th>
                                        <th>注单详情</th>
                                    </tr>
                                    @foreach ($task_detail as $k => $taskdetail)
                                    <tr>
                                        <td>
                                            @if ($taskdetail->status < 2)
                                                @if ($taskdetail->canneldeadline > time())
                                                    <label class="checkbox-inline"><input type="checkbox" name="taskid[]" value="{{$taskdetail->entry}}"/></label>
                                                @endif
                                            @endif
                                            {{$taskdetail->issue}}</td>
                                        <td>{{$taskdetail->multiple}} 倍</td>
                                        <td>{{$taskdetail->status_label}}</td>
                                        <td>
                                            @if (!empty($taskdetail->project_id))<a mountTabs target="_blank" href="/project/detail/?id={{id_encode($taskdetail->project_id)}}">详情</a>@endif
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
                                    <th>后余额</th>
                                    <th>后冻结</th>
                                    <th>前余额</th>
                                    <th>前冻结</th>
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
                                        <td>{{$order->balance}}</td>
                                        <td>{{$order->hold_balance}}</td>
                                        <td class="@if($order->operation == 1)text-success @elseif($order->operation == 2) text-danger @endif">{{$order->pre_balance}}</td>
                                        <td class="@if($order->hold_operation == 1)text-success @elseif($order->hold_operation == 2) text-danger @endif">{{$order->pre_hold_balance}}</td>
                                        <td>{{$order->comment}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        $('#cancel_task').click(function(){
            BootstrapDialog.confirm({
                message: '确认终止此追号单吗？此操作将会终止此追号单所有未开始的追号',
                type: BootstrapDialog.TYPE_WARNING,
                closable: true,
                draggable: true,
                btnCancelLabel: '取消',
                btnOKLabel: '立即终止',
                btnOKClass: 'btn-warning',
                callback: function(result) {
                    if(result){
                        loadShow();
                        $.ajax({
                            url: "/task/cancel",
                            dataType: "json",
                            method: "POST",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data:{
                                'id':'{{ $task->id }}'
                            },
                        }).done(function (json) {
                            loadFadeOut();
                            BootstrapDialog.alert(json.msg,function(){
                                location.reload();
                            });
                        });
                    }
                }});
        });
    </script>
@stop