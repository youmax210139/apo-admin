@extends('layouts.base')
@section('title','控制面板')
@section('function','控制面板')
@section('function_link', '/')
@section('here','首页')
@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $onlines }}</h3>
                    <p>在线人数</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $today_active }}</h3>
                    <p>今日访问人数</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $lottery_projects_today_users }}</h3>
                    <p>今日投注人数（彩票）</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-md"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $today_created }}</h3>
                    <p>今日注册人数</p>
                </div>
                <div class="icon">
                    <i class="fa fa-registered"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> 最近 7 天域名访问排名</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <div id="domains" class="domains" style="height: 280px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-pie-chart" aria-hidden="true"></i> 最近 7 天访问浏览器类型</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <div id="browsers" class="browsers" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-map-marker" aria-hidden="true"></i> 最近 7 天访客来源</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <div id="visitors" class="visitors" style="height: 530px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/libs/echarts/2.2.7/echarts2.2.7.js"></script>
    <script>
        var visitors;
        var domains;
        var browsers;
        var chart;
        var domains_dom = document.getElementById('domains');
        var visitors_dom = document.getElementById('visitors');
        var browsers_dom = document.getElementById('browsers');

        function requireCallback(ec, defaultTheme) {
            chart = ec;
            refresh(defaultTheme);
        }

        function refresh(defaultTheme) {
            if (visitors && visitors.dispose) {
                visitors.dispose();
            }

            visitors = chart.init(visitors_dom, defaultTheme);
            visitors.setOption(visitors_option, true);

            if (domains && domains.dispose) {
                domains.dispose();
            }

            domains = chart.init(domains_dom, defaultTheme);
            domains.setOption(domains_option, true);

            if (browsers && browsers.dispose) {
                browsers.dispose();
            }

            browsers = chart.init(browsers_dom, defaultTheme);
            browsers.setOption(browsers_option, true);
        }

        function resize() {
            visitors.resize();
            domains.resize();
            browsers.resize();
        }

        window.onresize = resize;

        var browsers_option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            calculable: true,
            series: [
                {
                    type: 'pie',
                    radius: '78%',
                    center: ['50%', '55%'],
                    data: {!! $recent_most_used_client !!}
                }
            ]
        };

        var domains_option = {
            tooltip: {
                trigger: 'axis'
            },
            grid: {
                borderWidth: 0
            },
            calculable: true,
            xAxis: [
                {
                    type: 'value',
                    show: false,
                }
            ],
            yAxis: [
                {
                    type: 'category',
                    data: {!! $recent_most_visited_url['keys'] !!}
                }
            ],
            series: [
                {
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            barBorderRadius: 5,
                            color: function (params) {
                                var colorList = [
                                    '#60C0DD', '#9BCA63', '#1393E3', '#F76821',
                                    '#E521F7', '#21BDF7',
                                    '#D7504B', '#C6E579', '#F4E001', '#F0805A', '#26C0C0'
                                ];
                                return colorList[params.dataIndex]
                            }
                        },
                        emphasis: {
                            barBorderRadius: 5
                        }
                    },
                    data: {{ $recent_most_visited_url['data'] }}
                },
            ]
        };

        var visitors_option = {
            tooltip: {
                trigger: 'item',
                formatter: "{b} : {c}"
            },
            dataRange: {
                min: 0,
                max: 2500,
                x: 'left',
                y: 'bottom',
                text: ['高', '低'],           // 文本，默认为数值文本
                calculable: true
            },
            toolbox: {
                show: true,
                orient: 'vertical',
                x: 'right',
                y: 'center',
                feature: {
                    mark: {show: true},
                    dataView: {show: true, readOnly: false},
                    restore: {show: true},
                    saveAsImage: {show: true}
                }
            },
            roamController: {
                show: true,
                x: 'right',
                mapTypeControl: {
                    'china': true
                }
            },
            series: [
                {
                    type: 'map',
                    mapType: 'china',
                    roam: false,
                    itemStyle: {
                        normal: {label: {show: true}},
                        emphasis: {label: {show: true}}
                    },
                    data:{!! $recent_most_province_visits !!}
                }
            ]
        };

        require.config({
            paths: {
                echarts: '/assets/libs/echarts/2.2.7'
            }
        });

        require(
            [
                'echarts',
                'echarts/chart/bar',
                'echarts/chart/pie',
                'echarts/chart/map'
            ],
            requireCallback
        );
    </script>
@stop
