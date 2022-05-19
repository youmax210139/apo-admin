@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','用户返点返水')

@section('content')
<link rel="stylesheet" href="/assets/plugins/bootstrap-slider/bootstrap-slider.min.css">
<style>
    .slider-selection {
        background: #3c8dbc;
    }
</style>
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">添加下级</h3>
                    </div>
                    <div class="panel-body form-horizontal">
                        @include('partials.errors')
                        @include('partials.success')
                        <form role="form" class="form-horizontal" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="POST">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">上级</label>
                                <div class="col-md-6 control-label">
                                    <p class="text-left">{{$user->username}} @if($user->parent_id)[代理] @else [总代] @endif</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">奖金组</label>
                                <div class="col-md-6 control-label">
                                    <p class="text-left">
                                        {{$top_user_level}}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label checkbox-inline text-bold">用户类型</label>
                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="user_type" checked="" value="2">
                                        代理
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="user_type" value="3">
                                        会员
                                    </label>
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="tag" class="col-md-3 control-label">用户名</label>
                                <div class="col-md-5">
                                    <textarea name="username" class="form-control" placeholder="用户名多个用,号隔开" ></textarea>
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="tag" class="col-md-3 control-label">密 码</label>
                                <div class="col-md-5">
                                    <input style="max-width: 180px" value="" type="text" name="password" class="form-control" placeholder="密 码" >
                                </div>
                            </div>
                                @foreach($parent_rebates as $k=>$row)
                                    <div class="form-group margin">
                                        <div class="col-md-10 col-md-offset-1" style="padding-top:17px;">
                                            <label class="col-md-2 control-label">
                                                {{$row['name']}} [
                                                @if(isset($rebates[$k]))
                                                    {{$rebates[$k]*100}}
                                                @else 0 @endif
                                                ]
                                            </label>
                                            <div class="col-md-8" style="padding-top: 5px;">
                                                <b>0</b>
                                                <input id="{{$k}}" class="exSlider"
                                                       type="text" data-slider-min="0"
                                                       data-slider-max="{{$row['limit']}}"
                                                       data-slider-step="@if($k=='lottery') {{ $operation_lottery_rebate_min_scale }} @else {{ $operation_third_rebate_min_scale }} @endif"
                                                       data-slider-value="0"/>
                                                <b style="margin-left: 15px;">
                                                    {{$row['limit']}}%
                                                </b>
                                                <input data-min="0" onkeyup="onlyNumber(this,0,{{$row['limit']}})"
                                                       data-max="{{$row['limit']}}" class="rebate-input" data="{{$k}}" name="rebates[{{$k}}][value]" type="text" value="0" style="width: 60px">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            <div class="form-group margin">
                                <div class="col-md-7 col-md-offset-3">
                                    <button type="button" class="btn btn-warning btn-md" onclick="location.href='/user/';">
                                        <i class="fa fa-minus-circle"></i>
                                        取消
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-md">
                                        <i class="fa fa-plus-circle"></i>
                                        保存
                                    </button>
                                    <input type="text" style="width:60px;margin-left:88px;" onkeyup="modifyAll(this,0)">
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
<script src="/assets/plugins/bootstrap-slider/bootstrap-slider.min.js"></script>
<script>
    $(document).ready(function () {
        @foreach($parent_rebates as $k=>$row)
        $('#{{$k}}').slider({
            formatter: function (value) {
                return '当前返点: ' + value;
            }
        });
        $('#{{$k}}').on("change", function(slideEvt) {
           $("input[data='{{$k}}']").val(slideEvt.value.newValue);
        });

        @endforeach
        // $(".rebate-input").keyup(function () {
        //     $('#'+$(this).attr('data')).slider( "option", "value", $(this).val() );
        //
        // });
    });
    function onlyNumber(obj,min,max) {
        //先把非数字的都替换掉，除了数字和.
        obj.value = obj.value.replace(/[^\d\.]/g, '');
        //必须保证第一个为数字而不是.
        obj.value = obj.value.replace(/^\./g, '');
        //保证只有出现一个.而没有多个.
        obj.value = obj.value.replace(/\.{2,}/g, '.');
        //保证.只出现一次，而不能出现两次以上
        obj.value = obj.value.replace('.', '$#$').replace(/\./g, '').replace(
            '$#$', '.');
        if(obj.value==''){
            obj.value = 0;
        }
        if(obj.value<min){
            obj.value = min;
        }
        if(obj.value>max){
            obj.value = max;
        }
       $('#'+$(obj).attr('data')).slider( 'setValue',obj.value);
    }

    function modifyAll(obj, min) {
        //先把非数字的都替换掉，除了数字和.
        obj.value = obj.value.replace(/[^\d\.]/g, '');
        //必须保证第一个为数字而不是.
        obj.value = obj.value.replace(/^\./g, '');
        //保证.只出现一次，而不能出现两次以上
        obj.value = obj.value.replace('.', '$#$').replace(/\./g, '').replace('$#$', '.');
        if (obj.value == '') {
            obj.value = 0;
        }
        if (obj.value < min) {
            obj.value = min;
        }

        $(".rebate-input").each(function () {
            $(this).val(obj.value);
            $(this).trigger('onkeyup');
        });
    }
</script>
@stop
