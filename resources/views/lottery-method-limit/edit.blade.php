@extends('layouts.base')

@section('title','投注限制')

@section('function','投注限制')
@section('function_link', '/lotterymethodlimit/')

@section('here','编辑限制')

{{--@section('pageDesc','DashBoard')--}}
@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">编辑限制</h3>
                        </div>
                        <div class="panel-body">

                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" role="form" method="POST" action="/lotterymethodlimit/edit">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="id" value="{{ $id }}">

                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">玩法类型</label>
                                    <div class="col-md-5 control">
                                        {{$lottery_method_category_name}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">玩法名称</label>
                                    <div class="col-md-5 control">
                                        {{$method_name}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">受限彩种</label>
                                    <div class="col-md-5">
                                        {{$lottery_name}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">单注最低投注</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="project_min" id="tag" value="{{ $project_min }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">单注最高投注</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="project_max" id="tag" value="{{ $project_max }}"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">单项最高投注</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="issue_max" id="tag" value="{{ $issue_max }}"  />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            保存
                                        </button>

                                        <button type="button" class="btn btn-warning btn-md" onclick="javascript:window.history.back()">
                                            <i class="fa fa-reply-all"></i>
                                            返回
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