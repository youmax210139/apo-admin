@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','发送站内信')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">发送站内信</h3>
                    </div>
                    <div class="panel-body">
                        @include('partials.errors')
                        @include('partials.success')
                        <form class="form-horizontal" id="defaultForm" role="form" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                             <input type="hidden" name="_method" value="PUT">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">接收人</label>
                                <div class="col-md-6">
                                    <p class="help-block">{{ $user->username }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">接收范围</label>
                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="send_type" checked="" value="0">
                                        仅本人
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="send_type"  value="1">
                                        本人所有下级
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="send_type"  value="2">
                                        本人直属下级
                                    </label>
                                    @if($user->parent_id)
                                    <label class="radio-inline">
                                        <input type="radio" name="send_type"  value="3">
                                        本人所有上级
                                    </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">消息标题</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="subject" value="" placeholder="不能超过100个字" maxlength="100">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">消息内容</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" placeholder="不能超过200个字" rows="3" name='content'></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-7 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary btn-md">
                                        <i class="fa fa-plus-circle"></i>
                                        立即发送
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-warning"></i> 注意!</h4>
                            <ol>
                                <li>仅本人接收：只发送给接收人自己</li>
                                <li>本人所有下级：包含接收人、接收人的所有下级代理以及普通会员，也包含下级代理的所有下级代理以及普通会员</li>
                                <li>本人直接下级：包含接收人、接收人的所有下级代理以及普通会员</li>
                                <li>本人所有上级：包含接收人、接收人的直接上级和所有直接上级的上级</li>
<!--                                <li>发送所有会员：发送全站所有会员</li>-->
                            </ol>
                        </div>
                    </div>
                    <!-- /.box-body -->


                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    $(document).ready(function () {
        $('#defaultForm')
                .bootstrapValidator({
                    message: '该数据不可用',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        send_type: {
                            validators: {
                                notEmpty: {message: '请选择发送类型！'},    //非空提示
                            }
                        },
                        subject: {
                            validators: {
                                notEmpty: {message: '请输入标题'},    //非空提示
                                stringLength: {    //长度限制
                                      min: 2,
                                      max: 100,
                                      message: '标题长度必须在2到100之间'
                                }
                            }
                        },
                        content: {
                            validators: {
                                notEmpty: {message: '请输入消息内容'},    //非空提示
                                stringLength: {    //长度限制
                                      min: 2,
                                      max: 200,
                                      message: '内容长度必须在2到200之间'
                                }
                            }
                        }
                    }
                }).on('success.form.bv', function (e) {
           
        });
    });
</script>
@stop