@extends('layouts.base')
@section('title','管理员中心')
@section('function','管理员中心')
@section('here','谷歌验证器')
@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">谷歌验证器</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            @if(auth()->user()->google_key)
                                <form class="form-horizontal" role="form" method="POST" action="/profile/googlekey" id='defaultForm'>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="flag" value="unbind">
                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">动态验证码</label>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="code" id="code" value="" maxlength="20" placeholder="你已经绑定，解绑请输入动态验证码" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-7 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary btn-md">
                                                <i class="fa fa-plus-circle"></i>
                                                解除绑定
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <form class="form-horizontal" role="form" method="POST" action="/profile/googlekey" id='defaultForm'>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="flag" value="bind">
                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">扫描二维码</label>
                                        <div class="col-md-5">
                                            <img src="" id="googlekey">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="tag" class="col-md-3 control-label">动态验证码</label>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="code" id="code" value="" maxlength="20" placeholder="请打开谷歌登录验证器扫描二维码，获取动态码" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-7 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary btn-md">
                                                <i class="fa fa-plus-circle"></i>
                                                立即绑定
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/jr-qrcode.js" charset="UTF-8"></script>
    <script>
        $(document).ready(function () {
            var imgBase64 = jrQrcode.getQrBase64('{{$google_key}}');
            $("#googlekey").attr('src', imgBase64)
        });
    </script>
@stop
