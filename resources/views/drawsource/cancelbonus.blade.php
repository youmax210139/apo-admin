@extends('layouts.base')

@section('title','开奖管理')

@section('function','开奖管理')
@section('function_link', '/cancelbonus/')

@section('here','系统撤单')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">

        </div>
    </div>
    @include('partials.errors')
    @include('partials.success')
    <div class="row page-title-row" style="margin:5px;">
        <div class="box box-primary">
            <div class="box-body">
                <form class="form-horizontal" method="post" onsubmit="return checkForm();">
                    <div class="box-body">
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-warning"></i> 注意!</h4>
                            请谨慎使用系统撤单功能，提交前请确认彩种，奖期，撤单原因是否正确！系统撤单时间为理时间限制在 1200 分钟内！
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" value="1" name="type" id="type_1">
                                        提前开奖
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" value="2" name="type" id="type_2">
                                        开错号码
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" value="3" name="type" id="type_3">
                                        官方未开奖
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group lottery_action" style="display: none">
                            <label for="inputEmail3" class="col-sm-2 control-label">彩种</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="lottery_id" id="lottery_id">
                                    <option value="">选择彩种</option>
                                    @foreach($lotteries as $k=>$L)
                                        <optgroup label="{{$k}}">
                                        @foreach($L as $item)
                                            <option value="{{$item->id}}">{{$item->name}} 【{{ $item->ident }}】</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group lottery_action" style="display: none">
                            <label for="inputEmail3" id="issue_label" class="col-sm-2 control-label">选奖期</label>
                            <div class="col-sm-10">
                                <select style="font-size: 16px;font-weight: bold" class="form-control" id="issue" name="issue[]">


                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="show_1" style="display: none">
                            <label for="inputEmail3" class="col-sm-2 control-label">官方实际开奖时间</label>
                            <div class="col-sm-10">
                                <input type="input" class="form-control" name="start_time" id="start_time"
                                       placeholder="">
                            </div>
                        </div>
                        <div class="form-group" id="show_2" style="display: none">
                            <label for="inputEmail3" class="col-sm-2 control-label">正确的开奖号码</label>
                            <div class="col-sm-10">
                                <input type="input" class="form-control" name="issue_code" id="issue_code"
                                       placeholder="">
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                    <!-- /.box-footer -->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                </form>

            </div>
        </div>

    </div>
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_time',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
        };
        laydate(layConfig);
        $(function () {
            $("#type_1").click(function () {
                $("#show_1").show();
                $("#show_2").hide();
                $("#issue").css('height','auto').attr('multiple',false);
                $("#issue_label").html("选奖期");
                $(".lottery_action").show()
            });
            $("#type_2").click(function () {
                $("#show_2").show();
                $("#show_1").hide();
                $("#issue").css('height','auto').attr('multiple',false);
                $("#issue_label").html("选奖期");
                $(".lottery_action").show();
            });
            $("#type_3").click(function () {
                $("#show_2").hide();
                $("#show_1").hide();
                $("#issue").css('height','400px').attr('multiple',true);
                $("#issue_label").html("选奖期（按住ctrl建多选）");
                $(".lottery_action").show();
            });
            $("#lottery_id").change(function () {
                if ($(this).val() == "") {
                    return;
                }
                var type = $(":checked[name='type']").val();
                if (type == undefined) {
                    alert("请选择处理原因！")
                    return false;
                }
                $("#issue").html('');
                $.ajax({
                    url: "/cancelbonus/index?lottery_id=" + $(this).val(),
                    dataType: "json",
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        lottery_id: $(this).val(),
                        type:type
                    }
                }).done(function (json) {
                    if (json.length == 0) {
                        return;
                    }
                    var tmp = '<option value="">请选择奖期</option>';
                    $.each(json, function (i, v) {
                        tmp += "<option value='" + v.issue + "'>" + v.issue + " (" + v.sale_start + " 至 " + v.sale_end + ")("+(v.code_status==0?'未录入':'已录入')+")" + "</option>";
                    });
                    $('#issue').html(tmp);
                })
            })
        });

        function checkForm() {
            if ($("#lottery_id").val() == '') {
                alert("请选择彩种")
                return false;
            }
            if ($("#issue").val() == '') {
                alert("请选择处理的奖期")
                return false;
            }
            var type = $(":checked[name='type']").val();
            if (type == undefined) {
                alert("请选择处理原因！")
                return false;
            }
            if (type == '1') {
                if ($("#start_time").val() == '') {
                    alert("请输入实际开奖时间！")
                    return false;
                }
            }
            if (type == '2') {
                if ($("#issue_code").val() == '') {
                    alert("请输入正确的开奖号码！")
                    return false;
                }
            }
            if(!confirm("确认提交开奖异常处理？")){
                return false;
            }
            return true;
        }
    </script>
@stop
