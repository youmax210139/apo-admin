@extends('layouts.base')
@section('title','留言管理')
@section('function','留言管理')
@section('function_link', '/guestbook/')
@section('here','留言详情')
@section('content')
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                <tr>
                                    <td>信箱</td>
                                    <td>{{$email}}</td>
                                    <td>称呼</td>
                                    <td>{{$appellation}}</td>
                                </tr>
                                <tr>
                                    <td>通讯软件</td>
                                    <td>{{$app_name}}</td>
                                    <td>通讯软件帐号</td>
                                    <td>{{$app_account}}</td>
                                </tr>
                                <tr>
                                    <td>主题</td>
                                    <td>{{$title}}</td>
                                    <td>状态</td>
                                    <td>{{$status}}</td>
                                </tr>
                                <tr>
                                    <td>留言</td>
                                    <td>{{$content}}</td>
                                    <td>备注</td>
                                    <td>{{$remark}}</td>
                                </tr>
                                <tr>
                                    <td>银行卡姓名</td>
                                    <td>{{$account_name}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer text-center">
                            <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
                                <i class="fa fa-arrow-left"></i> 返回
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
