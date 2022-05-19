@extends('layouts.base')
@section('title','平台报表概览')

@section('function','平台报表概览')
@section('function_link', '/')

@section('here','首页')


@section('content')
    <div class="">

        <div class="box box-primary">
            <div class="box-header  with-border">
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" id="search">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="username" class="col-sm-4 col-sm-4 control-label">用户名</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="username" name='username'
                                           placeholder="用户名"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="created_date" class="col-sm-2 control-label">时间</label>
                                <div class="col-sm-10">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date"
                                               id='start_date' value="{{$start_time}}" placeholder="开始时间"
                                               autocomplete="off">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date"
                                               id='end_date' value="{{$end_time}}" placeholder="结束时间"
                                               autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" data="1" class="btn btn-danger hotKey">今天</button>
                            <button type="button" data="2" class="btn hotKey">昨天</button>
                            <button type="button" data="3" class="btn hotKey">前天</button>
                            <button type="button" data="4" class="btn hotKey">3天</button>
                            <button type="button" data="5" class="btn hotKey">7天</button>
                            <button type="button" data="6" class="btn hotKey">15天</button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" onclick="hotKeySearch(0)" class="btn btn-primary">查询</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">

                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">总充值次数</span>
                                <span class="info-box-number"
                                      id="total_deposit_count">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-maroon">
                            <span class="info-box-icon"><i class="fa fa-trophy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">充值人数</span>
                                <span class="info-box-number"
                                      id="total_deposit_times">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-recycle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">首充人数</span>
                                <span class="info-box-number"
                                      id="total_deposit_first">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">二充人数</span>
                                <span class="info-box-number"
                                      id="total_deposit_second">0</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">三充以上人数</span>
                                <span class="info-box-number"
                                      id="total_deposit_third">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-maroon">
                            <span class="info-box-icon"><i class="fa fa-trophy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">注册人数</span>
                                <span class="info-box-number"
                                      id="total_user_reg">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-recycle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">登录人数</span>
                                <span class="info-box-number"
                                      id="total_users_login">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">当前在线人数</span>
                                <span class="info-box-number"
                                      id="total_users_online">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">投注金额(彩)</span>
                                <span class="info-box-number" id="lottery_projects_prices">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-maroon">
                            <span class="info-box-icon"><i class="fa fa-trophy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">派奖金额(彩)</span>
                                <span class="info-box-number" id="lottery_projects_bonus">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-recycle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">返点(彩)</span>
                                <span class="info-box-number" id="lottery_projects_rebate">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">游戏人数</span>
                                <span class="info-box-number"
                                      id="total_users_play">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">现金充值金额(元)</span>
                                <span class="info-box-number"
                                      id="deposit_amount">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-yellow">
                            <span class="info-box-icon"><i class="fa fa-bank"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">现金提现金额(元)</span>
                                <span class="info-box-number"
                                      id="withdrawal_amount">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-purple">
                            <span class="info-box-icon"><i class="fa fa-gift"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">活动费用(元)</span>
                                <span class="info-box-number"
                                      id="activity_amount">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-fuchsia">
                            <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">有效人数</span>
                                <span class="info-box-number" id="total_user_real">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-maroon">
                            <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">提现人数</span>
                                <span class="info-box-number"
                                      id="total_withdrawals_user">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-maroon">
                            <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">首次绑卡人数</span>
                                <span class="info-box-number"
                                      id="total_first_user_bind_bank">0</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row" style="padding-top: 10px;border-top: 1px solid #eee;">
                    <div class=" col-sm-12">
                        <div id="todaySalesChart" style="height: 520px; width: 100%"></div>
                    </div>
                    <div class=" col-sm-12">
                        <div id="todaySalesChart1" style="height: 520px; width: 100%"></div>
                    </div>
                </div>
                <div class="row" style="padding-top: 10px;border-top: 1px solid #eee;">
                    <div class=" col-sm-12">
                        <div id="todayThirdSalesChart" style="height: 520px; width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> 最近 10 天 注册/首充/登陆/游戏 人数报表
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <div id="users" class="users" style="height: 330px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> 最近 10 天充提报表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <div id="in_out" class="in_out" style="height: 330px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script src="/assets/libs/echarts/2.2.7/echarts.js?20191015"></script>
    <script src="/assets/libs/echarts/2.2.7/macarons.js"></script>
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
        var defaultTheme = 'macarons';
        var users;
        var in_out;
        var chart = echarts;
        var todaySalesChart;
        var todaySalesChart1;
        var todayThirdSalesChart;
        var in_out_dom = document.getElementById('in_out');
        var users_dom = document.getElementById('users');
        var todaySalesChartDom = document.getElementById("todaySalesChart");
        var todaySalesChartDom1 = document.getElementById("todaySalesChart1");
        var todayThirdSalesChartDom = document.getElementById("todayThirdSalesChart");

        function requireCallback(defaultTheme) {
            refresh(defaultTheme);
        }

        function refresh(defaultTheme) {
            if (users && users.dispose) {
                users.dispose();
            }

            users = chart.init(users_dom,defaultTheme);
            users.setOption(users_option, true);

            if (in_out && in_out.dispose) {
                in_out.dispose();
            }

            in_out = chart.init(in_out_dom,defaultTheme);
            in_out.setOption(in_out_option, true);
        }

        function refreshChart(data) {
            //彩票
            todaySalesChartOpion.title.text = "平台彩票销量("+data.lottery_projects_prices+"元)";
            todaySalesChartOpion.series[0].data = data.lottery_sales.chart1.series;
            todaySalesChart = chart.init(todaySalesChartDom,defaultTheme);
            todaySalesChart.setOption(todaySalesChartOpion, true);

            todaySalesChartOpion1.xAxis[0].data = data.lottery_sales.chart2.xAxis;
            todaySalesChartOpion1.series[0].data = data.lottery_sales.chart2.series.prize;
            todaySalesChartOpion1.series[1].data = data.lottery_sales.chart2.series.bonus;
            todaySalesChartOpion1.series[2].data = data.lottery_sales.chart2.series.rebate;
            todaySalesChartOpion1.series[3].data = data.lottery_sales.chart2.series.total;
            todaySalesChart1 = chart.init(todaySalesChartDom1,defaultTheme);
            todaySalesChart1.setOption(todaySalesChartOpion1, true);

            //第三方
            todayThirdSalesChartOpion.title.text = "第三方销量("+data.thirdgame_sales.total_bet+"元)";
            todayThirdSalesChartOpion.legend.data = data.thirdgame_sales.legend.data;
            todayThirdSalesChartOpion.legend.selected = data.thirdgame_sales.legend.selected;
            todayThirdSalesChartOpion.series[0].data = data.thirdgame_sales.series;
            todayThirdSalesChart = chart.init(todayThirdSalesChartDom,defaultTheme);
            todayThirdSalesChart.setOption(todayThirdSalesChartOpion, true);
        }
        var users_option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['注册人数', '首充人数', '登录人数', '游戏人数']
            },
            calculable: true,
            xAxis: [
                {
                    type: 'category',
                    data: {!! $deposits_today_amount_last10['keys'] !!}
                }
            ],
            yAxis: [
                {
                    type: 'value'
                }
            ],
            series: [
                {
                    name: '注册人数',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: '#21BDF7',
                        }
                    },
                    data: {!! $today_created_last10 !!}
                },
                {
                    name: '首充人数',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: '#E521F7',
                        }
                    },
                    data:  {!! $deposits_today_amount_last10_everyday !!}
                },
                {
                    name: '登录人数',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: '#22f788',
                        }
                    },
                    data:  {!! $today_login_last10 !!}
                },
                {
                    name: '游戏人数',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: '#f72a37',
                        }
                    },
                    data:  {!! $today_play_last10 !!}
                }
            ]
        };

        var in_out_option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['充值金额', '提现金额']
            },
            calculable: true,
            xAxis: [
                {
                    type: 'category',
                    data:  {!! $deposits_today_amount_last10['keys'] !!}
                }
            ],
            yAxis: [
                {
                    type: 'value'
                }
            ],
            series: [
                {
                    name: '充值金额',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: '#1393E3',
                        }
                    },
                    data:  {!! $deposits_today_amount_last10['data'] !!}
                },
                {
                    name: '提现金额',
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: '#F76821',
                        }
                    },
                    data: {!! $withdrawal_today_amount_last10 !!}
                }
            ]
        };
        var todaySalesChartOpion1 = {
            //color: ['#003366', '#006699', '#4cabce', '#e5323e'],
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['投注','奖金','返点','盈亏']
            },
            xAxis: [
                {
                    type: 'category',
                    axisPointer: {
                        type: 'shadow'
                    },
                    data:[]
                }
            ],
            yAxis: [
                {
                    type: 'value'
                }
            ],
            series: [
                {
                    name: '投注',
                    type: 'bar',
                    barWidth : 30,
                    label: {
                        normal: {
                            show: true,
                            position: 'insideBottom',
                            distance: 15,
                            align: 'left',
                            verticalAlign: 'middle',
                            rotate: 90,
                            formatter: '{c}  {name|{a}}',
                            fontSize: 12,
                            rich: {
                                name: {
                                    textBorderColor: '#fff'
                                }
                            }
                        },
                    },
                    data: []
                },
                {
                    name: '奖金',
                    type: 'bar',
                    stack: '总量',
                    barWidth : 30,
                    label: {
                        normal: {
                            show: true,
                            position: 'insideBottom',
                            distance: 15,
                            align: 'left',
                            verticalAlign: 'middle',
                            rotate: 90,
                            formatter: '{c}  {name|{a}}',
                            fontSize: 12,
                            rich: {
                                name: {
                                    textBorderColor: '#fff'
                                }
                            }
                        },
                    },
                    data: []
                },
                {
                    name: '返点',
                    type: 'bar',
                    stack: '总量',
                    barWidth : 30,
                    label: {
                        normal: {
                            show: true,
                            position: 'insideBottom',
                            distance: 15,
                            align: 'left',
                            verticalAlign: 'middle',
                            rotate: 90,
                            formatter: '{c}  {name|{a}}',
                            fontSize: 12,
                            rich: {
                                name: {
                                    textBorderColor: '#fff'
                                }
                            }
                        },
                    },
                    data: []
                },
                {
                    name: '盈亏',
                    type: 'bar',
                    barWidth : 30,
                    label: {
                        normal: {
                            show: true,
                            position: 'insideBottom',
                            distance: 15,
                            align: 'left',
                            verticalAlign: 'middle',
                            rotate: 90,
                            formatter: '{c}  {name|{a}}',
                            fontSize: 12,
                            rich: {
                                name: {
                                    textBorderColor: '#fff'
                                }
                            }
                        },
                    },
                    data: []
                }
            ]
        };
        var todaySalesChartOpion = {
            title: {
                text: '平台彩票销量(0元)',
                subtext: '',
                x: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            series: [
                {
                    name: '彩种',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    data: [],

                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        var todayThirdSalesChartOpion = {
            title: {
                text: '第三方游戏销量(1991427.468 元)',
                subtext: '',
                x: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'left',
                data: ["ciaag","ciabbin","vr","ciasunbet","ug","ciaimpt","ciaimdianjing","ciaggqp","ciaky","ciavgqp","cialgqp","cialcqp","fhleli","ciasb","ciasbosport","ciaggdy"],
                selected: {}
            },
            series
                :
                [
                    {
                        name: '名称',
                        type: 'pie',
                        radius: '55%',
                        center: ['50%', '50%'],
                        data: [{name:"ciaag",value:1699411.1500},{name:"ciabbin",value:0.0000},{name:"vr",value:5741.7880},{name:"ciasunbet",value:300.0000},{name:"ug",value:3578.0000},{name:"ciaimpt",value:0.0000},{name:"ciaimdianjing",value:0.0000},{name:"ciaggqp",value:0.0000},{name:"ciaky",value:52769.2500},{name:"ciavgqp",value:0.0000},{name:"cialgqp",value:0.0000},{name:"cialcqp",value:0.0000},{name:"fhleli",value:229627.2800},{name:"ciasb",value:0.0000},{name:"ciasbosport",value:0.0000},{name:"ciaggdy",value:0.0000}],
                        label: {
                            normal: {
                                formatter: '{a|{a}}{abg|}\n{hr|}\n  {b|{b}：}{c}  {per|{d}%}  ',
                                backgroundColor: '#eee',
                                borderColor: '#aaa',
                                borderWidth: 1,
                                borderRadius: 4,
                                // shadowBlur:3,
                                // shadowOffsetX: 2,
                                // shadowOffsetY: 2,
                                // shadowColor: '#999',
                                // padding: [0, 7],
                                rich: {
                                    a: {
                                        color: '#999',
                                        lineHeight: 22,
                                        align: 'center'
                                    },
                                    // abg: {
                                    //     backgroundColor: '#333',
                                    //     width: '100%',
                                    //     align: 'right',
                                    //     height: 22,
                                    //     borderRadius: [4, 4, 0, 0]
                                    // },
                                    hr: {
                                        borderColor: '#aaa',
                                        width: '100%',
                                        borderWidth: 0.5,
                                        height: 0
                                    },
                                    b: {
                                        fontSize: 16,
                                        lineHeight: 33
                                    },
                                    per: {
                                        color: '#eee',
                                        backgroundColor: '#334455',
                                        padding: [2, 4],
                                        borderRadius: 2
                                    }
                                }
                            }
                        },
                        itemStyle
                            :
                            {
                                emphasis: {
                                    shadowBlur: 10,
                                    shadowOffsetX
                                        :
                                        0,
                                    shadowColor
                                        :
                                        'rgba(0, 0, 0, 0.5)'
                                }
                            }
                    }
                ]
        }


        function hotKeySearch(type) {
            loadShow();
            if(type==0){
                $(".hotKey").removeClass('btn-danger');
            }
            $.ajax({
                url: "/report/",
                dataType: "json",
                method: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    hot_key: type,
                    username: $("#username").val(),
                    start_time:$("#start_date").val(),
                    end_time:$("#end_date").val()
                }
            }).done(function (json) {
                loadFadeOut();
                if (json.hasOwnProperty('code') && json.code == '302') {
                    window.location.reload();
                }
                if (json.status == 0) {
                    $("#total_deposit_count").html(json.data.total_deposit_count);
                    $("#total_deposit_times").html(json.data.total_deposit_times);
                    $("#activity_amount").html(json.data.activity_amount);
                    $("#deposit_amount").html(json.data.deposit_amount);
                    $("#lottery_projects_bonus").html(json.data.lottery_projects_bonus);
                    $("#lottery_projects_prices").html(json.data.lottery_projects_prices);
                    $("#lottery_projects_rebate").html(json.data.lottery_projects_rebate);
                    $("#total_deposit_first").html(json.data.total_deposit_first);
                    $("#total_deposit_second").html(json.data.total_deposit_second);
                    $("#total_deposit_third").html(json.data.total_deposit_third);
                    $("#total_user_real").html(json.data.total_user_real);
                    $("#total_user_reg").html(json.data.total_user_reg);
                    $("#total_users_login").html(json.data.total_users_login);
                    $("#total_users_online").html(json.data.total_users_online);
                    $("#total_users_play").html(json.data.total_users_play);
                    $("#withdrawal_amount").html(json.data.withdrawal_amount);
                    $("#chongti_amount").html(json.data.deposit_amount-json.data.withdrawal_amount);
                    $("#total_withdrawals_user").html(json.data.withdrawals_user);
                    $("#total_first_user_bind_bank").html(json.data.first_user_bind_bank);

                    refreshChart(json.data);
                } else {
                    alert(json.msg);
                }
            });
        }

        $(".hotKey").click(function () {
            hotKeySearch($(this).attr('data'));
            $(".hotKey").removeClass('btn-danger');
            $(this).addClass('btn-danger');
        });
        $(function(){
            hotKeySearch(1);
            requireCallback('macarons');
        });
    </script>
@stop
