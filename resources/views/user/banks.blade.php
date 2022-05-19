@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','银行卡信息')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    @include('partials.errors')
                    @include('partials.success')
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{$user->username}} 绑定中的银行卡</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">

                                <thead>
                                <tr>
                                    <th>开户人姓名</th>
                                    <th>银行账号</th>
                                    <th>开户银行</th>
                                    <th>省/市/区/支行</th>
                                    <th>绑定时间</th>
                                    <th>最后修改时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user_banks as $v)
                                    @if($v->status==1)
                                        <tr>
                                            <td>{{$v->account_name}}</td>
                                            <td>{{$v->account}}</td>
                                            <td>{{$v->bank_name}}</td>
                                            <td>{{$v->province}}/{{$v->city}}/{{$v->district}}/{{$v->branch}}</td>
                                            <td>{{$v->created_at}}</td>
                                            <td>{{$v->updated_at}}</td>
                                            <td>
                                                <a href="#{{$v->id}}" data="{{$v->id}}"
                                                   class="X-Small btn-xs text-primary unbundle">解绑</a>
                                                @if(Gate::check('user/editbank'))
                                                <a href="/user/editbank?id={{$v->id}}"
                                                   class="X-Small btn-xs text-primary">修改</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{$user->username}} 已取消绑定的银行卡</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">

                                <thead>
                                <tr>
                                    <th>开户人姓名</th>
                                    <th>银行账号</th>
                                    <th>开户银行</th>
                                    <th>省/市/区/支行</th>
                                    <th>绑定时间</th>
                                    <th>最后修改时间</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user_banks as $v)
                                    @if($v->status!=1)
                                        <tr>
                                            <td>{{$v->account_name}}</td>
                                            <td>{{$v->account}}</td>
                                            <td>{{$v->bank_name}}</td>
                                            <td>{{$v->province}}/{{$v->city}}/{{$v->district}}/{{$v->branch}}</td>
                                            <td>{{$v->created_at}}</td>
                                            <td>{{$v->updated_at}}</td>
                                            @if ($v->status==2)
                                                <td>已解绑</td>
                                            @else
                                                <td>已删除</td>
                                            @endif
                                            <td>
                                                <a href="#" class="X-Small btn-xs text-primary"
                                                   onclick="alert('{{$v->reason}}');">备注</a>
                                                @if ($v->status==2)
                                                    <a href="#{{$v->id}}" data="{{$v->id}}"
                                                       class="X-Small btn-xs text-primary delBank">删除</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--div class="panel-footer text-center">
                            <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
                                <i class="fa fa-arrow-left"></i> 返回
                            </button>
                        </div-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-unbundle" tabIndex="-1">
        <div class="modal-dialog modal-info">
            <div class="modal-content">
                <form class="delForm" method="POST" action="/user/unbundlebank">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            ×
                        </button>
                        <h4 class="modal-title">确认解绑</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead">
                            解绑原因：<input style="color:#444" type="text" name="reason">
                        </p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="unbundleid" value="">
                        <input type="hidden" name="virtualid" value="">
                        <input type="hidden" name="userid" value="{{$user->id}}">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa"></i> 确认
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="modal-del" tabIndex="-1">
        <div class="modal-dialog modal-danger">
            <div class="modal-content">
                <form class="delForm" method="POST" action="/user/delbank">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            ×
                        </button>
                        <h4 class="modal-title">确认删除</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead">
                            删除原因：<input style="color:#444" type="text" name="reason">
                        </p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="delid" value="">
                        <input type="hidden" name="virtualdeleteid" value="">
                        <input type="hidden" name="userid" value="{{$user->id}}">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type=" submit
                        " class="btn btn-danger">
                            <i class="fa"></i> 确认
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        $(".unbundle").click(function () {
            var id = $(this).attr('data');
            var virtual = $(this).attr('virtual');
            $("input[name='unbundleid']").val(id);
            $("input[name='virtualid']").val(virtual);
            $("#modal-unbundle").modal();
            return false;
        });
        $(".delBank").click(function () {
            var id = $(this).attr('data');
            var virtual = $(this).attr('virtual');
            $("input[name='delid']").val(id);
            $("input[name='virtualdeleteid']").val(virtual);
            $("#modal-del").modal();
            return false;
        });
    </script>
@stop
