<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">中文名称</label>
    <div class="col-md-7">
        <input type="text" class="form-control" name="name" id="tag" value="{{ $name }}" maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">英文标识</label>
    <div class="col-md-7">
        <input type="text" class="form-control" name="ident" id="tag" value="{{ $ident }}" maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">官方网址</label>
    <div class="col-md-7">
        <input type="text" class="form-control" name="official_url" id="tag" value="{{ $official_url }}" maxlength="64" placeholder="http://www.xxx.com" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">所属彩种分类</label>
    <div class="col-md-7">
          <div class="radio">
          @foreach($lottery_category as $category)
            <label class="radio-inline"><input type="radio" name="lottery_category_id" value="{{ $category->id }}"@if ( $category->id == $lottery_category_id ) checked="checked"@endif />{{ $category->name }}</label>
          @endforeach
          </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">所属玩法种类</label>
    <div class="col-md-7">
          <select name="lottery_method_category_id" class="form-control" @if ( $lottery_method_category_id )disabled="disabled"@endif>
            <option value="">选择玩法分类</option>
             @foreach($lottery_method_category as $v)
            <option value="{{ $v->id }}"  @if ( $v->id == $lottery_method_category_id )selected="selected"@endif>{{ $v->name }}</option>
            @endforeach
        </select>
        @if ( $lottery_method_category_id )
            <input name="lottery_method_category_id" type="hidden" value="{{ $lottery_method_category_id }}">
        @endif
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">开奖周期</label>

    <div class="col-md-9">
        <div class="checkbox">
<label for="date_1" class="checkbox-inline"><input type="checkbox" name="week_cycle[]" id="date_1" value="1" @if ($week_cycle&1)checked @endif>星期一</label>
<label for="date_2" class="checkbox-inline"><input type="checkbox" name="week_cycle[]" id="date_2" value="2" @if ($week_cycle&2)checked @endif>星期二</label>
<label for="date_3" class="checkbox-inline"><input type="checkbox" name="week_cycle[]" id="date_3" value="4" @if ($week_cycle&4)checked @endif>星期三</label>
<label for="date_4" class="checkbox-inline"><input type="checkbox" name="week_cycle[]" id="date_4" value="8" @if ($week_cycle&8)checked @endif>星期四</label>
<label for="date_5" class="checkbox-inline"><input type="checkbox" name="week_cycle[]" id="date_5" value="16" @if ($week_cycle&16)checked @endif>星期五</label>
<label for="date_6" class="checkbox-inline"><input type="checkbox" name="week_cycle[]" id="date_6" value="32" @if ($week_cycle&32)checked @endif>星期六</label>
<label for="date_7" class="checkbox-inline"><input type="checkbox" name="week_cycle[]" id="date_7" value="64" @if ($week_cycle&64)checked @endif>星期日</label>
    </div>
  </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">号码形态</label>
    <div class="col-md-7">
        <p>
            号码总长度: <input type="text" class="form-control input_inline w_9pct" name="number_rule[len]" id="norule_len" maxlength="20"  value="{{ $number_rule['len'] }}" onBlur="this.value=this.value.replace(/[^\d]/g,'');" onkeyup="this.value=this.value.replace(/[^\d]/g,'');">
            <span id="no_rule">
                @if ($lottery_category_id)
                    @if ($lottery_category_id==1 || $lottery_category_id==3 || $lottery_category_id==4 ||$lottery_category_id==5)
                        起始号码:<input type='text' class="form-control input_inline w_9pct" name='number_rule[start_number]' value="@if (isset($number_rule['start_number'])){{$number_rule['start_number']}} @endif" />
                        结束号码:<input type='text' class="form-control input_inline w_9pct" name='number_rule[end_number]' value="@if (isset($number_rule['end_number'])){{$number_rule['end_number']}} @endif"/>
                    @elseif ($lottery_category_id==2)
                        <p>普通起始号码:<input type='text' class="form-control input_inline w_9pct" name='number_rule[start_number]' value="@if (isset($number_rule['start_number'])){{$number_rule['start_number']}} @endif" />,
                        结束号码:<input type='text' class="form-control input_inline w_9pct" name='number_rule[end_number]' value="@if (isset($number_rule['end_number'])){{$number_rule['end_number']}} @endif"/>,
                        选出<input type='text' class="form-control input_inline w_9pct" name='number_rule[startrepeat]' value="@if (isset($number_rule['startrepeat'])){{$number_rule['startrepeat']}} @endif" onBlur="this.value=this.value.replace(/[^\d]/g,'');" onkeyup="this.value=this.value.replace(/[^\d]/g,'');" />次
                        </p>
                        <p>特别起始号码:<input type='text' class="form-control input_inline w_9pct" name='number_rule[spstart_number]'  value="@if (isset($number_rule['spstart_number'])){{$number_rule['spstart_number']}} @endif">,
                        结束号码:<input type='text' class="form-control input_inline w_9pct" name='number_rule[spend_number]'  value="@if (isset($number_rule['spend_number'])){{$number_rule['spend_number']}} @endif"/>,
                        选出<input type='text' class="form-control input_inline w_9pct" name='number_rule[sprepeat]'  value="@if (isset($number_rule['sprepeat'])){{$number_rule['sprepeat']}} @endif" onBlur="this.value=this.value.replace(/[^\d]/g,'');" onkeyup="this.value=this.value.replace(/[^\d]/g,'');" />次
                        </p>
                    @endif
                @endif

            </span>
        </p>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">奖期格式</label>
    <div class="col-md-7">
