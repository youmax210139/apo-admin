@extends('layouts.base')
@section('title','玩法奖金')
@section('function','玩法奖金')
@section('function_link', '/lottery/')
@section('here','玩法奖金')
@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
            {{$lottery->name}}
        </div>
        <div class="col-md-6 text-right">

        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false" style="width: 15%" class="hidden-sm">玩法ID</th>
                            <th style="width: 15%">玩法名称</th>
                            <th>原奖金</th>
                            <th>调整后奖金</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lists as $key=>$item)
                            <tr role="row" class="{{$key%2==0?'odd':'even'}}">
                                <td class="sorting_1">{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                @php
                                    $prize_level_name = json_decode($item->prize_level_name,true);
                                    $prize_level = json_decode($item->prize_level,true);
                                @endphp
                                <td>
                                    @foreach($prize_level_name as $k=>$name)
                                        <p>{{$name}} : <b class="text-success">{{$prize_level[$k]}}</b></p>
                                    @endforeach
                                </td>

                                <td>
                                    @if($item->prize_level_fixed)
                                        @php
                                            $prize_level_fixed = json_decode($item->prize_level_fixed,true);
                                        @endphp
                                        @foreach($prize_level_name as $k=>$name)
                                            <p>{{$name}} : <b class="text-danger">{{$prize_level_fixed[$k]}}</b></p>
                                        @endforeach
                                    @else
                                        未设置
                                    @endif
                                </td>
                                <td>
                                    <a method_id="{{$item->id}}" data_method="{{$item->name}}" data_prize_level_fixed="{{$item->prize_level_fixed}}" data_prize_name="{{$item->prize_level_name}}" class="X-Small btn-xs text-success setBtn" href="javascript:;"><i class="fa fa-edit"></i>设置</a>
                                    <a class="X-Small btn-xs text-danger delBtn" attr="2" href="javascript:;" method_id="{{$item->id}}"><i class="fa fa-times-circle"></i>清除设置</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-delete" tabIndex="-1">
        <div class="modal-dialog modal-danger">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        <i class="fa fa-question-circle fa-lg"></i>
                        确认要删除该设置吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST" action="/lottery/delprizelevel">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="post">
                        <input type="hidden" name="method_id" value="">
                        <input type="hidden" name="lottery_id" value="{{$lottery->id}}">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade modal-primary" id="modal-set" tabIndex="-1">
        <div class="modal-dialog modal-default modal-lg">
            <div class="modal-content">
                <form class="form-horizontal" role="form" id="edit-form" method="POST" action="/lottery/prizelevel">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="method_id" value="">
                    <input type="hidden" name="lottery_id" value="{{$lottery->id}}">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            ×
                        </button>
                        <h4 class="modal-title">设置奖金</h4>
                    </div>
                    <div class="modal-body ">
                        <div class="row">

                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">玩法名称</label>
                                <div class="col-md-5 control-label">
                                    <p class="text-left" id="cur_method_name"></p>
                                </div>
                            </div>

                            <div id="prize_level">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-center">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-outline" id="add-limit-submit">立即设置</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        $(function () {
            $(".setBtn").click(function () {
                $("#edit-form input[name='method_id']").val($(this).attr('method_id'));
                $("#edit-form #cur_method_name").html($(this).attr('data_method'));
                eval("var prize_name = " + ($(this).attr('data_prize_name')));

                if ($(this).attr('data_prize_level_fixed') != '') {
                    eval("var prize_fixed = " + ($(this).attr('data_prize_level_fixed')));
                } else {
                    var prize_fixed = [];
                }
                var html = '';
                for (var i = 0; i < prize_name.length; i++) {
                    html += ' <div class="form-group">\n' +
                        '                            <label for="tag" class="col-md-3 control-label">' + prize_name[i] + '</label>\n' +
                        '                            <div class="col-md-5">\n' +
                        '                                <input type="text" class="form-control" name="prize_level[]" value="' + (prize_fixed[i] ? prize_fixed[i] : '') + '">\n' +
                        '                            </div>\n' +
                        '                        </div>';
                }
                $("#edit-form #prize_level").html(html);
                $("#modal-set").modal();
            });
            $(".delBtn").click(function () {
                $("#modal-delete input[name='method_id']").val($(this).attr('method_id'));
                $("#modal-delete").modal();
            });
        });
    </script>
@stop
