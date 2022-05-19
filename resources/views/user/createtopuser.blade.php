@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','总代开户')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">总代开户</h3>
                    </div>
                    <div class="panel-body">
                        @include('partials.errors')
                        @include('partials.success')
                        <form role="form" class="form-horizontal" method="POST" id="defaultForm">
                             <input type="hidden" name="_token" value="{{ csrf_token() }}">
                             <input type="hidden" name="_method" value="PUT">
                            <div class="form-group">
                                <label for="username" class="col-md-3 control-label">用户名</label>
                                <div class="col-md-5">
                                    <input  type="text" name="username" class="form-control" placeholder="用户名长度必须在 6 到 20 之间">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-md-3 control-label">登录密码</label>
                                <div class="col-md-5">
                                    <input  type="password" name="password" class="form-control" placeholder="请输入密码">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="usernick" class="col-md-3 control-label">用户昵称</label>
                                <div class="col-md-5">
                                    <input type="text" name="usernick" class="form-control" placeholder="用户昵称长度必须在 6 到 20 之间">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">用户组</label>
                                <div class="col-md-5">
                                    <select name="user_group" class="form-control">
                                        <option value="">请选择用户组</option>
                                        @foreach($user_group as $g)
                                        <option value="{{$g->id}}">{{$g->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-3 control-label checkbox-inline text-bold">允许玩法类型</label>--}}
                                {{--<div class="col-md-5">--}}
                                    {{--<select name="play_mode" class="form-control">--}}
                                        {{--<option value="">请选择用户玩法类型</option>--}}
                                        {{--<option value="0">标准</option>--}}
                                        {{--<option value="1">盘口</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            <div class="form-group">
                                <label class="col-md-3 control-label">奖金级别</label>
                                <div class="col-md-5">
                                    <select name="user_prize_level" class="form-control" id="user_prize_level">
                                        <option value="">请选择奖金级别</option>
                                        @for($i=2000; $i >= 1600; $i-=10)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            @foreach($rebates as $v)
                            <div class="form-group">
                                <label class="col-md-3 control-label">{{$v['name']}}返点</label>
                                <div class="col-md-5">
                                    <select id="rebates_{{$v['ident']}}" name="rebates[{{$v['ident']}}]" type="{{ $v['ident'] }}" class="form-control rebates_select">
                                    </select>
                                </div>
                            </div>
                            @endforeach

                            <div class="form-group margin">
                                <div class="col-md-7 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary btn-md col-sm-2">确定开户</button>
                                    <button type="button" class="btn btn-default btn-md col-sm-2 col-md-offset-1" onclick="history.back()">取消</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    $(document).ready(function () {
        var rebates = {!! json_encode($rebates) !!};
        $('#user_prize_level').bind('change',function(){
            var level = parseInt($(this).val());
            $('.rebates_select').each(function(i){
                var type = $(this).attr('type');
                if(level!=''){
                    var options = '';
                    var tmp = tmp_txt = '';
                    var limit = {!! $third_game_rebate_limit * 1000 !!};
                    if(type == 'lottery'){
                        limit = (2000 - level) / 1000 * 1000;
                    }
                    for (var i = limit; i >= 0; i = i - 1) {
                        tmp = (i/10).toFixed(1) ;
                        tmp_txt = tmp;
                        if(type == 'lottery'){
                            tmp = (i/20).toFixed(2) ;
                            tmp_txt = parseInt(level) + (i / 1000 * 1000);
                            tmp_txt = tmp_txt + ' - ' + tmp;
                        }
                        options = options + '<option value="' + tmp + '">' + tmp_txt + ' %</option>';
                    }
                    $('#rebates_'+type).html(options);
                }
            });
        });
        $('#defaultForm')
                .bootstrapValidator({
                    message: '该数据不可用',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        username: {
                            validators: {
                                notEmpty: {
                                    message: '请输入用户名!'
                                },
                                stringLength: {    //长度限制
                                    min: 6,
                                    max: 30,
                                    message: '用户名长度必须在6到20之间'
                                },
                                regexp: {//匹配规则
                                      regexp: /^[a-za-z][a-z_\d]*$/i,  //正则表达式
                                      message:'用户名仅字母开头支持字母、数字、下划线的组合'
                                }
                        }
                    },
                    password: {
                            validators: {
                               notEmpty: {message: '请输入密码'},
                               different: {  //比较
                                    field: 'username', //需要进行比较的input name值
                                    message: '密码不能与用户名相同'
                               }
                            }
                    },
//                    play_mode: {
//                        validators: {
//                            notEmpty: {message: '请选择用户玩法类型'}
//                        }
//                    },
                    usernick: {
                        validators: {
                           notEmpty: {message: '请输入用户昵称'}
                        }
                    },
                     user_group: {
                        validators: {
                           notEmpty: {message: '请选择用户组'}
                        }
                    },
                    user_prize_level: {
                        validators: {
                            notEmpty: {message: '请选择奖金级别'}
                        }
                    }
                    }
                }).on('success.form.bv', function (e) {
                    return true;
           e.preventDefault();
        });
    });
</script>
@stop