<input type="text" class="form-control" name="issue_rule[rule]" id="issue_rule" value="@if (isset($issue_rule['rule'])){{$issue_rule['rule']}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">清零规则</label>
    <div class="col-md-7">
        年:<select name="issue_rule[year]" class="form-control input_inline" style="width:14.7%;">
            <option value="0" @if (isset($issue_rule['year']) && $issue_rule['year']==0)selected @endif>清零</option>
            <option value="1" @if (isset($issue_rule['year']) && $issue_rule['year']==1)selected @endif>不清零</option>
        </select>
        月:<select name="issue_rule[month]" class="form-control input_inline"  style="width:14.7%;">
            <option value="0" @if (isset($issue_rule['month']) && $issue_rule['month']==0)selected @endif>清零</option>
            <option value="1" @if (isset($issue_rule['month']) && $issue_rule['month']==1)selected @endif>不清零</option>
        </select>
        日:<select name="issue_rule[day]" class="form-control input_inline"  style="width:14.7%;">
            <option value="0"@if (isset($issue_rule['day']) && $issue_rule['day']==0)selected @endif>清零</option>
            <option value="1" @if (isset($issue_rule['day']) && $issue_rule['day']==1)selected @endif>不清零</option>
        </select>
    </div>
</div>
<div class="form-group">
    <label for="special" class="col-md-3 control-label checkbox-inline text-bold">禁止投注用户组</label>
    <div class="col-md-7">
        <div class="checkbox">
            <label class="checkbox-inline"><input type="checkbox" name="deny_user_group[]" value="1" @if ( in_array(1,$deny_user_group) ) checked="checked"@endif />正式组</label>
            <label class="checkbox-inline"><input type="checkbox" name="deny_user_group[]" value="2" @if ( in_array(2,$deny_user_group) ) checked="checked"@endif />测试组</label>
            <label class="checkbox-inline"><input type="checkbox" name="deny_user_group[]" value="3" @if ( in_array(3,$deny_user_group) ) checked="checked"@endif />试玩组</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="special" class="col-md-3 control-label checkbox-inline text-bold">特殊类型</label>
    <div class="col-md-7">
        <div class="radio">
            <label class="radio-inline"><input type="radio" name="special" value="0" @if ( $special == 0 ) checked="checked"@endif />官方彩种</label>
            <label class="radio-inline"><input type="radio" name="special" value="1" @if ( $special == 1 ) checked="checked"@endif />自开彩种</label>
            <label class="radio-inline"><input type="radio" name="special" value="2" @if ( $special == 2 ) checked="checked"@endif />自开秒秒彩</label>
        </div>
    </div>
</div>

<div class="form-group special_1" style="@if($special==0) display: none;@endif">
    <label for="special" class="col-md-3 control-label checkbox-inline text-bold">是否开启提前录入号</label>
    <div class="col-md-7">
        <div class="radio">
            <label class="radio-inline"><input type="radio" name="special_config[hand_coding]" value="0" @if ( !isset($special_config->hand_coding) || $special_config->hand_coding == 0 ) checked="checked"@endif />否</label>
            <label class="radio-inline"><input type="radio" name="special_config[hand_coding]" value="1" @if ( isset($special_config->hand_coding) && $special_config->hand_coding == 1 ) checked="checked"@endif />是</label>

        </div>
    </div>
