@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','用户密保')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">用户密保</h3>
                        </div>
                        <div class="panel-body">
                        @include('partials.errors')
                        @include('partials.success')
                        <!-- form start -->
                            <form id='defaultForm' class="form-horizontal" role="form" method="POST">
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">用户名</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left">{{$user->username}}</p>
                                    </div>
                                </div>


                               @if(count($user->security_question)>0)
                               @foreach($user->security_question as $v)
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">问题:*******</label>
                                    <div class="col-md-6 control-label">
                                        <p class="text-left">答案:*******</p>
                                    </div>
                                </div>
                                @endforeach
                             @else
                                <div class="form-group margin">
                                    <div class="col-md-7 col-md-offset-3">
                                        <p colspan="2" class="text-danger">用户没有设置密保！</p>
                                    </div>
                                </div>

                             @endif

                                <div class="form-group margin">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="button" class="btn btn-warning btn-md col-sm-2" onclick="history.back()"><i class="fa fa-times"></i> 取消</button>
                                        @if(count($user->security_question))
                                        <button type="submit" class="btn btn-primary btn-md col-sm-2 col-md-offset-1"><i class="fa fa-check"></i>清空密保</button>
                                        @endif
                                    </div>
                                </div>

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" value="{{$user->id}}">
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
$(document).ready(function() {
    $('#defaultForm')
        .bootstrapValidator({
            message: '该数据不可用',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            }
        }).on('success.form.bv', function(e) {
        	if (confirm("确认执行操作？")) {
        		return true;
        	}
        	e.preventDefault();
        });
});
</script>
@stop