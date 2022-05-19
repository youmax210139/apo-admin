@extends('layouts.base')
@section('title','幸运大奖池【'.$period_info->period.'】增减金额')
@section('function','幸运大奖池')
@section('function_link', '/activity/')
@section('here','幸运大奖池【'.$period_info->period.'】增减金额')

@section('content')
    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">

        </div>

        <div class="col-md-6 text-right">
            @if(Gate::check('activity/jackpot'))
                <a href="/activity/jackpot" class="btn btn-primary btn-md"><i class="fa fa-backward"></i> 返回期号列表 </a>
            @endif
        </div>
    </div>
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
        </div>
    </div>
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">【{{ $period_info->period }}】增减金额 </h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" role="form" method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-6">
                                        开始时间：{{ $period_info->start_at }} - 结束时间：{{ $period_info->end_at }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">【{{ $period_info->period }}】增减金额</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" name="operation_prize" value="{{ $period_info->operation_prize }}" maxlength="64" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-6 text-danger">
                                        正数为加上，负数为减去
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="button" class="btn btn-warning btn-md" onclick="location.href = '/activity/jackpot';">
                                            <i class="fa fa-minus-circle"></i>
                                            取消
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            修改
                                        </button>
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