</div>
<div class="form-group special_1 special" style="@if($special==0) display: none;@endif">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">做号概率</label>
    <div class="col-md-7">
        <input type="text" class="form-control" style="width: 80px" name="special_config[probability]" value="{{ $special_config->probability??10 }}" maxlength="20" />
    </div>
</div>
<div class="form-group special" style="@if($special==0) display: none;@endif">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">重复开号次数</label>
    <div class="col-md-7">
        <input type="text" style="width: 80px" class="form-control" name="special_config[times]"  value="{{ $special_config->times??5 }}" maxlength="20" />
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">抓号次数</label>
    <div class="col-md-7">
        <input type="text" style="width: 80px" class="form-control" name="special_config[max_time]"  value="{{ $special_config->max_time??5 }}" maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">重复抓号间隔</label>
    <div class="col-md-7">
        <input type="text" style="width: 80px" class="form-control" name="special_config[sleep_seconds]"  value="{{ $special_config->sleep_seconds??5 }}" maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">前台请求间隔</label>
    <div class="col-md-7">
        <input type="text" style="width: 80px" class="form-control" name="special_config[request_time]"  value="{{ $special_config->request_time??10 }}" maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">标记</label>
    <div class="col-md-7">
        <div class="radio">
            @php
                $special_config->flag = $special_config->flag??0;
            @endphp
            <label class="radio-inline"><input type="radio" name="special_config[flag]" value="0" @if ( $special_config->flag == 0 ) checked="checked"@endif />无</label>
            <label class="radio-inline"><input type="radio" name="special_config[flag]" value="1" @if ( $special_config->flag == 1 ) checked="checked"@endif />热门</label>
            <label class="radio-inline"><input type="radio" name="special_config[flag]" value="2" @if ( $special_config->flag == 2 ) checked="checked"@endif />新开</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">默认玩法ID</label>
    <div class="col-md-7">
        <input type="text" style="width: 180px" class="form-control" name="special_config[default_method]"  value="{{ $special_config->default_method ?? '' }}" maxlength="40" />
    </div>
</div>

