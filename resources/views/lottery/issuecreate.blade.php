@extends('layouts.base')

@section('title','奖期管理')

@section('function','奖期管理')
@section('function_link', '/issue/list?'.$lottery->id)

@section('here','添加奖期')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">添加奖期</h3>
                        </div>
                        <div class="panel-body">

                            @include('partials.errors')
                            @include('partials.success')

                            <form class="form-horizontal" id="issue_form" onsubmit="return submitForm();" role="form" method="POST" action="/lottery/issuecreate">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="lottery_id" value="{{ $lottery->id }}">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">彩种</label>
                                    <div class="col-md-7 form-control-static">
                                    {{ $lottery->name }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">英文标识</label>
                                    <div class="col-md-7 form-control-static text-bold">
                                        {{ $lottery->ident }}
                                    </div>
                                </div>

                                @foreach ( $lottery->issue_set as $key => $set )
                                    @if ( $set['status'] == 1 )
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">分段销售时间_{{ $set['sort'] }}</label>
                                        <div class="col-md-7 form-control-static">
                                              开始时间：{{ $set['start_hour'] }} : {{ $set['start_minute'] }} : {{ $set['start_second'] }}
                                        <br />结束时间：{{ $set['end_hour'] }} : {{ $set['end_minute'] }} : {{ $set['end_second'] }}
                                        <br />游戏周期：{{ $set['cycle'] }}秒
                                        <br />停售时间：{{ $set['end_sale'] }}秒
                                        <br />开奖号码录入时间：{{ $set['input_code_time'] }}秒
                                        <br />撤单时间：{{ $set['drop_time'] }}秒
                                        </div>
                                    </div>
                                    @endif
                               @endforeach

                               <div class="form-group">
                                    <label class="col-md-2 control-label">休市日期</label>
                                    <div class="col-md-7 form-control-static">
                                    {{ $lottery->closed_time_start }}  -  {{ $lottery->closed_time_end }}
                                    </div>
                                </div>

                               <div class="form-group">
                                    <label class="col-md-2 control-label">开奖周期</label>
                                    <div class="col-md-7 form-control-static">
                                    @if ($lottery->week_cycle&1) 星期一&nbsp;&nbsp;&nbsp; @endif
                                    @if ($lottery->week_cycle&2) 星期二&nbsp;&nbsp;&nbsp; @endif
                                    @if ($lottery->week_cycle&4) 星期三&nbsp;&nbsp;&nbsp; @endif
                                    @if ($lottery->week_cycle&8) 星期四&nbsp;&nbsp;&nbsp; @endif
                                    @if ($lottery->week_cycle&16) 星期五&nbsp;&nbsp;&nbsp; @endif
                                    @if ($lottery->week_cycle&32) 星期六&nbsp;&nbsp;&nbsp; @endif
                                    @if ($lottery->week_cycle&64) 星期日&nbsp;&nbsp;&nbsp; @endif
                                    </div>
                                </div>





                                    @if (isset($step) && $step=="confirm")
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">信息确认</label>
                                            <div class="col-md-7 form-control-static">

                                                @if ($intersect_dates['startday'])
                                                <font color=#669900>目前已生成从 {{ $intersect_dates['startday'] }} 到 {{ $intersect_dates['endday']
                                                }} 的奖期。<br/>
                                                @endif

                                                即将生成从 {{ $date1 }} 到 {{ $date2 }} 之间的奖期 <font color=red>(其中不包括{{ $date2 }}当天)</font></font>
                                                <input type="hidden" name="start_date" value="{{ $date1 }}" /><input type="hidden" name="end_date" value="{{ $date2 }}" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">奖期生成开始期号</label>
                                            <div class="col-md-7 form-control-static">
                                                @if ($first_date)
                                                    第&nbsp;{{ $first_date }} &nbsp;期
                                                @else
                                                    未指定
                                                @endif
                                                <input type="hidden" name="first_date"  size="11" value="{{ $first_date }}">
                                            </div>
                                        </div>

                                        @if ($intersect_dates['intersect_startday'])
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">提示</label>
                                            <div class="col-md-7 form-control-static">
                                                从 {{ $intersect_dates['intersect_startday'] }} 到 {{ $intersect_dates['intersect_endday'] }} 的奖期已存在，并将重新生成！
                                            </div>
                                        </div>
                                       @endif

                                        @if (isset($collectissues) && $collectissues)
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">
                                                采集到 <strong>{{ $collectissues_count }}</strong> 个奖期：
                                            </label>
                                            <div class="col-md-7 form-control-static">
                                                <input type="hidden" name="issuefrom" value="collect" />
                                                <ol>
                                                    @foreach ($collectissues as $key=>$issue)
                                                    <li>奖期：{{ $issue['issue'] }}&nbsp;&nbsp;&nbsp;&nbsp;开售时间：{{ $issue['salestart'] }}&nbsp;&nbsp;&nbsp;&nbsp;停售时间：{{ $issue['saleend'] }}&nbsp;&nbsp;&nbsp;&nbsp;开奖周期：{{ $issue['week'] }}
                                                        <input type="hidden" name="collect_issue[{{ $issue['issue'] }}]" value="{{ $issue['issue'] }}" />
                                                        <input type="hidden" name="collect_belongdate[{{ $issue['issue'] }}]" value="{{ $issue['belongdate'] }}" />
                                                        <input type="hidden" name="collect_salestart[{{ $issue['issue'] }}]" value="{{ $issue['salestart'] }}" />
                                                        <input type="hidden" name="collect_saleend[{{ $issue['issue'] }}]" value="{{ $issue['saleend'] }}" />
                                                        <input type="hidden" name="collect_canceldeadline[{{ $issue['issue'] }}]" value="{{ $issue['canceldeadline'] }}" />
                                                        <input type="hidden" name="collect_earliestwritetime[{{ $issue['issue'] }}]" value="{{ $issue['earliestwritetime'] }}" />
                                                    </li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        </div>
                                        @endif


                                        <div class="form-group">
                                            <div class="col-md-7 col-md-offset-2">
                                                <button type="submit" class="btn btn-primary btn-md" id="confirm_button" disabled="disabled">
                                                    <i class="fa fa-plus-circle"></i>
                                                    确认添加
                                                </button>
                                            </div>
                                        </div>


                                    @else


                                            <div class="form-group">
                                                <label for="start_date" class="col-md-2 control-label">奖期生成日期</label>
                                                <div class="col-md-7 form-control-static">
                                                    <div class="input-daterange input-group">
                                                        <input type="text" class="form-control form_datetime" name="start_date" id='start_date' autocomplete="off"  placeholder="开始日期">
                                                        <span class="input-group-addon"> ~ </span>
                                                        <input type="text" class="form-control form_datetime" name="end_date" id='end_date' autocomplete="off"  placeholder="结束日期">
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($need_first)
                                            <div class="form-group">
                                                <label for="first_date" class="col-md-2 control-label">奖期生成开始期号</label>
                                                <div class="col-md-7 form-control-static">
                                                    第 <input type="text" name="first_date" id="first_date" size="11">&nbsp;期（请正确输入将要生成的起始期号）<br>
                                                    规则：{{ $lottery->issue_rule['rule'] }}
                                                </div>
                                            </div>
                                            @endif


                                            @if (strtolower($lottery->ident)=='xglhc')
                                            <!--香港六合彩-->
                                            <div class="form-group">
                                                <label for="issuefrom" class="col-md-2 control-label">奖期生成方式</label>
                                                    <div class="col-md-7 form-control-static">
                                                        <label class="radio-inline"><input class="form-actions" type="radio" id="issuefrom" name="issuefrom" value="collect" checked="checked"> 采集官网&nbsp;&nbsp;</label>
                                                        <label class="radio-inline"><input class="form-actions" type="radio" name="issuefrom" value="code"> 程序生成（注意：此方式需要先修改<strong>彩种的开奖周期</strong>，生成奖期后再修改<strong>奖期的开始时间</strong>）</label>
                                                    </div>
                                            </div>
                                            @endif

                                            <div class="form-group">
                                                <div class="col-md-7 col-md-offset-2">
                                                    <input name="step" type="hidden" value="confirm" />
                                                    <button type="submit" class="btn btn-primary btn-md" id="confirm_button">
                                                        <i class="fa fa-plus-circle"></i>
                                                        添加
                                                    </button>
                                                </div>
                                            </div>

                                    @endif


                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
<script>
    @if(empty($step))
    laydate.skin('lynn');
    var layConfig ={
        elem: '#start_date',
        event: 'focus',
        format: 'YYYYMMDD',
        istoday: true,
        zindex:2
    };
    laydate(layConfig);

    layConfig.elem = '#end_date';
    laydate(layConfig);
    @endif

    function submitForm() {
        $("#confirm_button").attr('disabled',true);
        return true;
    }
    $(function () {
        setTimeout(function () {
            $("#confirm_button").attr('disabled',false);
        }, 800);
    });
</script>
@stop
