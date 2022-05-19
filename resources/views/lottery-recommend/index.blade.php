@extends('layouts.base')

@section('title','推荐彩种管理')

@section('function','推荐彩种管理')
@section('function_link', '/lotteryrecommend/')

@section('here','推荐彩种列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="row">
        <div class="row page-title-row" id="dangqian" style="margin:5px;">
            <div class="col-md-6"></div>
            <div class="col-md-6 text-right">
                <a href="/lotteryrecommend/create" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加彩种 </a>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">推荐彩种管理</h3>
                </div>
                <div class="panel-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <form class="form-horizontal" id="defaultForm" role="form" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                            <thead>
                            <tr>
                                <th>编号</th>
                                <th data-sortable="false">彩种</th>
                                @foreach(range(1,4) as $idx)
                                    <th>推荐{{$idx}}</th>
                                @endforeach
                                <th data-sortable="false">提示文案</th>
                                <th data-sortable="false">状态</th>
                                <th data-sortable="false">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($lottery_recommend as $v)
                                <tr>
                                    <td>{{$v->id}}</td>
                                    <td>{{$v->name}}</td>
                                    @foreach(range(1,4) as $idx)
                                        @if($v->{'lottery_ident'.$idx})
                                            <td>{{ $v->{'lottery_ident'.$idx} . '|' . $v->{'lottery_ident_name'.$idx} }}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                    <td>{{$v->tip}}</td>
                                    <td>
                                        @if($v->status == 0)
                                            <span class="label label-danger">禁用</span>
                                        @else
                                            <span class="label label-success">启用</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="/lotteryrecommend/edit?id={{$v->id}}" class="X-Small btn-xs text-primary">编辑</a>
                                        @if($v->status == 0)
                                            <a href="javascript:;" attr="{{$v->id}}" data="1" class="X-Small btn-xs text-success status"> <span class="text-success">启用</span></a>
                                        @else
                                            <a href="javascript:;" attr="{{$v->id}}" data="0" class="X-Small btn-xs text-danger status"><span class="text-danger">禁用</span></a>
                                        @endif
                                        <a href="javascript:;" attr="{{$v->id}}" class="X-Small btn-xs text-danger del">删除</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <th scope="row" colspan="8" class="text-center">空数据</th>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                    </form>
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
                        确认要删除该推荐彩种吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-status" tabIndex="-1">
        <div class="modal-dialog modal-primary">
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
                        确认要<span class="row_verify_text"></span>该推荐彩种吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="verifyForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="status" value="0">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check-circle-o"></i> 确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        $(function () {
            $("table").delegate('.del', 'click', function () {
                var id = $(this).attr('attr');
                $('.deleteForm').attr('action', '/lotteryrecommend/?id=' + id);
                $("#modal-delete").modal();
            });
            $("table").delegate('.status', 'click', function () {
                var id = $(this).attr('attr');
                $("#modal-status .row_verify_text").html($(this).text());
                $('.verifyForm input[name="status"]').val($(this).attr('data'));
                $('.verifyForm').attr('action', '/lotteryrecommend/status/?id=' + id);
                $("#modal-status").modal();
            });
        });
    </script>
@stop
