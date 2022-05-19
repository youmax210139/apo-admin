@extends('layouts.base')
@section('title','充提报表')

@section('function','充提报表')
@section('function_link', '#')

@section('here','首页')


@section('content')

    <section class="content">
        <div class="row">
            <div class="">
                <!--搜索框 Start-->
                <div class="box box-primary">
                    <form class="form-horizontal" id="search">
                        <div class="box-header with-border">
                            <h3 class="box-title"></h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="start_date" class="col-sm-2 control-label">时间</label>
                                    <div class="col-sm-10">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control form_datetime" name="start_date"
                                                   id="start_date" value="{{$start_date}}" placeholder="开始时间">
                                            <span class="input-group-addon">~</span>
                                            <input type="text" class="form-control form_datetime" name="end_date"
                                                   id="end_date" value="{{$end_date}}" placeholder="结束时间">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type" class="col-sm-3 control-label">组别</label>
                                    <div class="col-sm-9">
                                        <select name="user_group" class="form-control">
                                            <option value="">所有组别</option>
                                            <option value="1" @if($user_group==1) selected="selected" @endif>正式组</option>
                                            <option value="2" @if($user_group==2) selected="selected" @endif>测试组</option>
                                            <option value="3" @if($user_group==3) selected="selected" @endif>试玩组</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type" class="col-sm-3 control-label">冻结账号</label>
                                    <div class="col-sm-9">
                                        <select name="frozen" class="form-control">
                                            <option value="-1" @if($frozen==-1) selected="selected" @endif>所有类型</option>
                                            <option value="0" @if($frozen==0) selected="selected" @endif">非冻结账号</option>
                                            <option value="1" @if($frozen==1) selected="selected" @endif>冻结账号</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="btn-group col-md-6">
                                <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i
                                            class="fa fa-search" aria-hidden="true"></i>查询
                                </button>
                            </div>
                            <div class=" btn-group col-md-6">
                                <button type="reset" class="btn btn-default col-sm-2">重置</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="box box-info">
                <div class="box-body">
                    <table class="table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead class="bg-aqua-active">
                        <tr>
                            <th rowspan="2">用户名</th>
                            <th rowspan="2">所属组</th>
                            <th colspan="9">人工处理</th>
                            <th colspan="5">在线处理</th>
                            <th colspan="3">现金充提合计</th>

                        </tr>
                        <tr>
                            <th>充值</th>
                            <th>提现</th>
                            <th>现金充值</th>
                            <th>理赔充值</th>
                            <th>分红发放</th>
                            <th>佣金发放</th>
                            <th>私返发放</th>
                            <th>营利扣减</th>
                            <th>管理员扣减</th>
                            <th>在线充值</th>
                            <th>线下充值</th>
                            <th>商务提现</th>
                            <th>充值手续费</th>
                            <th>提现手续费</th>
                            <th>充值金额</th>
                            <th>提现金额</th>
                            <th>充提结余</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lists as $list)
                        <tr>
                            @if($list['user_observe'])
                                <td><span class="label-danger">{{$list['username']}}</span></td>
                            @else
                            <td>{{$list['username']}}</td>
                            @endif
                            <td>
                                @if($list['usertype']==1)
                                    <span class="label label-success">正式组</span>
                                    @elseif($list['usertype']==2)
                                    <span class="label label-warning">测试组</span>
                                    @else
                                    <span class="label label-danger">试玩组</span>
                                @endif
                            </td>
                            <td><span class="text-green">{{$list['hand_cash_in']}}</span></td>
                            <td><span class="text-red">{{$list['hand_cash_out']}}</span></td>
                            <td><span class="text-green">{{$list['email_hand_cash_in']}}</span></td>
                            <td><span class="text-green">{{$list['cash_lp_in']}}</span></td>
                            <td><span class="text-aqua">{{$list['cash_fhff_in']}}</span></td>
                            <td><span class="text-aqua">{{$list['cash_xtyjff_in']}}</span></td>
                            <td><span class="text-aqua">{{$list['cash_xtsfff_in']}}</span></td>
                            <td><span class="text-aqua">{{$list['cash_xtjjkk_out']}}</span></td>
                            <td><span class="text-red">{{$list['cash_lp_out']}}</span></td>
                            <td><span class="text-green">{{$list['cash_payment_in']-$list['cash_payment_offline_in']}}</span></td>
                            <td><span class="text-green">{{$list['cash_payment_offline_in']}}</span></td>
                            <td><span class="text-red">{{$list['cash_payment_out']}}</span></td>
                            <td><span class="text-green">{{$list['cash_payment_fee_in']}}</span></td>
                            <td><span class="text-green">{{$list['cash_payment_fee_out']}}</span></td>
                            <td><span class="text-green">{{$list['cash_in']}}</span></td>
                            <td><span class="text-red">{{$list['cash_out']}}</span></td>
                            <td><span class="@if($list['cash_diff'] > 0) text-green @else text-red @endif" >{{$list['cash_diff']}}</span></td>

                        </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="text-red">
                            <td colspan="2" >合计:</td>
                            <td class="text-green">{{$sum['sum_hand_cash_in']}}</td>
                            <td class="text-red">{{$sum['sum_hand_cash_out']}}</td>
                            <td class="text-green">{{$sum['sum_email_hand_cash_in']}}</td>
                            <td class="text-green">{{$sum['sum_lp_in']}}</td>
                            <td class="text-aqua">{{$sum['sum_fhff_in']}}</td>
                            <td class="text-aqua">{{$sum['sum_xtyjff_in']}}</td>
                            <td class="text-aqua">{{$sum['sum_xtsfff_in']}}</td>
                            <td class="text-aqua">{{$sum['sum_xtjjkk_out']}}</td>
                            <td class="text-red">{{$sum['sum_lp_out']}}</td>
                            <td class="text-green">{{$sum['sum_payment_in']}}</td>
                            <td class="text-red">{{$sum['sum_payment_offline_in']}}</td>
                            <td class="text-red">{{$sum['cash_payment_out']}}</td>
                            <td class="text-green">{{$sum['sum_payment_fee_in']}}</td>
                            <td class="text-red">{{$sum['sum_payment_fee_out']}}</td>
                            <td class="text-green">{{$sum['sum_cash_in']}}</td>
                            <td class="text-red">{{$sum['sum_cash_out']}}</td>
                            <td class="@if($sum['sum_cash_diff'] > 0) text-green @else text-red @endif" >{{$sum['sum_cash_diff']}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="box box-default collapsed-box">
                <div class="box-header with-border">
                    <div class="box-title text-muted  small">使用帮助/表格栏目说明</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <table  class="table table-striped">
                        <thead>
                            <tr>
                                <th width="200px">大栏</th>
                                <th width="200px">小栏</th>
                                <th>备注</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="9">人工处理</td>
                                <td>充值</td>
                                <td>帐变类型为<span class="text-orange">上级充值[SJCZ]</span>、<span class="text-orange">向上级转帐[XSJZZ]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td>提现</td>
                                <td>帐变类型为<span class="text-orange">跨级提现[KJTX]</span>、<span class="text-orange">跨级充值[KJCZ]</span>、<span class="text-orange">从下级转入[CXJZR]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td>现金充值</td>
                                <td>无支付渠道及通道的充值纪录</td>
                            </tr>
                            <tr>
                                <td>理赔充值</td>
                                <td>帐变类型为<span class="text-orange">理赔充值[LPCZ]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td>分红发放</td>
                                <td>帐变类型为<span class="text-orange">分红发放[FHFF]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td>佣金发放</td>
                                <td>帐变类型为<span class="text-orange">系统佣金发放[XTYJFF]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td>私返发放</td>
                                <td>帐变类型为<span class="text-orange">系统私返发放[XTSFFF]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td>营利扣减</td>
                                <td>帐变类型为<span class="text-orange">系统经营扣款[XTJYKK]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td>管理员扣减</td>
                                <td>帐变类型为<span class="text-orange">管理员扣减[GLYJK]</span>的帐变总和</td>
                            </tr>
                            <tr>
                                <td rowspan="5">在线处理</td>
                                <td>在线充值</td>
                                <td>通过支付渠道进入的金额扣减去线下充值</td>
                            </tr>
                            <tr>
                                <td>线下充值</td>
                                <td>
                                    在线充值中支付类型为 <span class="text-orange">手工转账[transfer]</span>、 <span class="text-orange">线下扫码[qrcode_offline]</span> 、<span class="text-orange">第三方线下转账[third_offline]</span> 、<span class="text-orange">代理QQ[agent_qq]</span> 、<span class="text-orange">代理微信[agent_weixin]</span> 、<span class="text-orange">代理支付宝[agent_alipay]</span> 、<span class="text-orange">会话充值[agent_chat]</span>的充值金额总和
                                </td>
                            </tr>
                            <tr>
                                <td>商务提现</td>
                                <td>提现出款内提现成功的金额总和</td>
                            </tr>
                            <tr>
                                <td>充值手续费</td>
                                <td>状态为成功的充值纪录中用户手续费总和</td>
                            </tr>
                            <tr>
                                <td>提现手续费</td>
                                <td>状态为成功的提款纪录中用户手续费总和</td>
                            </tr>
                            <tr>
                                <td rowspan="3">现金充提合计</td>
                                <td>充值金额</td>
                                <td>
                                    充值<span class="link-muted">[人工处理]</span> + 理赔充值<span class="link-muted">[人工处理]</span> + 现金充值<span class="link-muted">[人工处理]</span> + 在线充值<span class="link-muted">[在线处理]</span> + 线下充值<span class="link-muted">[在线处理]</span> - 充值手续费<span class="link-muted">[在线处理]</span>
                                </td>
                            </tr>
                            <tr>
                                <td>提现金额</td>
                                <td>
                                    提现<span class="link-muted">[人工处理]</span>+管理员扣减<span class="link-muted">[人工处理]</span>+商务提现<span class="link-muted">[在线处理]</span>+提现手续费<span class="link-muted">[在线处理]</span>
                                </td>
                            </tr>
                            <tr>
                                <td>充提结余</td>
                                <td>充值金额 - 提现金额</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);

        layConfig.elem = '#end_date';
        laydate(layConfig);
    </script>
@stop
