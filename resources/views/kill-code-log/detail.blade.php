<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        ×
    </button>
    <h4 class="modal-title">详情</h4>
</div>
<div class="modal-body">
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">请求日志详情[{{$id}}]</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <td width="15%">消息</td>
                                    <td colspan="5" width="85%">{!! $message !!}</td>
                                </tr>
                                <tr>
                                    <td>响应时间</td>
                                    <td>{{$created_at}}</td>
                                    <td>计算完成时间</td>
                                    <td>{{$calculated_at}}</td>
                                    <td>推送完成时间</td>
                                    <td>{{$posted_at}}</td>
                                </tr>
                                <tr>
                                    <td>第三方平台</td>
                                    <td>{{$third_ident}}</td>
                                    <td>第三方彩种</td>
                                    <td>{{$third_lottery}}</td>
                                    <td>第三方奖期</td>
                                    <td>{{$third_issue}}</td>
                                </tr>
                                <tr>
                                    <td>步骤 / 状态</td>
                                    <td>
                                        @if($step == 0) <span class="label label-warning">响应返回</span>
                                        @elseif($step == 1) <span class="label label-warning">本地适配</span>
                                        @elseif($step == 2) <span class="label label-warning">计算完成</span>
                                        @elseif($step == 3) <span class="label label-success">推送完成</span>
                                        @else <span class="label label-danger">其他{{$step}}</span>
                                        @endif

                                        @if($error == 0) <span class="label label-success">正常</span>
                                        @elseif($error == 1) <span class="label label-warning">本地无对应彩种</span>
                                        @elseif($error == 2) <span class="label label-warning">本地彩种不存在</span>
                                        @elseif($error == 3) <span class="label label-warning">无对应分隔符号</span>
                                        @elseif($error == 4) <span class="label label-warning">本地奖期不存在</span>
                                        @elseif($error == 5) <span class="label label-warning">推送发生异常</span>
                                        @elseif($error == 6) <span class="label label-danger">推送返回空值</span>
                                        @elseif($error == 7) <span class="label label-danger">推送返回解析失败</span>
                                        @elseif($error == 8) <span class="label label-danger">推送返回缺少顺利标识</span>
                                        @elseif($error == 9) <span class="label label-warning">重复发送</span>
                                        @else <span class="label label-danger">其他{{$error}}</span>
                                        @endif
                                    </td>
                                    <td>处理方式</td>
                                    <td>@if($mode == 1) <span class="label label-primary">CLI</span>
                                        @elseif($mode==0) <span class="label label-success">WEB</span>
                                        @else <span class="label label-danger">其他{{$mode}}</span>
                                        @endif
                                    </td>
                                    <td>请求批次</td>
                                    <td>
                                        {{$third_serial}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>请求IP</td>
                                    <td>{{$third_ip}}</td>
                                    <td>回调地址</td>
                                    <td colspan="3">{{$third_callback}}</td>
                                </tr>
                                <tr>
                                    <td>本地彩种</td>
                                    <td>{{$l_name}}[{{$local_lottery}}]</td>
                                    <td>本地奖期</td>
                                    <td>{{$local_issue}}</td>
                                    <td>销售截止</td>
                                    <td>{{$local_issue_sale_end}}</td>
                                </tr>
                                <tr>
                                    <td>开奖状态</td>
                                    <td>
                                        @if ($i_code_status == 0) <span class="label label-default">未写入</span>
                                        @elseif($i_code_status == 1) <span class="label label-info">写入待验证</span>
                                        @elseif($i_code_status == 2) <span class="label label-success">已验证</span>
                                        @elseif($i_code_status == 3) <span class="label label-warning">官方未开奖</span>
                                        @else <span class="label label-warning">其他{{$i_code_status}}</span>
                                        @endif
                                    </td>
                                    <td>开奖号码</td>
                                    <td>
                                        @if ($i_code_status == 2){{$i_code}}
                                        @else <span class="label label-default">待开</span>
                                        @endif
                                    </td>
                                    <td>汇总状态</td>
                                    <td>
                                        @if ($i_report_status == 0) <span class="label label-default">未开始</span>
                                        @elseif($i_report_status > 0 && $i_report_status < 5) <span
                                                class="label label-info">进行中</span>
                                        @elseif($i_report_status == 5) <span class="label label-success">已完成</span>
                                        @else <span class="label label-warning">其他{{$i_report_status}}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($i_report_status == 5)
                                    <tr>
                                        <td>注金</td>
                                        <td>{{$real_sell}}</td>
                                        <td>奖金</td>
                                        <td>{{$real_bonus}}</td>
                                        <td>盈亏</td>
                                        <td>{{$real_profit}}</td>
                                    </tr>
                                @endif

                                <!-- (start)如果是分分彩合并了5分，10分的在此处分别展示 -->
                                @if(!empty($need_merge_data))
                                    @foreach($need_merge_data as $data_value)
                                        <tr>
                                            <td>本地彩种</td>
                                            <td>{{$data_value['l_name']}}[{{$data_value['l_ident']}}]</td>
                                            <td>本地奖期</td>
                                            <td>{{$data_value['l_issue']}}</td>
                                            <td>销售截止</td>
                                            <td>{{$data_value['l_sale_end']}}</td>
                                        </tr>
                                        <tr>
                                            <td>开奖状态</td>
                                            <td>
                                                @if ($data_value['i_code_status'] == 0) <span class="label label-default">未写入</span>
                                                @elseif($data_value['i_code_status'] == 1) <span class="label label-info">写入待验证</span>
                                                @elseif($data_value['i_code_status'] == 2) <span class="label label-success">已验证</span>
                                                @elseif($data_value['i_code_status'] == 3) <span class="label label-warning">官方未开奖</span>
                                                @else <span class="label label-warning">其他{{$data_value['i_code_status']}}</span>
                                                @endif
                                            </td>
                                            <td>开奖号码</td>
                                            <td>
                                                @if ($data_value['i_code_status'] == 2){{$data_value['i_code']}}
                                                @else <span class="label label-default">待开</span>
                                                @endif
                                            </td>
                                            <td>汇总状态</td>
                                            <td>
                                                @if ($data_value['i_report_status'] == 0) <span class="label label-default">未开始</span>
                                                @elseif($data_value['i_report_status'] > 0 && $data_value['i_report_status'] < 5) <span
                                                    class="label label-info">进行中</span>
                                                @elseif($data_value['i_report_status'] == 5) <span class="label label-success">已完成</span>
                                                @else <span class="label label-warning">其他{{$data_value['i_report_status']}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($data_value['i_report_status'] == 5)
                                            <tr>
                                                <td>注金</td>
                                                <td>{{$data_value['real_sell']}}</td>
                                                <td>奖金</td>
                                                <td>{{$data_value['real_bonus']}}</td>
                                                <td>盈亏</td>
                                                <td>{{$data_value['real_profit']}}</td>
                                            </tr>
                                    @endif
                                    @endforeach
                                @endif
                                <!-- (end)如果是分分彩合并了5分，10分的在此处显示 -->

                                <!-- (start)如果有合并5f 1f彩加上汇总-->
                                @if(!empty($need_merge_data))
                                    <tr>
                                        <td>注金汇总</td>
                                        <td>{{$total_real_sell}}</td>
                                        <td>奖金汇总</td>
                                        <td>{{$total_real_bonus}}</td>
                                        <td>盈亏汇总</td>
                                        <td>{{$total_real_profit}}</td>
                                    </tr>
                                @endif
                                <!-- (end)如果有合并5f 1f彩加上汇总-->

                                <tr>
                                    <td>点杀开关</td>
                                    <td>
                                        @if ($flag_switch == 1) <span class="label label-primary">黑名单</span>
                                        @elseif($flag_switch == 2) <span class="label label-warning">次数不足</span>
                                        @elseif($flag_switch == 0) <span class="label label-success">一般</span>
                                        @else <span class="label label-warning">其他{{$i_report_status}}</span>
                                        @endif
                                    </td>
                                    <td>点杀名单</td>
                                    <td>{{$flag_users}}</td>
                                    <td>点杀注金</td>
                                    <td>{{$flag_bet_sum}} [{{$flag_bet_count}}笔]</td>
                                </tr>
                                <tr>
                                    <td>平台注金</td>
                                    <td>{{$all_bet_sum}} [{{$all_bet_count}}笔]</td>
                                    <td>对应盈亏</td>
                                    <td>@if ($i_report_status == 5) {{$plan_profit}} @endif</td>
                                    <td>盈亏偏差</td>
                                    <td>@if ($i_report_status == 5) {{$real_plan_profit_diff}} @endif</td>
                                </tr>
                                <tr>
                                    <td>杀号盈亏</td>
                                    <td colspan="5">
                                        <div style="max-height:200px; overflow-y: auto;word-wrap: break-word; width: 700px;"><?php echo "<pre>";print_r($code_map);echo "</pre>"; ?></div>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