{{--<div class="form-group">--}}
    {{--<label for="special_config" class="col-md-3 control-label checkbox-inline text-bold">特殊配置</label>--}}
    {{--<div class="col-md-7">--}}
        {{--<textarea class="form-control" placeholder="包括抓号次数，抓号等待时间，自主彩种策略配置等json {key:value}" name="special_config" id="special_config">{{ json_encode(json_decode($special_config,true),JSON_PRETTY_PRINT)}}</textarea>--}}
    {{--</div>--}}
{{--</div>--}}
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">公司最小留水</label>
    <div class="col-md-7">
        <input type="text" class="form-control" name="min_profit" id="tag" value="{{ $min_profit }}" size='4' maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">上下级最小点差</label>
    <div class="col-md-7">
        <input type="text" class="form-control" name="min_spread" id="tag" value="{{ $min_spread }}" maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">休市时间</label>
    <div class="col-md-7">
        <div class="input-daterange input-group">
            <input type="text" class="form-control form_datetime" name="closed_time_start" value="{{ $closed_time_start ? $closed_time_start : '2017-08-08 00:00:00' }}" id="start_date" placeholder="开始时间">
            <span class="input-group-addon">~</span>
            <input type="text" class="form-control form_datetime" name="closed_time_end" value="{{ $closed_time_end ? $closed_time_end : '2017-08-09 00:00:00' }}" id="end_date" placeholder="结束时间">
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">计划任务</label>
    <div class="col-md-7">
        <div class="input-daterange input-group">
            <table cellspacing="3">
                <tr><th>分钟</th><th>小时</th><th></th></tr>
                <tr>
                    <td><input type="text" class="form-control" name="cron_minute" value="{{ $cron[0] }}" maxlength="29"></td>
                    <td><input type="text" class="form-control" name="cron_hour" value="{{ $cron[1] }}" maxlength="29"></td>
                    <td> <span style="color: grey;">Linux计划任务格式：*表示每分钟、每小时。10-23表示时间段，多个时间段用英文逗号,分隔</span></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">奖期规则<br/> <a id='add_issue_set' href='javascript:;'>增加时段</a></label>
    <div class="col-md-9" id="issue_set">
        @foreach ( $issue_set as $key => $issue )
        <table>
            <tr><td class="issue_settitle" style="color:green"><p><b>第{{$china_num[$key]}}段销售时间设置<span class="delissue"><font color="red">[删除]</font></span></b></p></td></tr>
                <tr><td><p>
                        有效状态:<select name="issue_set[{{$key}}][status]" class="form-control input_inline status" style="width:80px;"><option value="0">无效</option><option value="1" @if($issue['status']==1) selected @endif>有效</option></select>&nbsp;&nbsp;&nbsp;
                        序号:<input size="3" class="form-control input_inline sort" style="width:62px;" type="text" name="issue_set[{{$key}}][sort]" value="{{ $issue['sort'] }}" onkeyup="limitnumerinput(this)" onafterpaste="limitnumerinput(this)">&nbsp;&nbsp;&nbsp;
                        销售开始时间:
                        <select name="issue_set[{{$key}}][start_hour]" class="form-control input_inline" style="width:62px;">
                            @foreach ($hours as $i )
                            <option value="{{$i}}" @if($issue['start_hour']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        ：<select name="issue_set[{{$key}}][start_minute]" class="form-control input_inline" style="width:62px;">
                             @foreach ($times as $i )
                            <option value="{{$i}}" @if($issue['start_minute']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        ：<select name="issue_set[{{$key}}][start_second]" class="form-control input_inline" style="width:62px;">
                            @foreach ($times as $i )
                            <option value="{{$i}}" @if($issue['start_second']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                            &nbsp;&nbsp;第一期开始是否昨天时间<select name="issue_set[{{$key}}][first_start_yesterday]" class="form-control input_inline" style="width:62px;">
                                    <option value="0" @if(isset($issue['first_start_yesterday']) && $issue['first_start_yesterday']==0) selected @endif>否</option>
                                    <option value="1" @if(isset($issue['first_start_yesterday']) && $issue['first_start_yesterday']==1) selected @endif>是</option>
                            </select></p><p>
                        官方第一期销售截止时间:
                        <select name="issue_set[{{$key}}][first_end_hour]" class="form-control input_inline" style="width:62px;">
                            @foreach ($hours as $i )
                            <option value="{{$i}}" @if($issue['first_end_hour']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        ：<select name="issue_set[{{$key}}][first_end_minute]" class="form-control input_inline" style="width:62px;">
                            @foreach ($times as $i )
                            <option value="{{$i}}" @if($issue['first_end_minute']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        ：<select name="issue_set[{{$key}}][first_end_second]" class="form-control input_inline" style="width:62px;">
                             @foreach ($times as $i )
                            <option value="{{$i}}" @if($issue['first_end_second']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        &nbsp;&nbsp;官方销售结束时间:
                        <select name="issue_set[{{$key}}][end_hour]" class="form-control input_inline" style="width:62px;">
                             @foreach ($hours as $i )
                            <option value="{{$i}}" @if($issue['end_hour']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        ：<select name="issue_set[{{$key}}][end_minute]" class="form-control input_inline" style="width:62px;">
                             @foreach ($times as $i )
                            <option value="{{$i}}" @if($issue['end_minute']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        ：<select name="issue_set[{{$key}}][end_second]" class="form-control input_inline" style="width:62px;">
                             @foreach ($times as $i )
                            <option value="{{$i}}" @if($issue['end_second']==$i) selected @endif>{{$i}}</option>
                            @endforeach
                        </select>
                        </p><p>
                        销售周期:<input class="form-control input_inline" style="width:62px;" value="@if (isset($issue['cycle'])) {{$issue['cycle']}}  @else 600 @endif" type="text" class="cycle" name="issue_set[{{$key}}][cycle]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;秒,
                        等待开奖时间:<input class="form-control input_inline end_sale" style="width:62px;" value="{{$issue['end_sale']}}" type="text" name="issue_set[{{$key}}][end_sale]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;秒,
                        &nbsp;&nbsp;撤单时间:<input value="{{$issue['drop_time']}}" class="form-control input_inline drop_time" style="width:62px;" type="text" name="issue_set[{{$key}}][drop_time]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;秒,
                        &nbsp;&nbsp;号码录入时间:<input value="{{$issue['input_code_time']}}" class="form-control input_inline input_code_time" style="width:62px;" type="text" name="issue_set[{{$key}}][input_code_time]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;秒</p>
                    </td></tr>
            </table>
            @endforeach
    </div>
</div>
@section('js')
<script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
<script>

$(function(){
    laydate.skin('lynn');
    var layConfig ={
        elem: '#start_date',
        event: 'focus',
        format: 'YYYY-MM-DD hh:mm:ss',
        istime: true,
        istoday: true,
        zindex:2
    };
    laydate(layConfig);

    layConfig.elem = '#end_date';
    laydate(layConfig);

    $("input:radio[name='category_id']").click(function(){
        var i = $(":radio:checked[name='category_id']").val();
        if(i==1 || i== 3 || i==4 )
        {
            $("#no_rule").html("起始号码:<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[start_number]' id='norule_start_number' />,"
            +"结束号码:<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[end_number]' id='norule_end_number'/>");
        }
        else if(i==2)
        {
            $("#no_rule").html("起始号码:<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[start_number]' id='norule_start_number' />,"
            +"结束号码:<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[end_number]' id='norule_end_number' />,"
            +"先重复<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[startrepeat]' id='norule_startrepeat'"
            +"onBlur=this.value=this.value.replace(/[^\\d]/g,\'\') onkeyup=this.value=this.value.replace(/[^\\d]/g,\'\') />次,"
            +"</p><p>然后从剩下号码中选出作为<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[splen]' id='norule_splen'"
            +"onBlur=this.value=this.value.replace(/[^\\d]/g,\'\') onkeyup=this.value=this.value.replace(/[^\\d]/g,\'\') />位作为特别号码");
        }
        else if(i==5)
        {
            $("#no_rule").html("普通起始号码:<input type='text' class='form-control input_inline' style='width:62px;' name='norule[start_number]' id='norule_start_number' />,"
            +"结束号码:<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[end_number]' id='norule_end_number'/>,"
            +"选出<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[startrepeat]' id='norule_startrepeat'"
            +"onBlur=this.value=this.value.replace(/[^\\d]/g,\'\') onkeyup=this.value=this.value.replace(/[^\\d]/g,\'\') />次,"
            +"<br/>特别起始号码:<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[spstart_number]' id='norule_spstart_number'/>,"
            +"结束号码:<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[spend_number]' id='norule_spend_number'/>,"
            +"选出<input type='text' class='form-control input_inline' style='width:62px;' name='number_rule[sprepeat]' id='norule_sprepeat'"
            +"onBlur=this.value=this.value.replace(/[^\\d]/g,\'\') onkeyup=this.value=this.value.replace(/[^\\d]/g,\'\') />次,");
        }
    });
    $("#add_issue_set").click(function(){
        var china_num = Array('一','二','三','四','五','六','七','八','九','十');
        var issue_set_num = $(".issue_settitle").size();
        var shtml = '<table style="font-size:13px"><tr><td class="issue_settitle"  style="color:green"><p><b>第'+china_num[issue_set_num];
        shtml += '段销售时间设置 <span class="delissue"><font color="red">[删除]</font></span></b></p></td></tr>';
        shtml += '<tr><td><p>';
        shtml += '有效状态:<select name="issue_set['+issue_set_num+'][status]" class="form-control input_inline status" style="width:80px;"><option value="0">无效</option><option value="1" selected>有效</option></select>&nbsp;&nbsp;&nbsp;';
        shtml += '序号:<input value="'+issue_set_num+'" class="form-control input_inline sort" style="width:62px;" type="text" name="issue_set['+issue_set_num+'][sort]" onkeyup="limitnumerinput(this)" onafterpaste="limitnumerinput(this)">&nbsp;&nbsp;&nbsp;';
        shtml += '销售开始时间:&nbsp';
        shtml += '<select name="issue_set['+issue_set_num+'][start_hour]" class="form-control input_inline" style="width:62px;">@foreach ($hours as $hour)<option value="{{$hour}}">{{$hour}}</option>@endforeach</select>';
        shtml += '：<select name="issue_set['+issue_set_num+'][start_minute]" class="form-control input_inline"  style="width:62px;">@foreach ($times as $i)<option value="{{$i}}">{{$i}}</option>@endforeach</select>';
        shtml += '：<select name="issue_set['+issue_set_num+'][start_second]" class="form-control input_inline"  style="width:62px;">@foreach ($times as $i)<option value="{{$i}}">{{$i}}</option>@endforeach</select>';
        shtml += '&nbsp;&nbsp;第一期开始是否昨天时间<select name="issue_set['+issue_set_num+'][first_start_yesterday]" class="form-control input_inline" style="width:62px;"><option value="0">否</option><option value="1">是</option></select></p><p>';
        shtml += '官方第一期销售截止时间:';
        shtml += '<select name="issue_set['+issue_set_num+'][first_end_hour]" class="form-control input_inline"  style="width:62px;">@foreach ($hours as $hour)<option value="{{$hour}}">{{$hour}}</option>@endforeach</select>';
        shtml += '：<select name="issue_set['+issue_set_num+'][first_end_minute]" class="form-control input_inline"  style="width:62px;">@foreach ($times as $i)<option value="{{$i}}">{{$i}}</option>@endforeach</select>';
        shtml += '：<select name="issue_set['+issue_set_num+'][first_end_second]" class="form-control input_inline"  style="width:62px;">@foreach ($times as $i)<option value="{{$i}}">{{$i}}</option>@endforeach</select>';
        shtml += '&nbsp;&nbsp;官方销售结束时间:';
        shtml += '<select name="issue_set['+issue_set_num+'][end_hour]" class="form-control input_inline"  style="width:62px;">@foreach ($hours as $hour)<option value="{{$hour}}">{{$hour}}</option>@endforeach</select>';
        shtml += '：<select name="issue_set['+issue_set_num+'][end_minute]" class="form-control input_inline"  style="width:62px;">@foreach ($times as $i)<option value="{{$i}}">{{$i}}</option>@endforeach</select>';
        shtml += '：<select name="issue_set['+issue_set_num+'][end_second]" class="form-control input_inline"  style="width:62px;">@foreach ($times as $i)<option value="{{$i}}">{{$i}}</option>@endforeach</select>';
        shtml += '</p><p>';
        shtml += '销售周期:<input value="600" class="form-control input_inline cycle" style="width:62px;" type="text" name="issue_set['+issue_set_num+'][cycle]" value=""  onkeyup="limitnumerinput(this)" onafterpaste="limitnumerinput(this)">&nbsp;秒, ';
        shtml += "&nbsp;&nbsp;等待开奖时间:<input value='90' class='form-control input_inline end_sale' style='width:62px;' type='text' name='issue_set["+issue_set_num+"][end_sale]' value='' onkeyup='limitnumerinput(this)' onafterpaste='limitnumerinput(this)'>&nbsp;秒, ";
        shtml += "&nbsp;&nbsp;撤单时间:<input value='90' class='form-control input_inline drop_time' style='width:62px;' type='text'  name='issue_set["+issue_set_num+"][drop_time]' value='' onkeyup='limitnumerinput(this)' onafterpaste='limitnumerinput(this)'>&nbsp;秒, ";
        shtml += "&nbsp;&nbsp;号码录入时间:<input value='120' class='form-control input_inline input_code_time' style='width:62px;' type='text' name='issue_set["+issue_set_num+"][input_code_time]' value='' onkeyup='limitnumerinput(this)' onafterpaste='limitnumerinput(this)'>&nbsp;秒";
        shtml += '</p></td></tr></table>';
        $("#issue_set").append(shtml);
     });
     
     $(document).on('click','.delissue',function(){
        $(this).parent().parent().parent().parent().parent().parent().remove();
     });
        
    if( $("#issue_set").html().trim() == '' ){
        $("#add_issue_set").click();
    }
    $("input[name='special']").click(function(){
        if($(this).val()==0){
            $(".special_1").hide();
            $(".special").hide();
        }
        if($(this).val()==1){
            $(".special_1").show();
            $(".special").show();
        }
        if($(this).val()==2){
            $(".special_1").hide();
            $(".special").show();
        }
    });

});
</script>
@stop
