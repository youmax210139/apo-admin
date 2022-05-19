@extends('layouts.base')

@section('title','彩种管理')

@section('function','彩种管理')
@section('function_link', '/lottery/index?'.$lottery->id)

@section('here','禁用玩法')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">禁用玩法</h3>
                        </div>
                        <div class="panel-body">
                            @include('partials.errors')
                            @include('partials.success')
                            <form class="form-horizontal" id="issue_form" role="form" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="lottery_id" value="{{ $lottery->id }}">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">彩种</label>
                                    <div class="col-md-7 form-control-static">
                                        {{ $lottery->name }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label">玩法</label>
                                    <div class="col-md-7 form-control-static">
                                        @foreach ( $lottery_methods as $method )
                                            <div class="checkbox-inline">
                                                <label {{in_array( $method['ident'],$lottery->deny_method_ident)?'style=color:red':''}} >
                                                    <input class="level_0" {{in_array( $method['ident'],$lottery->deny_method_ident)?'checked':''}} name="deny_methods[]"
                                                           type="checkbox" value="{{ $method['ident'] }}">
                                                    {{ $method['name'] }}
                                                </label>
                                                @if(!empty($method['child']))
                                                    <div class="container-fluid">
                                                        @foreach ( $method['child'] as $method1 )
                                                            <div class="checkbox-inline">
                                                                <label {{in_array( $method1['ident'],$lottery->deny_method_ident)?'style=color:red':''}} >
                                                                    <input class="level_1" {{in_array( $method1['ident'],$lottery->deny_method_ident)?'checked':''}} name="deny_methods[]"
                                                                           type="checkbox" value="{{ $method1['ident'] }}">
                                                                    {{ $method1['name'] }}
                                                                </label>
                                                                @if(!empty($method1['child']))
                                                                    <div class="container-fluid">
                                                                        @foreach ( $method1['child'] as $method2 )
                                                                            <div class="checkbox-inline">
                                                                                <label {{in_array( $method2['ident'],$lottery->deny_method_ident)?'style=color:red':''}} >
                                                                                    <input class="level_2" {{in_array( $method2['ident'],$lottery->deny_method_ident)?'checked':''}} name="deny_methods[]"
                                                                                           type="checkbox" value="{{ $method2['ident'] }}">
                                                                                    {{ $method2['name'] }}
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>

                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-2">
                                        <button type="submit" class="btn btn-primary btn-md" id="confirm_button">
                                            <i class="fa fa-plus-circle"></i>
                                            确定
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
        $(function () { 
            $(".level_0,.level_1").click(function () {
                $(this).parent().parent().find('div input').prop('checked',$(this).prop('checked'));
            });

            $(".level_2").click(function () {
                if($(this).parent().parent().parent().find('.checkbox-inline input').size() ==
                    $(this).parent().parent().parent().find('.checkbox-inline input:checked').size()){
                    $(this).parent().parent().parent().parent().find(".level_1").prop('checked',true);
                }else{
                    $(this).parent().parent().parent().parent().find(".level_1").prop('checked',false);
                }
            });
            $(".level_1").change(function () {
                if($(this).parent().parent().parent().parent().find('.level_1').size()
                == $(this).parent().parent().parent().parent().find("input:checked[class='level_1']").size()){
                    $(this).parent().parent().parent().parent().find('.level_0').prop('checked',true);
                }
            });
        });
    </script>
@stop