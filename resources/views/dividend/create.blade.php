@extends('layouts.base')

@section('title','分红契约管理')

@section('function','分红契约管理')
@section('function_link', '/dividend/')

@section('here','添加分红契约')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<link rel="stylesheet" href="/assets/plugins/bootstrap-slider/bootstrap-slider.min.css">

    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">签订分红契约</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')

                            @if($dividend_type==1)
                                <div class="box dividend_row" id="parent_dividend">
                                    <div class="box-header with-border ">
                                        <h4 class="text-center "><strong>上级 {{$parent_dividend['username']}} 分红[A线 佣金模式]</strong></h4>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-hover table-striped table-bordered">
                                            <tr>
                                                <th class="text-center" >日最小盈亏</th>
                                                <th class="text-center" >日最大盈亏</th>
                                                <th class="text-center" >活跃人数</th>
                                                <th class="text-center" >分红金额</th>
                                            </tr>

                                            @foreach( $parent_dividend->content as $content )
                                                <tr>
                                                    <td class="text-center" >{{$content['min']}}</td>
                                                    <td class="text-center" >{{$content['max']}}</td>
                                                    <td class="text-center" >{{$content['daus']}}</td>
                                                    <td class="text-center" >
                                                        @if(in_array(get_config('dividend_type_ident'), ['Chuangying']))
                                                        <div class="row"><div class="col-md-6 text-right">自身：</div><div class="col-md-6 text-left">{{$content['commission'][0]}}</div></div>
                                                        <div class="row"><div class="col-md-6 text-right">上级：</div><div class="col-md-6 text-left">{{$content['commission'][1]}}</div></div>
                                                        <div class="row"><div class="col-md-6 text-right">直属：</div><div class="col-md-6 text-left">{{$content['commission'][2]}}</div></div>
                                                        <div class="row"><div class="col-md-6 text-right">招商：</div><div class="col-md-6 text-left">{{$content['commission'][3]}}</div></div>
                                                        @else
                                                        <div class="row"><div class="col-md-6 text-right">上级：</div><div class="col-md-6 text-left">{{$content['commission'][0]}}</div></div>
                                                        <div class="row"><div class="col-md-6 text-right">上上级：</div><div class="col-md-6 text-left">{{$content['commission'][1]}}</div></div>
                                                        <div class="row"><div class="col-md-6 text-right">上上上级：</div><div class="col-md-6 text-left">{{$content['commission'][2]}}</div></div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @else
                                @if(!empty($parent_dividend))
                                <form class="form-horizontal" role="form" method="POST" action="/dividend/createoredit" onsubmit="return false;">
                                <div class="box dividend_row" id="parent_dividend">
                                    <div class="box-header with-border ">
                                        <h4 class="text-center "><strong>上级 {{$parent_dividend['username']}} @if($parent_dividend->is_system_default) 【系统默认】 @endif日分红</strong></h4>
                                    </div>
                                    <div class="box-body">
                                        @if( $user->parent_id == 0 )
                                            <div class="form-group has-feedback">
                                                <label class="col-sm-3 control-label">分红类型：</label>
                                                <div class="col-sm-7 ">
                                                    <select class="form-control dividend_type" name="type" >
                                                        <option value="1" @if($parent_dividend->type==1) selected @endif disabled> A线[佣金模式]</option>
                                                        <option value="2" @if($parent_dividend->type!=1) selected @endif disabled> B线[比例模式]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        @if(get_config('dividend_backend_show_mode') == 1 )
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">分红模式：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <label><input type="radio" value="1" name="mode" @if($parent_dividend->mode==1) checked @endif disabled> 累计</label>
                                                        <label><input type="radio" value="0" name="mode" @if($parent_dividend->mode!=1) checked @endif disabled> 不累计</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if( $dividend_type != 1 )
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">状态：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="status" @if($parent_dividend->status==0) checked @endif disabled> 待确认</label>
                                                        <label><input type="radio" value="1" name="status" @if($parent_dividend->status==1) checked @endif disabled> 已同意</label>
                                                        <label><input type="radio" value="2" name="status" @if($parent_dividend->status==2) checked @endif disabled> 已拒绝</label>
                                                        <label><input type="radio" value="3" name="status" @if($parent_dividend->status==3) checked @endif disabled> 已失效</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">分红比例：</label>
                                            <div class="col-sm-7 ">
                                                <div class="input-group" >
                                                    <input type="text" class="form-control input-sm input-small" value="{{$parent_dividend->base_rate}}"  name="base_rate" disabled>
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">分红要求消费天数：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <input type="text" class="form-control" name="base_consume_day" value="{{$parent_dividend->base_consume_day}}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">每天最低日量：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <input type="text" class="form-control" name="base_min_day_sales" value="{{$parent_dividend->base_min_day_sales}}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">消费量类型：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" name="consume_type" @if($parent_dividend->consume_type!=1) checked @endif disabled> 总消费量</label>
                                                    <label><input type="radio" value="1" name="consume_type" @if($parent_dividend->consume_type==1) checked @endif disabled> 平均日量</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">亏损量类型：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" name="loss_type" @if($parent_dividend->loss_type!=1) checked @endif checked disabled> 总亏损量</label>
                                                    <label><input type="radio" value="1" name="loss_type" @if($parent_dividend->loss_type==1) checked @endif disabled> 平均日亏损量</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">奖励类型：</label>
                                            <div class="col-sm-7">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" name="reward_type" @if($parent_dividend->reward_type!=1) checked @endif disabled> 百分比</label>
                                                    <label><input type="radio" value="1" name="reward_type" @if($parent_dividend->reward_type==1) checked @endif disabled> 固定金额</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">额外配置：</label>
                                            <div class="col-sm-7 ">
                                                <table data-table="extraRules" class="table table-bordered table-small dividend_content_table">
                                                    <thead>
                                                    <tr>
                                                        <td width="14%" class="text-center">消费量(万)</td>
                                                        <td width="14%" class="text-center">亏损量(万)</td>
                                                        <td width="14%" class="text-center">有效会员</td>
                                                        <td width="14%" class="text-center">奖励金额</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody >
                                                    @foreach( $parent_dividend->content as $key => $content )
                                                        <tr>
                                                            <td class="text-center"><input name="consume_amount[]" type="text" class="form-control input-sm input-small" value="{{$content['consume_amount']}}" disabled></td>
                                                            <td class="text-center"><input name="profit[]" type="text" class="form-control input-sm input-small" value="{{$content['profit']}}" disabled></td>
                                                            <td class="text-center"><input name="daus[]" type="text" class="form-control input-sm input-small" value="{{$content['daus']}}" disabled></td>
                                                            <td class="text-center"><div class="input-group"><input name="rate[]" type="text" class="form-control input-sm input-small" value="{{$content['rate']}}" disabled><span class="input-group-addon">%</span></div></td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                @endif

                                @if(!empty($user_valid_dividend))
                                <form class="form-horizontal" role="form" method="POST" action="/dividend/createoredit" onsubmit="return false;">
                                    <div class="box dividend_row" id="user_valid_dividend">
                                        <div class="box-header with-border ">
                                            <h4 class="text-center "><strong>用户 {{$user_valid_dividend->username}} 已确认契约@if($user_valid_dividend->is_system_default) 【系统默认】 @endif</strong></h4>
                                        </div>
                                        <div class="box-body">
                                            @if( $user->parent_id == 0 )
                                                <div class="form-group has-feedback">
                                                    <label class="col-sm-3 control-label">分红类型：</label>
                                                    <div class="col-sm-7 ">
                                                        <select class="form-control dividend_type" name="type" >
                                                            <option value="1" @if($user_valid_dividend->type==1) selected @endif disabled> A线[佣金模式]</option>
                                                            <option value="2" @if($user_valid_dividend->type!=1) selected @endif disabled> B线[比例模式]</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(get_config('dividend_backend_show_mode') == 1 )
                                                <div class="form-group has-feedback type_b">
                                                    <label class="col-sm-3 control-label">分红模式：</label>
                                                    <div class="col-sm-7 ">
                                                        <div class="radio">
                                                            <label><input type="radio" value="1" name="mode" @if($user_valid_dividend->mode==1) checked @endif disabled> 累计</label>
                                                            <label><input type="radio" value="0" name="mode" @if($user_valid_dividend->mode!=1) checked @endif disabled> 不累计</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if( $dividend_type != 1 )
                                                <div class="form-group has-feedback type_b">
                                                    <label class="col-sm-3 control-label">状态：</label>
                                                    <div class="col-sm-7 ">
                                                        <div class="radio">
                                                            <label><input type="radio" value="0" name="status" @if($user_valid_dividend->status==0) checked @endif disabled> 待确认</label>
                                                            <label><input type="radio" value="1" name="status" @if($user_valid_dividend->status==1||$user_valid_dividend->status==NULL) checked @endif disabled> 已同意</label>
                                                            <label><input type="radio" value="2" name="status" @if($user_valid_dividend->status==2) checked @endif disabled> 已拒绝</label>
                                                            <label><input type="radio" value="3" name="status" @if($user_valid_dividend->status==3) checked @endif disabled> 已失效</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">分红比例：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="input-group" >
                                                        <input type="text" class="form-control input-sm input-small" value="{{$user_valid_dividend->base_rate}}"  name="base_rate" disabled>
                                                        <span class="input-group-addon">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">分红要求消费天数：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <input type="text" class="form-control" name="base_consume_day" value="{{$user_valid_dividend->base_consume_day??0}}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">每天最低日量：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <input type="text" class="form-control" name="base_min_day_sales" value="{{$user_valid_dividend->base_min_day_sales??0}}" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">消费量类型：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="consume_type" @if($user_valid_dividend->consume_type!=1) checked @endif disabled> 总消费量</label>
                                                        <label><input type="radio" value="1" name="consume_type" @if($user_valid_dividend->consume_type==1) checked @endif disabled> 平均日量</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">亏损量类型：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="loss_type" @if($user_valid_dividend->loss_type!=1) checked @endif checked disabled> 总亏损量</label>
                                                        <label><input type="radio" value="1" name="loss_type" @if($user_valid_dividend->loss_type==1) checked @endif disabled> 平均日亏损量</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">奖励类型：</label>
                                                <div class="col-sm-7">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="reward_type" @if($user_valid_dividend->reward_type!=1) checked @endif disabled> 百分比</label>
                                                        <label><input type="radio" value="1" name="reward_type" @if($user_valid_dividend->reward_type==1) checked @endif disabled> 固定金额</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">额外配置：</label>
                                                <div class="col-sm-7 ">
                                                    <table data-table="extraRules" class="table table-bordered table-small dividend_content_table">
                                                        <thead>
                                                        <tr>
                                                            <td width="14%" class="text-center">消费量(万)</td>
                                                            <td width="14%" class="text-center">亏损量(万)</td>
                                                            <td width="14%" class="text-center">有效会员</td>
                                                            <td width="14%" class="text-center">奖励金额</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody >
                                                        @foreach( $user_valid_dividend->content as $key => $content )
                                                            <tr>
                                                                <td class="text-center"><input name="consume_amount[]" type="text" class="form-control input-sm input-small" value="{{$content['consume_amount']}}" disabled></td>
                                                                <td class="text-center"><input name="profit[]" type="text" class="form-control input-sm input-small" value="{{$content['profit']}}" disabled></td>
                                                                <td class="text-center"><input name="daus[]" type="text" class="form-control input-sm input-small" value="{{$content['daus']}}" disabled></td>
                                                                <td class="text-center"><div class="input-group"><input name="rate[]" type="text" class="form-control input-sm input-small" value="{{$content['rate']}}"  disabled><span class="input-group-addon">%</span></div></td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @endif

                                @if(!empty($user_unconfirmed_dividend))
                                <form class="form-horizontal" role="form" method="POST" action="/dividend/createoredit" onsubmit="return CreateOrUpdate(this);">
                                    <div class="box dividend_row" id="user_unconfirmed_dividend">
                                        <div class="box-header with-border ">
                                            <h4 class="text-center "><strong>调整 {{$user_unconfirmed_dividend->username}} 待确认分红契约</strong></h4>
                                        </div>

                                        <div class="box-body">
                                            @if( $user->parent_id == 0 )
                                                <div class="form-group has-feedback">
                                                    <label class="col-sm-3 control-label">分红类型：</label>
                                                    <div class="col-sm-7 ">
                                                        <select class="form-control dividend_type" name="type" >
                                                            <option value="1" @if($user_unconfirmed_dividend->type==1) selected @endif > A线[佣金模式]</option>
                                                            <option value="2" @if($user_unconfirmed_dividend->type!=1) selected @endif > B线[比例模式]</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(get_config('dividend_backend_show_mode') == 1 )
                                                <div class="form-group has-feedback type_b">
                                                    <label class="col-sm-3 control-label">分红模式：</label>
                                                    <div class="col-sm-7 ">
                                                        <div class="radio">
                                                            <label><input type="radio" value="1" name="mode" @if($user_unconfirmed_dividend->mode==1) checked @endif > 累计</label>
                                                            <label><input type="radio" value="0" name="mode" @if($user_unconfirmed_dividend->mode!=1) checked @endif > 不累计</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if( $dividend_type != 1 )
                                                <div class="form-group has-feedback type_b">
                                                    <label class="col-sm-3 control-label">状态：</label>
                                                    <div class="col-sm-7 ">
                                                        <div class="radio">
                                                            <label><input type="radio" value="0" name="status" @if($user_unconfirmed_dividend->status==0) checked @endif > 待确认</label>
                                                            <label><input type="radio" value="1" name="status" @if($user_unconfirmed_dividend->status==1) checked @endif > 已同意</label>
                                                            <label><input type="radio" value="2" name="status" @if($user_unconfirmed_dividend->status==2) checked @endif > 已拒绝</label>
                                                            <label><input type="radio" value="3" name="status" @if($user_unconfirmed_dividend->status==3) checked @endif > 已失效</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">分红比例：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="input-group" >
                                                        <input type="text" class="form-control input-sm input-small" value="{{$user_unconfirmed_dividend->base_rate}}"  name="base_rate" >
                                                        <span class="input-group-addon">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">分红要求消费天数：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <input type="text" class="form-control" name="base_consume_day" value="{{$user_unconfirmed_dividend->base_consume_day}}" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">每天最低日量：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <input type="text" class="form-control" name="base_min_day_sales" value="{{$user_unconfirmed_dividend->base_min_day_sales}}" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">消费量类型：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="consume_type" @if($user_unconfirmed_dividend->consume_type!=1) checked @endif > 总消费量</label>
                                                        <label><input type="radio" value="1" name="consume_type" @if($user_unconfirmed_dividend->consume_type==1) checked @endif > 平均日量</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">亏损量类型：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="loss_type" @if($user_unconfirmed_dividend->loss_type!=1) checked @endif checked > 总亏损量</label>
                                                        <label><input type="radio" value="1" name="loss_type" @if($user_unconfirmed_dividend->loss_type==1) checked @endif > 平均日亏损量</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">奖励类型：</label>
                                                <div class="col-sm-7">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="reward_type" @if($user_unconfirmed_dividend->reward_type!=1) checked @endif > 百分比</label>
                                                        <label><input type="radio" value="1" name="reward_type" @if($user_unconfirmed_dividend->reward_type==1) checked @endif > 固定金额</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">额外配置：</label>
                                                <div class="col-sm-7 ">
                                                    <table data-table="extraRules" class="table table-bordered table-small dividend_content_table">
                                                        <thead>
                                                        <tr>
                                                            <td width="14%" class="text-center">消费量(万)</td>
                                                            <td width="14%" class="text-center">亏损量(万)</td>
                                                            <td width="14%" class="text-center">有效会员</td>
                                                            <td width="30%" class="text-center"></td>
                                                            <td width="14%" class="text-center">奖励金额</td>
                                                            <td width="14%" class="text-center">
                                                                @if(get_config('dividend_backend_multi_level') > 0 )
                                                                    <button type="button" class="btn btn-primary btn-xs" id="addNewRule"><i class="fa fa-plus" ></i> 添加</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="dividend_log_content">
                                                            @foreach( $user_unconfirmed_dividend->content as $key => $content )
                                                                <tr>
                                                                    <td class="text-center"><input name="consume_amount[]" type="text" class="form-control input-sm input-small" value="{{$content['consume_amount']}}" ></td>
                                                                    <td class="text-center"><input name="profit[]" type="text" class="form-control input-sm input-small" value="{{$content['profit']}}" ></td>
                                                                    <td class="text-center"><input name="daus[]" type="text" class="form-control input-sm input-small" value="{{$content['daus']}}" ></td>
                                                                    <td class="text-center" ><input id="slider_{{$key}}" type="text" data-slider-value="{{$content['rate']}}"/></td>
                                                                    <td class="text-center"><div class="input-group"><input name="rate[]" type="text" class="form-control input-sm input-small" value="{{$content['rate']}}" id="rate_{{$key}}" ><span class="input-group-addon">%</span></div></td>
                                                                    <td class="text-center"><button type="button" class="btn btn-danger btn-xs btn-mini delete_btn"><i class="fa fa-times"></i> 删除</button></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-7 col-md-offset-5">
                                                    <input type="hidden" value="{{$user->id}}" name="user_id">

                                                    <button type="button" class="btn btn-warning btn-md" id="close">
                                                        <i class="fa fa-minus-circle"></i>
                                                        取消
                                                    </button>
                                                    <button type="submit" class="btn btn-primary btn-md">
                                                        <i class="fa fa-plus-circle"></i>
                                                        保存
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            @endif

                            @if( (empty($user_unconfirmed_dividend)&&($dividend_type==2||$dividend_type==null)) || ($dividend_type==1&&$user->parent_id == 0))
                            <form class="form-horizontal" role="form" method="POST" action="/dividend/createoredit" onsubmit="return CreateOrUpdate(this);">
                                <div class="box dividend_row" id="user_dividend" style="display: @if( empty($user_valid_dividend) && empty($user_unconfirmed_dividend)&& $dividend_type==2)  block @else none @endif">
                                    <div class="box-header with-border ">
                                        <h4 class="text-center "><strong>签订 {{$user->username}} 分红契约</strong></h4>
                                    </div>

                                    <div class="box-body">
                                        @if( $user->parent_id == 0 )
                                            <div class="form-group has-feedback">
                                                <label class="col-sm-3 control-label">分红类型：</label>
                                                <div class="col-sm-7 ">
                                                    <select class="form-control dividend_type" name="type" >
                                                        <option value="1" @if(get_config('dividend_default_type')==1) selected @endif> A线[佣金模式]</option>
                                                        <option value="2" @if(get_config('dividend_default_type')!=1) selected @endif> B线[比例模式]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        @if(get_config('dividend_backend_mode_canedit') > 0 )
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">分红模式：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <label><input type="radio" value="1" name="mode" > 累计</label>
                                                    <label><input type="radio" value="0" name="mode" checked> 不累计</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if( $dividend_type != 1 )
                                            <div class="form-group has-feedback type_b">
                                                <label class="col-sm-3 control-label">状态：</label>
                                                <div class="col-sm-7 ">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" name="status" checked> 待确认</label>
                                                        <label><input type="radio" value="1" name="status" > 已同意</label>
                                                        <label><input type="radio" value="2" name="status" > 已拒绝</label>
                                                        <label><input type="radio" value="3" name="status" > 已失效</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">分红比例：</label>
                                            <div class="col-sm-7 ">
                                                <div class="input-group" >
                                                    <input type="text" class="form-control input-sm input-small"  name="base_rate">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">分红要求消费天数：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <input type="text" class="form-control" name="base_consume_day" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">每天最低日量：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <input type="text" class="form-control" name="base_min_day_sales" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">消费量类型：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" name="consume_type" checked> 总消费量</label>
                                                    <label><input type="radio" value="1" name="consume_type" > 平均日量</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">亏损量类型：</label>
                                            <div class="col-sm-7 ">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" name="loss_type" checked> 总亏损量</label>
                                                    <label><input type="radio" value="1" name="loss_type" > 平均日亏损量</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">奖励类型：</label>
                                            <div class="col-sm-7">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" name="reward_type" checked> 百分比</label>
                                                    <label><input type="radio" value="1" name="reward_type" > 固定金额</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback type_b">
                                            <label class="col-sm-3 control-label">额外配置：</label>
                                            <div class="col-sm-7 ">
                                                <table data-table="extraRules" class="table table-bordered table-small dividend_content_table">
                                                    <thead>
                                                    <tr>
                                                        <td width="14%" class="text-center">消费量(万)</td>
                                                        <td width="14%" class="text-center">亏损量(万)</td>
                                                        <td width="14%" class="text-center">有效会员</td>
                                                        <td width="30%" class="text-center"></td>
                                                        <td width="14%" class="text-center">奖励金额</td>
                                                        <td width="14%" class="text-center">
                                                            @if(get_config('dividend_backend_multi_level') > 0 )
                                                            <button type="button" class="btn btn-primary btn-xs" id="addNewRule"><i class="fa fa-plus" ></i> 添加</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="dividend_log_content">
                                                        @if( empty($user_valid_dividend) && empty($user_unconfirmed_dividend))
                                                            <tr class="reNewDividend">
                                                                <td colspan="6" class="text-center" style="font-size: 60px;margin: 10px;">
                                                                    <i class="fa fa-bell-o"></i>
                                                                    <div style="font-size: 16px;font-weight: bold;margin: 8px 0px;">没有分红契约</div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="form-group new_rule">
                                            <div class="col-md-7 col-md-offset-5">
                                                <input type="hidden" value="{{$user->id}}" name="user_id">

                                                <button type="button" class="btn btn-warning btn-md closetab" >
                                                    <i class="fa fa-minus-circle"></i>
                                                    取消
                                                </button>
                                                <button type="submit" class="btn btn-primary btn-md">
                                                    <i class="fa fa-plus-circle"></i>
                                                    保存
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @endif

                            <div class="form-group" >
                                <div class="col-md-7 col-md-offset-5">
                                    @if( $user->user_level >= get_config('dividend_send_high_level',0) || $user->parent_id == 0)
                                        @if( (empty($user_unconfirmed_dividend)&&($dividend_type==2||$dividend_type==null)) || $user->parent_id == 0)
                                        <button type="button" class="btn btn-warning btn-md" id="reNewDividend">
                                            @if (empty($user_valid_dividend))
                                                签订新契约
                                            @else
                                                重新签订契约
                                            @endif
                                        </button>
                                        @endif
                                    @endif
                                    <button type="button" class="btn btn-default btn-md closetab" >
                                        关闭标签页
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    .slider.slider-horizontal{
        width: 80%;
    }
    .slider-selection,.slider-track-high{
        background: #f9c758;
    }
    .slider-track-high{
        background: #ffe19e;
    }
    .form-group .radio{line-height: 27px;margin-top: 3px;}
    .radio label:first-child{margin-left:0px;}
    .radio label{margin-left:20px;}
    .radio input[type=radio]{margin-left: -16px;margin-top: 7px;}
    .btn.btn_sm{padding: 2px 7px;}
    .box{border: 1px solid #e4e4e4;}

</style>
@stop

@section('js')
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script src="/assets/plugins/bootstrap-slider/bootstrap-slider.min.js"></script>
<script>
    $(function(){
        $('#addNewRule').click(function(){
            $('.reNewDividend').css({'display':'none'});

            $('.new_rule').css({'display':'block'});
            $('.new_rule_table').css({'display':'table'});
            $('.table-footer-group').css({'display':'table-footer-group'});
            
            addNewRule();
        });

        $("table").delegate('.delete_btn', 'click', function () {
            $(this).parents('tr').remove();
        });

        $('.closetab').click(function(){
            $('#iframe-tabs .active button',parent.document).click();
        });

        // 重新签订新契约
        $('#reNewDividend').click(function(){
            //重新签约 按钮关闭
            $('#user_dividend').css({'display':'block'});
            $('#addNewRule').click();
        });

        $('.dividend_type').change(function(){
            if( $(this).val() == 1 ){
                $(this).parents('div.form-group').siblings('.type_b').css('display','none');
            }else{
                $(this).parents('div.form-group').siblings('.type_b').css('display','block');
            }
        });

        $("input[name=mode]").change(function(){
            // 如果是累计模式，则消费、亏算量类型只能为总量
            if( $(this).val() == 1 ){
                // 消费、亏算量类型改为总量
                $(this).parents('.box-body').find('input[name=loss_type][value="0"]').prop('checked','checked');
                $(this).parents('.box-body').find('input[name=consume_type][value="0"]').prop('checked','checked');

                // 消费、亏算量类型不可选
                $(this).parents('.box-body').find('input[name=loss_type]').prop('disabled','disabled');
                $(this).parents('.box-body').find('input[name=consume_type]').prop('disabled','disabled');
            }else{
                // 消费、亏算量类型不可选
                $(this).parents('.box-body').find('input[name=loss_type]').prop('disabled',false);
                $(this).parents('.box-body').find('input[name=consume_type]').prop('disabled',false);
            }
        });
    });
    @if(!empty($user_unconfirmed_dividend))
    @foreach( $user_unconfirmed_dividend->content as $key => $content )
    sliderEvent({{$key}});
    @endforeach
    @endif

    var id = $('#dividend_log_content > tr').size();
    function addNewRule(){
        id = $('#dividend_log_content > tr').size();
        var html = ''+
            '<tr>'+
                '<td class="text-center"><input name="consume_amount[]" type="text" class="form-control input-sm input-small" value="0"></td>'+
                '<td class="text-center"><input name="profit[]" type="text" class="form-control input-sm input-small" value="0"></td>'+
                '<td class="text-center"><input name="daus[]" type="text" class="form-control input-sm input-small" value="0"></td>'+
                '<td class="text-center" ><input id="slider_'+id+'" type="text" data-slider-value="0"/></td>'+
                '<td class="text-center"><div class="input-group"><input name="rate[]" type="text" class="form-control input-sm input-small" value=0 id=rate_'+id+'><span class="input-group-addon">%</span></div></td>'+
                '<td class="text-center"><button type="button" class="btn btn-danger btn-xs btn-mini delete_btn"><i class="fa fa-times"></i> 删除</button></td>'+
            '</tr>';

        $('#dividend_log_content').append(html);
        sliderEvent(id);
        id++;
    }
    function sliderEvent( id ){
        $('#slider_'+id).slider({
            min: 0,
            max: {{$self_max_rate}},
            step:{{$dividend_step}},
            tooltip: 'always',
            tooltip_position:'bottom'
        });
        $('#slider_'+id).on("slide", function(slideEvt) {
            $("#rate_"+id).val(slideEvt.value);
        });
    }

    function CreateOrUpdate(obj) {
        var _this = $(this);
        loadShow();

        $.ajax({
            url: "/dividend/createoredit",
            dataType: "json",
            method: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: $(obj).serialize()
        }).done(function (json) {
            loadFadeOut();
            if (json.hasOwnProperty('code') && json.code == '302') {
                window.location.reload();
            }

            if( json.status==0 ){
                app.bootoast(json.msg,'success');
                window.location.reload();
            }else{
                app.bootoast(json.msg,'danger');
            }
        })

        return false;
    }


</script>
@stop