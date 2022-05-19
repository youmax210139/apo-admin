@extends('layouts.base')

@section('title','游戏报表总览')

@section('function','游戏报表总览')
@section('function_link', '/lotteryreport/')

@section('here','首页')

@section('content')

    <div class="row page-title-row" style="margin:5px;">
        <div class="box box-primary">
            <form class="form-horizontal" id="search">
                <div class="box-header with-border">
                    <h3 class="box-title"><!--搜索查询区--></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">报表</label>
                                <div class="col-sm-9">
                                    <select name="reporttype" id="reporttype" class="form-control">
                                        <option value="win" @if($type=='win')selected=""@endif>用户输赢排行</option>
                                        <option value="user" @if($type=='user')selected=""@endif>参与人数排行</option>
                                        <option value="profit" @if($type=='profit')selected=""@endif>公司盈亏排行</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="created_date" class="col-sm-3 control-label">时间</label>
                                <div class="col-sm-9">
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
                                <label class="col-sm-3 control-label text-right">彩种</label>
                                <div class="col-sm-9">
                                    <select name="lottery_id" class="form-control lottery_id">
                                        <option value="0">所有彩种</option>
                                        @foreach ($lottery_list as $lottery)
                                            <option value='{{ $lottery->id }}' @if($lottery_id==$lottery->id)selected=""
                                                    @endif
                                                    ident='{{ $lottery->ident }}'>{{ $lottery->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label text-right">类型</label>
                                <div class="col-sm-9">
                                    <select name="searchtype" id="searchtype" class="form-control">
                                        @if($type=='win')
                                            <option value="0" @if($searchtype==0)selected=""@endif>中奖最多用户</option>
                                            <option value="1" @if($searchtype==1)selected=""@endif>投入最多的用户</option>
                                            <option value="2" @if($searchtype==2)selected=""@endif>输得最多的用户</option>
                                            <option value="3" @if($searchtype==3)selected=""@endif>赢得最多的用户</option>
                                        @elseif($type=='user')
                                            <option value="0" @if($searchtype==0)selected=""@endif>参与人数最多的奖期</option>
                                            <option value="1" @if($searchtype==1)selected=""@endif>参与人数最多的游戏</option>
                                            <option value="2" @if($searchtype==2)selected=""@endif>参与人数最多的玩法</option>
                                            <option value="3" @if($searchtype==3)selected=""@endif>销量最好的奖期</option>
                                        @else
                                            <option value="0" @if($searchtype==0)selected=""@endif>公司亏损最多的奖期</option>
                                            <option value="1" @if($searchtype==1)selected=""@endif>公司盈利最多的奖期</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username" class="col-sm-3 col-sm-3 control-label">排行个数</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="num" value="{{$num}}"
                                           placeholder="个数">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="btn-group col-md-6">
                        <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search"><i
                                    class="fa fa-search" aria-hidden="true"></i>查询
                        </button>
                    </div>
                    <div class=" btn-group col-md-6">
                        <button type="reset" class="btn btn-default col-sm-2"><i class="fa fa-refresh"
                                                                                 aria-hidden="true"></i>重置
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">

        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    @if($type=='win')
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th>排名</th>
                                <th>用户名</th>
                                <th>注册时间</th>
                                <th>总投注金额</th>
                                <th>返点金额</th>
                                <th>总中奖金额</th>
                                <th>总结算</th>
                                <th>公司利润</th>
                            </tr>
                            @if($results)
                                @foreach($results as $key=>$result)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        @if($result->user_observe)
                                            <td><span class="label-danger">{{$result->username}}</span></td>
                                        @else
                                        <td>{{$result->username}}</td>
                                        @endif
                                        <td>{{$result->created_at}}</td>
                                        <td>{{$result->total_price}}</td>
                                        <td>{{$result->total_rebate}}</td>
                                        <td>{{$result->total_bonus}}</td>
                                        <td class=" @if($result->total_win>=0) text-green @else  text-red @endif">{{$result->total_win}}</td>
                                        <td> <span class="badge @if($result->total_win>=0)bg-green @else bg-red @endif">{{number_format($result->total_win_rate*100,2)}}
                                                %</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">暂无数据</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    @elseif($type=='user')
                        @if($searchtype==0)
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th>排名</th>
                                    <th>游戏</th>
                                    <th>奖期</th>
                                    <th>参与人数</th>
                                </tr>
                                @if($results)
                                    @foreach($results as $key=>$result)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$result->lottery_name}}</td>
                                            <td>{{$result->issue}}</td>
                                            <td>{{$result->total_num}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">暂无数据</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @elseif($searchtype==1)
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th>排名</th>
                                    <th>游戏</th>
                                    <th>参与人数</th>
                                </tr>
                                @if($results)
                                    @foreach($results as $key=>$result)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$result->lottery_name}}</td>
                                            <td>{{$result->total_num}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">暂无数据</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @elseif($searchtype==2)
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th>排名</th>
                                    <th>游戏</th>
                                    <th>玩法</th>
                                    <th>参与人数</th>
                                </tr>
                                @if($results)
                                    @foreach($results as $key=>$result)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$result->lottery_name}}</td>
                                            <td>{{$result->method_name}}</td>
                                            <td>{{$result->total_num}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">暂无数据</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @elseif($searchtype==3)
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th>排名</th>
                                    <th>游戏</th>
                                    <th>奖期</th>
                                    <th>销售总额</th>
                                </tr>
                                @if($results)
                                    @foreach($results as $key=>$result)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$result->lottery_name}}</td>
                                            <td>{{$result->issue}}</td>
                                            <td>{{$result->total_price}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">暂无数据</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @endif
                    @else
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th>排名</th>
                                <th>游戏</th>
                                <th>奖期</th>
                                <th>销售总额</th>
                                <th>总返点</th>
                                <th>总返奖</th>
                                <th>总结算</th>
                            </tr>
                            @if($results)
                                @foreach($results as $key=>$result)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$result->lottery_name}}</td>
                                        <td>{{$result->issue}}</td>
                                        <td>{{$result->total_price}}</td>
                                        <td>{{$result->total_rebate}}</td>
                                        <td>{{$result->total_bonus}}</td>
                                        <td> <span class="badge @if($result->total_win>=0)bg-green @else bg-red @endif">{{$result->total_win}}</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">暂无数据</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
        };
        laydate(layConfig);

        layConfig.elem = '#end_date';
        laydate(layConfig);
        $("#reporttype").change(function () {
            if ($(this).val() == 'win') {
                $("#searchtype").html(' <option value="0" selected="">中奖最多用户</option>\n' +
                    '                                        <option value="1">投入最多的用户</option>\n' +
                    '                                        <option value="2">输得最多的用户</option>\n' +
                    '                                        <option value="3">赢得最多的用户</option>');
            } else if ($(this).val() == 'user') {
                $("#searchtype").html('<option value="0" selected="">参与人数最多的奖期</option>\n' +
                    '<option value="1">参与人数最多的游戏</option>\n' +
                    '<option value="2">参与人数最多的玩法</option>\n' +
                    '<option value="3">销量最好的奖期</option>');
            } else if ($(this).val() == 'profit') {
                $("#searchtype").html('<option value="0" selected="">公司亏损最多的奖期</option>\n' +
                    '<option value="1">公司盈利最多的奖期</option>');
            }
        })
    </script>
@stop