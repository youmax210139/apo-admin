<table class="table table-bordered table-condensed" style="margin-bottom: 0">
    <tbody>
        <tr>
            <th scope="row" class="text-right">会员:</th>
            <td><span id="username">{{$username}}</span></td>
            <th scope="row" class="text-right">总代:</th>
            <td><span id="topuser">{{$topusername}}</span></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">申请时间:</th>
            <td><span id="bank_name">{{$created_at}}</span></td>
            <th scope="row" class="text-right">申请 IP:</th>
            <td><span id="created_at">{{$ip}}</span></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">开户银行:</th>
            <td><span id="bank_name">{{$bank_name}}</span></td>
            <th scope="row" class="text-right">所在地:</th>
            <td><span id="created_at">{{$province}} / {{$city}}</span></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">开户名:</th>
            <td colspan="2"><span id="account_name">{{$account_name}}</span></td>
            <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#account_name" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">银行卡号:</th>
            <td colspan="2"><span id="account_no">{{$account}}</span></td>
            <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#account_no" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">结算金额:</th>
            <td colspan="2"><code id="calculate_amount">{{ round($amount,2) + ($user_fee<0?$user_fee:0)  }}</code></td>
            <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#calculate_amount" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">提款金额:</th>
            <td colspan="2"><code id="amount">{{round($amount,2)}}</code></td>
            <td>
                @if(get_config('copy_withdrawal_button', 0))
                    <button type="button" data-clipboard-action="copy" data-clipboard-target="#amount" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button>
                @endif
            </td>
        </tr>
        <tr id="usdt_exchange" style="display:none" >
            <th scope="row" class="text-right">汇率计算:</th>
            <td colspan="2"><code id="bank_virtual_rate">汇率:1CNY:{{round(1/$rate,6)}} USDT 计算完金额:  {{round($amount/$rate,4)}}</code></td>
            <td>
            </td>
        </tr>       
        <tr>
            <th scope="row" class="text-right">用户备注:</th>
            <td><span class="text-danger">{{$user_remark}}</span></td>
            <th scope="row" class="text-right">重点观察备注:</th>
            <td><span class="text-danger">{{$user_observe}}</span></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">本次属当天({{$this_withdrawal_date}})次数:</th>
            <td><span id="this_withdrawal_times">{{$this_withdrawal_times}}</span></td>
            <th scope="row" class="text-right">客户端IP:</th>
            <td><span id="ip">{{$ip}}</span></td>
        </tr>
        <tr class="deal_action">
            <th scope="row" class="text-right">用户手续费:</th>
            <td>
                <input type="text" autocomplete="off" class="form-control" id="user_fee" name="user_fee" value="{{$user_fee?abs($user_fee):'0.00'}}">
                <p class="help-block" style="vertical-align: middle;margin-bottom: 5px;">
                    <label><input type="radio" value="1" name="user_fee_option" style="margin-left: 5px;" @if($user_fee<0||$user_fee==0) checked @endif> 扣除</label>
                    <label><input type="radio" value="0" name="user_fee_option" style="margin-left: 15px;" @if($user_fee>0) checked @endif > 返还</label>
                </p>
            </td>
            <th scope="row" class="text-right">平台手续费:</th>
            <td>
                <input type="text" autocomplete="off" class="form-control" id="platform_fee" name="platform_fee" value="0.00">
            </td>
        </tr>
        <tr class="deal_action">
            <th scope="row" class="text-right">请选择银行:</th>
            <td colspan="3">
                <select name="bank_id" class="form-control">
                    <option value="">请选择银行</option>
                    @foreach($banks as $b)
                    <option bata="{{$b['ident']}}" value="{{$b['id']}}">{{$b['name']}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr class="deal_action">
            <th scope="row" class="text-right">外部流水号:</th>
            <td colspan="3">
                <input type="text" maxlength="64" class="form-control" name="bank_order_no" value="" placeholder="请输入银行转帐流水号(不能超过64个字符)">
            </td>
        </tr>
        <tr class="deal_action">
            <th scope="row" class="text-right">操作备注:</th>
            <td colspan="3">
                <textarea class="form-control" placeholder="操作备注" name="remark"></textarea>
            </td>
        </tr>
        @if(in_array($status,array(12,14)))
            <tr>
                <th scope="row" class="text-right">第三出款接口反馈:</th>
                <td colspan="3"><textarea class="form-control" readonly="" rows="6" style="font-size: 12px">{{$third_reponse}}</textarea></td>
            </tr>
        @endif
        <input type="hidden" name="withdrawalid" value="{{$id}}">
    </tbody>
</table>

<script >
    $(function(){
        $('#user_fee,#platform_fee').keyup(function( event ){
            change_calculate_amount();
        });
        $("input[name=user_fee_option]").change(function(){
            change_calculate_amount();
        });
        $("select[name=bank_id]").blur(function( ){
            var bank_ident = $("select[name=bank_id]  option:selected").attr('bata');
            if(bank_ident == "{{$bv_ident}}"){
                $("textarea[name=remark]").val($('#bank_virtual_rate').text());
                $('#usdt_exchange').show();
            }else{
                $('#usdt_exchange').hide();
            }
       
        });
        function change_calculate_amount(){
            var user_fee = parseFloat($('#user_fee').val());

            var option = $("input[name=user_fee_option]:checked").val();

            if( option == 1){
                $('#calculate_amount').text( {{round($amount,2)}} - user_fee );
            }else{
                $('#calculate_amount').text( {{round($amount,2)}} );
            }
        }
    });
</script>