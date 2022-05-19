@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','用户详情')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">今日充值金额</span>
                            <span class="info-box-number">{{$user->today_deposit}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-bank"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">今日提现金额</span>
                            <span class="info-box-number">{{$user->today_withdrawal}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-blue">
                        <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">今日投注金额(彩)</span>
                            <span class="info-box-number">{{$user->today_bet}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-maroon">
                        <span class="info-box-icon"><i class="fa fa-trophy"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">今日派奖金额(彩)</span>
                            <span class="info-box-number">{{$user->today_bonus}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">基本信息</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                <tr>
                                    <td>用户名</td>
                                    <td>{{$user->username}}</td>
                                    <td>昵称</td>
                                    <td>{{$user->usernick}}</td>
                                </tr>
                                <tr>
                                    <td>级别</td>
                                    <td>{{$user->user_level}}</td>
                                    <td>组别</td>
                                    <td>{{$user->group->name}}<!--{{$user->group_id}}--></td>
                                </tr>
                                <tr>
                                    <td>总代</td>
                                    <td>{{$user->top_username}}<!--{{$user->top_id}}--></td>
                                    <td>上级</td>
                                    <td>{{$user->parent_username}}<!--{{$user->parent_id}}--></td>
                                </tr>
                                <tr>
                                    <td>彩票奖金组/返点</td>
                                    <td>{{$user->prize_level}}
                                        /{{2000 * $user->lottery_rebate + $user->prize_level}} {{$user->lottery_rebate*100}}
                                        %
                                    </td>
                                    <td>Email</td>
                                    <td>{{$user->email}}
                                        @if($user->email && \Illuminate\Support\Facades\Gate::check('user/unbindemail'))
                                            <button type="button" style="margin-left: 15px" class="btn btn-warning btn-xs" onclick="unbindProfile('{{$user->id}}','unbindemail','Email')">
                                                <i class="fa fa-ban"></i> 解绑
                                            </button>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>手机号码</td>
                                    <td>{{$user->telephone}}
                                        @if($user->telephone && \Illuminate\Support\Facades\Gate::check('user/unbindtelephone'))
                                            <button type="button" style="margin-left: 15px" class="btn btn-warning btn-xs" onclick="unbindProfile('{{$user->id}}','unbindtelephone','手机号码')">
                                                <i class="fa fa-ban"></i> 解绑
                                            </button>
                                            @else
                                            --
                                        @endif
                                    </td>
                                    <td>是否绑定谷歌登录器</td>
                                    <td>
                                        @if ($user->google_key)
                                            <code>是</code>
                                            @if( \Illuminate\Support\Facades\Gate::check('user/googlekey'))
                                                <button type="button" style="margin-left: 15px" class="btn btn-warning btn-xs" onclick="unbindProfile('{{$user->id}}','googlekey','谷歌登录器')">
                                                    <i class="fa fa-ban"></i> 解绑
                                                </button>
                                            @endif
                                        @else
                                            否
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>微信</td>
                                    <td>
                                        {{$user->weixin}}
                                        @if($user->weixin && \Illuminate\Support\Facades\Gate::check('user/unbindweixin'))
                                            <button type="button" style="margin-left: 15px" class="btn btn-warning btn-xs" onclick="unbindProfile('{{$user->id}}','unbindweixin','微信号')">
                                                <i class="fa fa-ban"></i> 解绑
                                            </button>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>QQ</td>
                                    <td>{{$user->qq}}
                                        @if($user->qq && \Illuminate\Support\Facades\Gate::check('user/unbindqq'))
                                            <button type="button" style="margin-left: 15px" class="btn btn-warning btn-xs" onclick="unbindProfile('{{$user->id}}','unbindqq','QQ号码')">
                                                <i class="fa fa-ban"></i> 解绑
                                            </button>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>注册时间</td>
                                    <td>{{$user->created_at}}</td>
                                    <td>最后登录时间</td>
                                    <td>{{$user->last_time}}</td>
                                </tr>

                                <tr>
                                    <td>注册 IP</td>
                                    <td>{{$user->created_ip}}</td>
                                    <td>最后登录 IP</td>
                                    <td>{{$user->last_ip}}</td>
                                </tr>
                                <tr>
                                    <td>是否冻结</td>
                                    <td>@if ($user->frozen==1) 完全冻结（冻结时间：{{$user->frozen_at}}）@elseif($user->frozen==2)
                                            可登录，不可投注，不可充提（冻结时间：{{$user->frozen_at}}）@elseif($user->frozen==3)
                                            可登录，不可投注，可充提（冻结时间：{{$user->frozen_at}}）@else 否 @endif</td>
                                    <td>冻结原因</td>
                                    <td>@if ($user->frozen>0){{$user->frozen_reason}}@endif</td>
                                </tr>
                                @if ($user->user_observe!='')
                                    <tr>
                                        <td>重点观察</td>
                                        <td>是</td>
                                        <td>观察原因</td>
                                        <td>{{$user->user_observe}}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>资金密码异动时间</td>
                                    <td>
                                        @if ($user->change_security_password)
                                            {{ $user->change_security_password }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>备注内容</td>
                                    <td colspan="3" class="text-danger">
                                        {{$user->remark}}
                                        @if(Gate::check('user/editremark'))
                                            <br><button onclick="editRemark({{ $user->id }})" class="btn btn-default">修改备注</button>
                                        @endif
                                    </td>
                                </tr>
                                <!--
                                <tr>
                                    <td>软删除时间</td>
                                    <td>{{$user->deleted_at}}</td>
                                    <td>代理树</td>
                                    <td>{{$user->parent_tree}}</td>
                                </tr>
                                -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title">彩票游戏信息</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover">

                                <tbody>
                                <tr>
                                    <td>累计投注次数</td>
                                    <td>{{$user->lotteryTotal->bet_times}}</td>
                                    <td>累计中奖次数</td>
                                    <td>{{$user->lotteryTotal->win_times}}</td>
                                </tr>
                                <tr>
                                    <td>累计投注金额</td>
                                    <td>{{isset($user->lotteryTotal->price)?$user->lotteryTotal->price:0}}</td>
                                    <td>累计中奖金额</td>
                                    <td>{{isset($user->lotteryTotal->bonus)?$user->lotteryTotal->bonus:0}}</td>
                                </tr>
                                <tr>
                                    <td>累计返点金额</td>
                                    <td>{{$user->lotteryTotal->rebate}}</td>
                                    <td></td>
                                    <td></td>
                                </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">财务信息</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover">

                                <tbody>
                                <tr>
                                    <td>余额</td>
                                    <td>{{$user->fund->balance}}</td>
                                    <td>冻结金额</td>
                                    <td>{{$user->fund->hold_balance}}</td>
                                </tr>
                                <tr>
                                    <td>团队余额</td>
                                    <td>{{$user->teambalance}}</td>
                                    <td>积分</td>
                                    <td>{{$user->fund->points}}</td>
                                </tr>
                                <tr>
                                    <td>累计充值次数</td>
                                    <td>{{$user->depositTotal->times}}</td>
                                    <td>累计提现次数</td>
                                    <td>{{$user->withdrawalTotal->times}}</td>
                                </tr>
                                <tr>
                                    <td>累计充值金额</td>
                                    <td>{{$user->depositTotal->amount}}</td>
                                    <td>累计提现金额</td>
                                    <td>{{$user->withdrawalTotal->amount}}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">冻结/解冻日志(最近25条)</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body" style="max-height: 360px;overflow-y: scroll">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <th>状态</th>
                                <th>管理员</th>
                                <th>原因</th>
                                <th>操作时间</th>
                                </thead>
                                <tbody>
                                @foreach($freeze_log as $log)
                                    <tr>
                                        <td>
                                            @if($log->freeze_type==0)
                                                <span class="label label-success">解冻</span>
                                            @elseif($log->freeze_type==1)
                                                <span class="label label-danger">完全冻结</span>
                                            @elseif($log->freeze_type==2)
                                                <span class="label label-warning">可登录，不可投注，不可充提，不可转账</span>
                                            @elseif($log->freeze_type==3)
                                                <span class="label label-warning">不可投注，可充提，可转账</span>
                                            @endif
                                        </td>
                                        <td>{{$log->admin}}</td>
                                        <td>{{$log->reason}}</td>
                                        <td>{{$log->created_at}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer text-center">
{{--                            <button type="button" class="btn btn-warning btn-md" onclick="history.back()">--}}
{{--                                <i class="fa fa-arrow-left"></i> 返回--}}
{{--                            </button>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        function unbindProfile(user_id, type,title) {
            BootstrapDialog.confirm({
                message: '确定要解绑'+title+'吗？',
                type: BootstrapDialog.TYPE_WARNING,
                closable: true,
                draggable: true,
                btnCancelLabel: '取消',
                btnOKLabel: '解绑',
                btnOKClass: 'btn-warning',
                callback: function(result) {
                    if(result) {
                        $.ajax({
                            url: "/user/"+type,
                            dataType: "json",
                            method: "POST",
                            data:{user_id:user_id}
                        }).done(function (json) {
                            if(json.status==0){
                                notify(json.msg,'success');
                                setTimeout(window.location.reload(),1000);
                            }else{
                                notify(json.msg,'error');
                            }

                        }).error(function (jqXHR, textStatus, errorThrown) {
                            alert(errorThrown.toString())
                        });
                    }
                }
            });
        }

        function editRemark(id) {
            BootstrapDialog.show({
                title:'修改备注内容',
                message: $('<div></div>').load("/user/editremark/?id=" + id),
                buttons: [{
                    icon: 'glyphicon glyphicon-send',
                    label: '保存',
                    cssClass: 'btn-primary',
                    //autospin: true,
                    action: function(dialogRef){
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        $.ajax({
                            url: "/user/editremark/?id=" + id,
                            dataType: "json",
                            method: "POST",
                            data:$("#remark-form").serialize(),
                        }).done(function (json) {
                            if(json.status == 0) {
                                BootstrapDialog.alert({
                                    title: '修改备注内容',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {
                                        document.location.reload();
                                    }
                                });
                            } else {
                                BootstrapDialog.alert({
                                    title: '修改备注内容',
                                    message: json.msg,
                                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                                    closable: true, // <-- Default value is false
                                    draggable: true, // <-- Default value is false
                                    buttonLabel: '关闭', // <-- Default value is 'OK',
                                    callback: function(result) {

                                    }
                                });
                                dialogRef.enableButtons(true);
                                dialogRef.setClosable(true);
                            }
                        });
                    }
                }, {
                    label: '取消',
                    action: function(dialogRef){
                        dialogRef.close();
                    }
                }]
            });
        }
    </script>
@stop