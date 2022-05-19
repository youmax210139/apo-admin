@extends('layouts.base')
@section('title','短信通道管理')
@section('function','短信通道管理')
@section('function_link', '/smschannel/')
@section('here','发送短信')
@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">发送短信</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" onsubmit="return checkForm" role="form" method="POST" action="/smschannel/send">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="id" value="{{ $id }}">
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">渠道名称</label>
                                    <div class="col-md-5">
                                        {{$channel->name}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">通道名称</label>
                                    <div class="col-md-5">
                                        {{$channel->cate_name}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">手机号码</label>
                                    <div class="col-md-5">
                                        <input required type="text" class="form-control" placeholder="请输入11位手机号码" name="phone" id="phone" value="" maxlength="11"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">短信内容</label>
                                    <div class="col-md-5">
                                        <textarea id="message" name="message" class="form-control" rows="3" placeholder="腾讯云的短信，需要备案短信内容了才能发送"></textarea>
                                        <span>已输入字数：<span id="message_len">0</span>，还可以输入<span id="message_remain">0</span>个字</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                      <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
                                           <i class="fa fa-minus-circle"></i>
                                           取消
                                       </button>
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            发送
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

@section('js')
    <script>
        function checkText() {
            var max_len = 60;
            var input_len = $("#message").val().length;
            $("#message_len").text(input_len);
            $("#message_remain").text(max_len - input_len);
        }

        function checkForm() {
            var phone = $("#phone").val();
            var message = $("#message").val();
            if(isNaN(phone) || phone.length != 11) {
                alert("请输入正确的手机号");
                return false;
            }
            if(message.length > 60) {
                alert("短信内容不能超过60个字");
                return false;
            }
            return true;
        }

        $(function () {
            $("#message").bind('change keyup', function () {
                checkText();
            })
            checkText();
        })
    </script>
@stop
