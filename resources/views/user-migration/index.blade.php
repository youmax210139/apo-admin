@extends('layouts.base')

@section('title','用户转移')

@section('function','用户转移')
@section('function_link', '/usermigration/')

@section('here','用户转移')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">用户转移</h3>
                        </div>
                        <div class="panel-body">

                            @include('partials.errors')
                            @include('partials.success')

                            <form class="form-horizontal" role="form" method="POST" action="/usermigration/migrate" onsubmit="return checkForm()">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <label for="username" class="col-md-3 control-label">转移用户名：</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="username" id="username" value="" maxlength="20" placeholder="将转移该用户及其所有下级" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="new_parent" class="col-md-3 control-label">新父级用户名：</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="new_parent" id="new_parent" value="" maxlength="20" placeholder="新父级用户名" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="reset" class="btn btn-warning btn-md">
                                            <i class="fa fa-minus-circle"></i>
                                            取消
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            执行
                                        </button>
                                    </div>
                                </div>

                            </form>
                            <div>
                                <p>转移条件：</p>
                                <ul>
                                    <li>被转移不能是测试、试玩用户。</li>
                                    <li>被转移用户不能是总代。</li>
                                    <li>如果被转移用户的基础奖金（1700、1800、1900）和新父级用户不一样，将自动改成新父级用户的基础奖级，彩票返点也将自动修改成对应的返点。</li>
                                    <li>被转移用户的彩票、PT、AG、SB、QP、BBIN等第三方返点不能大于新父级用户的。</li>
                                    <li>新父级用户的用户类型必须是代理，不能是会员。</li>
                                </ul>
                                <p>注意事项：</p>
                                <ul>
                                    <li><strong>提交执行后，请耐心等待浏览器返回结果。请勿中途关闭浏览器，以免数据执行不完整。</strong></li>
                                    <li>转移之后，旧数据会显示在新上级代理的报表上。如果可以，请在每月计算好分红后再执行转移用户。
                                    <li>由于旧数据在新上级代理的报表，所以会增加新上级的日工资、佣金的金额。</li>
                                </ul>
                            </div>
                            <div style="text-align: right;">
                                <a href="/usermigration/records" class="btn btn-primary btn-md">
                                    <i class="fa fa-search"></i> 查看转移记录
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function checkForm() {
            var username = $.trim($("#username").val());
            var new_parent = $.trim($("#new_parent").val());
            if(username.length < 6) {
                alert("转移用户名 不能小于6个字符");
                return false;
            }
            if(new_parent.length < 6) {
                alert("新父级用户名 不能小于6个字符");
                return false;
            }
            return true;
        }
    </script>
@stop