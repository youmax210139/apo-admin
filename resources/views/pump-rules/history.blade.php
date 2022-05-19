@extends('layouts.base')
@section('title','变更历史【'.$self->username.'】')
@section('function','抽返水规则')
@section('function_link', '/pumprule/')
@section('here','变更历史【'.$self->username.'】')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <ol class="breadcrumb" id="parent_tree" style="margin-bottom: 10px;font-weight: bold;padding: 5px">
                        @foreach($parent_tree as $parent)
                        <li> <a href="/pumprule/history?user_id={{$parent->user_id}}" class="X-Small btn-xs text-maroon">{{$parent->username}}</a></li>
                        @endforeach
                    </ol>
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct no-footer">
                        <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th width="150">创建时间</th>
                            <th width="120">创建者</th>
                            <th width="150">删除时间</th>
                            <th width="120">删除者</th>
                            <th width="100">操作位置</th>
                            <th>规则内容</th>
                            <th width="100">使用状态</th>
                            <th width="100">删除状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($pump_default_rule && $have_enable == 0 && $self->user_id == $self->top_id)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <span class="label label-success">默认</span>
                            </td>
                            <td>
                                <table class="table table-striped table-condensed table-bordered table-hover "
                                       style="margin-bottom: 0px">
                                    <thead>
                                    @foreach($pump_default_rule_columns as $_ck => $_cl)
                                        <th>{{$_cl}}</th>
                                    @endforeach
                                    <th width="15%">返水层级</th>
                                    <th width="15%">返水比例</th>
                                    </thead>
                                    <tbody>

                                        @foreach($pump_default_rule['inlet'] as $inlet)
                                            <tr>
                                                @foreach($pump_default_rule_columns as $_ck => $_cl)
                                                    <td>{{$inlet[$_ck]}}</td>
                                                @endforeach
                                                @if(isset($inlet['outlet']))
                                                    <td colspan="2">
                                                        <table class="table table-striped table-condensed mb-0"
                                                               style="margin-bottom: 0px">
                                                            <tbody>
                                                            @foreach($inlet['outlet'] as $outlet)
                                                                <tr>
                                                                    <td width="50%">{{$outlet['level']}}</td>
                                                                    <td width="50%">{{$outlet['scale']}}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                @else
                                                    <td colspan="2">暂无</td>
                                                @endif
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </td>
                            <td>
                                @if(isset($pump_default_rule['enable']) && $pump_default_rule['enable'] == 1)
                                    <span class="label label-success">开启</span>
                                @else
                                    <span class="label label-warning">关闭</span>
                                @endif
                            </td>
                            <td>
                                <span class="label label-success">开启</span>
                            </td>
                        </tr>
                        @endif
                        @forelse($rules as $rule)
                            <tr>
                                <td>{{$rule->id}}</td>
                                <td>{{$rule->created_at}}</td>
                                <td>{{$rule->created_username}}</td>
                                <td>{{$rule->updated_at}}</td>
                                <td>{{$rule->updated_username}}</td>
                                <td>
                                    @if($rule->stage == 1)
                                        <span class="label label-success">前台</span>
                                    @elseif($rule->stage == 2)
                                        <span class="label label-warning">后台</span>
                                    @else
                                        <span class="label label-danger">未知[{{$rule->stage}}]</span>
                                    @endif
                                </td>
                                <td>
                                    <table class="table table-striped table-condensed table-bordered table-hover "
                                           style="margin-bottom: 0px">
                                        <thead>
                                        @foreach($rule->content_columns as $_ck => $_cl)
                                            <th>{{$_cl}}</th>
                                        @endforeach
                                        <th width="15%">返水层级</th>
                                        <th width="15%">返水比例</th>
                                        </thead>
                                        <tbody>
                                        @if($rule->conten_inlet)
                                            @foreach($rule->conten_inlet as $inlet)
                                                <tr>
                                                    @foreach($rule->content_columns as $_ck => $_cl)
                                                        <td>{{$inlet[$_ck]}}</td>
                                                    @endforeach
                                                    @if(isset($inlet['outlet']))
                                                        <td colspan="2">
                                                            <table class="table table-striped table-condensed mb-0"
                                                                   style="margin-bottom: 0px">
                                                                <tbody>
                                                                @foreach($inlet['outlet'] as $outlet)
                                                                    <tr>
                                                                        <td width="50%">{{$outlet['level']}}</td>
                                                                        <td width="50%">{{$outlet['scale']}}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    @else
                                                        <td colspan="2">暂无</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    @if(isset($rule->content['enable']) && $rule->content['enable'] == 1)
                                        <span class="label label-success">开启</span>
                                    @else
                                        <span class="label label-warning">关闭</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rule->status == 0)
                                        <span class="label label-success">正常</span>
                                    @else
                                        <span class="label label-warning">删除</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            @if($self->user_id <> $self->top_id)
                                <tr>
                                    <td colspan="9">暂无数据</td>
                                </tr>
                            @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-detail" tabIndex="-1" role="dialog">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content"></div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        $(function () {

        });
    </script>
@stop
